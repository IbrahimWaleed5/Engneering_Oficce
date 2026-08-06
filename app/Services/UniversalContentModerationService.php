<?php

namespace App\Services;

use App\Models\ContentModeration;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class UniversalContentModerationService
{
    public function __construct(
        private readonly ModerationWarningService $warningService
    ) {
    }

    public function moderateText(
        User $user,
        string $text,
        string $sourceType,
        ?int $sourceId = null,
        array $context = []
    ): array {
        $text = trim($text);

        if ($text === '') {
            return $this->approvedResult();
        }

        $localResult = $this->localTextCheck(
            user: $user,
            text: $text,
            context: $context
        );

        if (
            $localResult['decision'] === 'rejected'
            && in_array(
                $localResult['risk_level'],
                ['high', 'critical'],
                true
            )
        ) {
            return $this->persistDecision(
                user: $user,
                text: $text,
                sourceType: $sourceType,
                sourceId: $sourceId,
                decision: $localResult,
                provider: 'local_rules',
                providerResponse: $localResult
            );
        }

        $aiResult = $this->askGemini(
            user: $user,
            text: $text,
            sourceType: $sourceType,
            context: $context
        );

        $finalResult = $aiResult ?? $localResult;

        if ($finalResult['decision'] === 'approved') {
            return $this->approvedResult();
        }

        return $this->persistDecision(
            user: $user,
            text: $text,
            sourceType: $sourceType,
            sourceId: $sourceId,
            decision: $finalResult,
            provider: $aiResult ? 'gemini' : 'local_rules',
            providerResponse: $aiResult ?? $localResult
        );
    }

    private function localTextCheck(
        User $user,
        string $text,
        array $context
    ): array {
        $normalized = $this->normalizeArabicText($text);

        $criticalMinorPatterns = [
            '/(?:قاصر|طفل|طفله|اطفال).{0,30}(?:جنس|عاري|عري|صور خاصه|اباحي)/u',
            '/(?:جنس|عاري|عري|اباحي).{0,30}(?:قاصر|طفل|طفله|اطفال)/u',
        ];

        if ($this->matchesAny($normalized, $criticalMinorPatterns)) {
            return $this->decision(
                decision: 'rejected',
                riskLevel: 'critical',
                categories: ['sexual_minors'],
                reason: 'تم اكتشاف محتوى جنسي متعلق بقاصر.',
                issueWarning: true,
                suspendImmediately: true
            );
        }

        $threatPatterns = [
            '/\b(?:سوف|راح|رح)?\s*(?:اقتلك|اذبحك|افجرك|اضربك|اكسرك|ادبحك)\b/u',
            '/\b(?:بقتلك|بذبحك|بفجرك|بضربك|بكسرك)\b/u',
            '/\b(?:تهديد|قتل|ذبح|تفجير)\b.{0,20}\b(?:لك|عليك|فيك)\b/u',
        ];

        if ($this->matchesAny($normalized, $threatPatterns)) {
            return $this->decision(
                decision: 'rejected',
                riskLevel: 'high',
                categories: ['threats_violence'],
                reason: 'تم اكتشاف تهديد مباشر أو محتوى عنيف.',
                issueWarning: true
            );
        }

        $explicitSexualPatterns = [
            '/\b(?:اباحي|اباحيه|جنس صريح|صور عاريه|صور عري|مقاطع جنسيه|نودز|nudes|porn)\b/ui',
            '/\b(?:نامي معي|تعال للفراش|اريد جسدك|ابعت صور خاصه)\b/u',
        ];

        if ($this->matchesAny($normalized, $explicitSexualPatterns)) {
            return $this->decision(
                decision: 'rejected',
                riskLevel: 'high',
                categories: ['explicit_sexual_content'],
                reason: 'تم اكتشاف محتوى جنسي صريح أو طلب جنسي مخالف.',
                issueWarning: true
            );
        }

        $sexualHarassmentPatterns = [
            '/\b(?:تحرش|كلام جنسي|طلب جنسي|صور خاصه|ابعتي صورتك الخاصه|ابعت صورتك الخاصه)\b/u',
        ];

        if ($this->matchesAny($normalized, $sexualHarassmentPatterns)) {
            return $this->decision(
                decision: 'rejected',
                riskLevel: 'high',
                categories: ['sexual_harassment'],
                reason: 'تم اكتشاف تحرش أو طلب ذي طابع جنسي.',
                issueWarning: true
            );
        }

        if ($this->isOffPlatformSolicitation($user, $normalized, $context)) {
            return $this->decision(
                decision: 'rejected',
                riskLevel: 'high',
                categories: ['off_platform_solicitation'],
                reason: 'محاولة نقل العميل أو الاتفاق أو الدفع خارج المنصة.',
                issueWarning: true
            );
        }

        $abusePatterns = [
            '/\b(?:حيوان|حقير|وسخ|كلب|غبي|تافه|لعنه عليك)\b/u',
        ];

        if ($this->matchesAny($normalized, $abusePatterns)) {
            return $this->decision(
                decision: 'needs_review',
                riskLevel: 'medium',
                categories: ['abuse_harassment'],
                reason: 'تم اكتشاف إساءة أو شتيمة وتحتاج إلى مراجعة.',
                issueWarning: false
            );
        }

        return $this->decision(
            decision: 'approved',
            riskLevel: 'low',
            categories: [],
            reason: 'لم يتم اكتشاف مخالفة واضحة.',
            issueWarning: false
        );
    }

    private function isOffPlatformSolicitation(
        User $user,
        string $text,
        array $context
    ): bool {
        $professionalRoles = [
            'engineer',
            'office_owner',
            'employee',
        ];

        if (
            ! in_array($user->role, $professionalRoles, true)
            && ! ($context['enforce_off_platform_for_all'] ?? false)
        ) {
            return false;
        }

        $intentPatterns = [
            '/\b(?:هات|اعطني|ارسل|ابعث|ابعت)\b.{0,20}\b(?:رقمك|رقم الهاتف|رقم الجوال|واتساب|واتس)\b/u',
            '/\b(?:تواصل|كلمني|راسلني|احكي معي)\b.{0,25}\b(?:خارج المنصه|خارج الموقع|واتساب|واتس|تلغرام|تيليجرام|انستغرام|فيسبوك)\b/u',
            '/\b(?:نتفق|نشتغل|نكمل|ننجز)\b.{0,25}\b(?:خارج المنصه|خارج الموقع|بيننا|بالخاص)\b/u',
            '/\b(?:حول|ادفع|الدفع)\b.{0,25}\b(?:مباشر|خارج المنصه|خارج الموقع|على حسابي|محفظتي)\b/u',
            '/\b(?:سعر اقل|خصم)\b.{0,30}\b(?:خارج المنصه|خارج الموقع|بالخاص)\b/u',
            '/(?:wa\.me|t\.me|telegram|whatsapp|واتساب|واتس|تيليجرام|تلغرام)/ui',
        ];

        $hasIntent = $this->matchesAny($text, $intentPatterns);

        $hasContactData =
            preg_match('/(?:\+?\d[\d\s\-\(\)]{7,}\d)/u', $text) === 1
            || preg_match('/@[a-zA-Z0-9_\.]{3,}/u', $text) === 1
            || preg_match('/\b[\w\.\-]+@[\w\.\-]+\.[a-zA-Z]{2,}\b/u', $text) === 1;

        return $hasIntent || (
            $hasContactData
            && preg_match(
                '/\b(?:تواصل|كلمني|راسلني|واتساب|واتس|اتصل|رقمي|هذا رقمي|خاص)\b/u',
                $text
            ) === 1
        );
    }

    private function askGemini(
        User $user,
        string $text,
        string $sourceType,
        array $context
    ): ?array {
        if (! config('services.gemini.enabled')) {
            return null;
        }

        $apiKey = config('services.gemini.api_key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            return null;
        }

$model = (string) config(
    'services.gemini.moderation_model',
    config(
        'services.gemini.model',
        'gemini-3.1-flash-lite'
    )
);

        $timeout = (int) config('services.gemini.timeout', 45);

        $contextJson = json_encode(
            [
                'sender_role' => $user->role,
                'source_type' => $sourceType,
                'recipient_role' => $context['recipient_role'] ?? null,
                'conversation_type' => $context['conversation_type'] ?? null,
            ],
            JSON_UNESCAPED_UNICODE
        );

        $prompt = <<<PROMPT
أنت نظام رقابة محتوى لمنصة خدمات هندسية.

حلل النص بحسب السياق، وأعد JSON صالحًا فقط دون Markdown.

المخالفات:
- threats_violence: التهديد المباشر أو التحريض على العنف.
- abuse_harassment: الشتائم والإساءة والتحرش والتنمر.
- hate_content: خطاب الكراهية ضد فئة محمية.
- sexual_content: إيحاء أو محتوى جنسي.
- explicit_sexual_content: محتوى جنسي صريح أو عري أو طلب صور خاصة.
- sexual_harassment: تحرش أو طلب جنسي غير مرغوب.
- sexual_minors: أي محتوى جنسي متعلق بقاصر، وهو خطر حرج.
- fraud_extortion: الاحتيال أو الابتزاز.
- sensitive_personal_data: نشر بيانات شخصية حساسة.
- off_platform_solicitation: محاولة المهندس أو المكتب أو الموظف أخذ العميل خارج المنصة.
- external_payment_request: طلب الدفع أو التحويل خارج المنصة.
- contact_information_request: طلب رقم الهاتف أو واتساب أو حساب خارجي بقصد نقل العمل.
- dangerous_link: رابط مشبوه أو ضار.

قواعد مهمة:
- لا تعتبر رقم مساحة أرض أو مبلغًا أو تاريخًا أو رقم مخطط بيانات تواصل من دون سياق.
- المحتوى الطبي أو التعليمي المشروع ليس جنسيًا لمجرد وجود مصطلحات تشريحية.
- محاولة المهندس أو المكتب نقل العميل خارج المنصة مرفوضة.
- التهديد المباشر والمحتوى الجنسي الصريح والمحتوى الجنسي المتعلق بقاصر مرفوض.
- عند الشك البسيط اختر needs_review بدل rejected.
- approved يعني السماح.
- needs_review يعني إيقاف مؤقت للمراجعة دون تحذير تلقائي.
- rejected يعني منع المحتوى.
- issue_warning يكون true فقط للمخالفة الواضحة.
- suspend_immediately يكون true فقط للحالات الحرجة جدًا مثل sexual_minors أو تهديد شديد ووشيك.

السياق:
{$contextJson}

النص:
{$text}

أعد هذا الشكل فقط:
{
  "decision": "approved|needs_review|rejected",
  "risk_level": "low|medium|high|critical",
  "categories": [],
  "reason": "سبب عربي مختصر",
  "issue_warning": false,
  "suspend_immediately": false
}
PROMPT;

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            rawurlencode($model)
        );

        try {
            $response = Http::acceptJson()
                ->timeout($timeout)
                ->retry(2, 700, throw: false)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0,
                        'maxOutputTokens' => 500,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if (! $response->successful()) {
                Log::error('Gemini moderation request failed.', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return null;
            }

            $rawText = collect(
                $response->json('candidates.0.content.parts', [])
            )
                ->pluck('text')
                ->filter(fn ($value) => is_string($value))
                ->implode("\n");

            $decoded = json_decode(trim($rawText), true);

            if (! is_array($decoded)) {
                Log::warning('Gemini moderation returned invalid JSON.', [
                    'raw' => Str::limit($rawText, 1000),
                ]);

                return null;
            }

            return $this->sanitizeAiDecision($decoded);
        } catch (Throwable $exception) {
            Log::error('Gemini moderation exception.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function sanitizeAiDecision(array $data): array
    {
        $allowedDecisions = ['approved', 'needs_review', 'rejected'];
        $allowedRiskLevels = ['low', 'medium', 'high', 'critical'];

        $decision = in_array(
            $data['decision'] ?? null,
            $allowedDecisions,
            true
        ) ? $data['decision'] : 'needs_review';

        $riskLevel = in_array(
            $data['risk_level'] ?? null,
            $allowedRiskLevels,
            true
        ) ? $data['risk_level'] : 'medium';

        $categories = collect($data['categories'] ?? [])
            ->filter(fn ($category) => is_string($category))
            ->map(fn ($category) => trim($category))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $this->decision(
            decision: $decision,
            riskLevel: $riskLevel,
            categories: $categories,
            reason: trim((string) (
                $data['reason']
                ?? 'تم اكتشاف محتوى يحتاج إلى مراجعة.'
            )),
            issueWarning: (bool) ($data['issue_warning'] ?? false),
            suspendImmediately: (bool) (
                $data['suspend_immediately'] ?? false
            )
        );
    }

    private function persistDecision(
        User $user,
        string $text,
        string $sourceType,
        ?int $sourceId,
        array $decision,
        string $provider,
        array $providerResponse
    ): array {
        $moderation = ContentModeration::create([
            'user_id' => $user->id,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'status' => 'completed',
            'decision' => $decision['decision'],
            'risk_level' => $decision['risk_level'],
            'detected_categories' => $decision['categories'],
            'category_scores' => [],
            'reason' => $decision['reason'],
            'provider' => $provider,
            'model' => $provider === 'gemini'
                ? config(
                    'services.gemini.moderation_model',
                    config('services.gemini.model')
                )
                : 'local-rules-v1',
            'provider_response' => array_merge(
                $providerResponse,
                ['content_excerpt' => Str::limit($text, 500)]
            ),
            'warning_issued' => false,
            'processed_at' => now(),
        ]);

        $warningIssued = false;
        $accountSuspended = false;

        if (
            $decision['decision'] === 'rejected'
            && $decision['issue_warning']
        ) {
            $warning = $this->warningService->issueWarning(
                user: $user,
                reason: $decision['reason'],
                moderation: $moderation,
                category: $decision['categories'][0]
                    ?? 'content_policy',
                issuedByType: 'ai'
            );

            $warningIssued = true;
            $accountSuspended = $warning->account_suspended;
        }

        if (
            $decision['suspend_immediately']
            && ! $accountSuspended
        ) {
            $user->forceFill([
                'status' => 'suspended_pending_review',
                'suspended_at' => now(),
                'suspension_reason' => $decision['reason'],
                'suspension_source' => 'content_moderation',
            ])->save();

            $accountSuspended = true;
        }

        return [
            'allowed' => false,
            'decision' => $decision['decision'],
            'risk_level' => $decision['risk_level'],
            'category' => $decision['categories'][0] ?? null,
            'categories' => $decision['categories'],
            'reason' => $decision['reason'],
            'user_message' => $this->userMessageFor($decision),
            'moderation_id' => $moderation->id,
            'warning_issued' => $warningIssued,
            'account_suspended' => $accountSuspended,
        ];
    }

    private function userMessageFor(array $decision): string
    {
        if (in_array(
            'off_platform_solicitation',
            $decision['categories'],
            true
        )) {
            return 'يمنع طلب بيانات التواصل أو نقل الاتفاق والدفع خارج المنصة. جميع المحادثات والمدفوعات يجب أن تتم داخل الموقع.';
        }

        if (
            in_array('explicit_sexual_content', $decision['categories'], true)
            || in_array('sexual_harassment', $decision['categories'], true)
            || in_array('sexual_minors', $decision['categories'], true)
        ) {
            return 'تم منع المحتوى لأنه يخالف سياسة المنصة الخاصة بالمحتوى الجنسي والتحرش.';
        }

        if (in_array(
            'threats_violence',
            $decision['categories'],
            true
        )) {
            return 'تم منع الرسالة لأنها تحتوي على تهديد أو محتوى عنيف.';
        }

        if ($decision['decision'] === 'needs_review') {
            return 'تم إيقاف المحتوى مؤقتًا وإرساله إلى الإدارة للمراجعة.';
        }

        return 'تم منع المحتوى لمخالفته سياسة استخدام المنصة.';
    }

    private function approvedResult(): array
    {
        return [
            'allowed' => true,
            'decision' => 'approved',
            'risk_level' => 'low',
            'category' => null,
            'categories' => [],
            'reason' => 'المحتوى آمن.',
            'user_message' => '',
            'moderation_id' => null,
            'warning_issued' => false,
            'account_suspended' => false,
        ];
    }

    private function decision(
        string $decision,
        string $riskLevel,
        array $categories,
        string $reason,
        bool $issueWarning,
        bool $suspendImmediately = false
    ): array {
        return [
            'decision' => $decision,
            'risk_level' => $riskLevel,
            'categories' => $categories,
            'reason' => $reason,
            'issue_warning' => $issueWarning,
            'suspend_immediately' => $suspendImmediately,
        ];
    }

    private function normalizeArabicText(string $text): string
    {
        $text = mb_strtolower($text);

        $text = str_replace(
            ['أ', 'إ', 'آ', 'ى', 'ة', 'ؤ', 'ئ'],
            ['ا', 'ا', 'ا', 'ي', 'ه', 'و', 'ي'],
            $text
        );

        $text = preg_replace(
            '/[\x{064B}-\x{065F}\x{0670}]/u',
            '',
            $text
        ) ?? $text;

        return preg_replace('/\s+/u', ' ', trim($text))
            ?? trim($text);
    }

    private function matchesAny(string $text, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }
}
