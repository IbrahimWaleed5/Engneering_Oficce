<?php

namespace App\Services;

use App\Models\ContentModeration;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AttachmentModerationService
{
    private const SUPPORTED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
        'audio/mpeg',
        'audio/mp3',
        'audio/ogg',
        'audio/wav',
        'audio/x-wav',
        'audio/aac',
        'audio/flac',
        'video/webm',
    ];

    private const MAX_INLINE_BYTES = 20 * 1024 * 1024;

    public function __construct(
        private readonly ModerationWarningService $warningService
    ) {
    }

    public function moderate(
        User $user,
        UploadedFile $file,
        string $sourceType,
        ?int $sourceId = null,
        array $context = []
    ): array {
        $mimeType = (string) (
            $file->getMimeType()
            ?: $file->getClientMimeType()
            ?: 'application/octet-stream'
        );

        $fileSize = (int) ($file->getSize() ?: 0);
        $originalName = $file->getClientOriginalName();
        $realPath = $file->getRealPath();

        if (! is_string($realPath) || ! is_file($realPath)) {
            return $this->failedResult(
                'تعذر قراءة الملف المرفوع.'
            );
        }

        $fileHash = hash_file('sha256', $realPath) ?: null;

        /*
        |--------------------------------------------------------------------------
        | الأنواع غير المدعومة في الفحص الدلالي
        |--------------------------------------------------------------------------
        |
        | تبقى خاضعة للتحقق الموجود في الـController من الامتداد والحجم.
        | لا ندّعي فحص محتواها بصريًا أو صوتيًا.
        |
        */

        if (! in_array($mimeType, self::SUPPORTED_MIME_TYPES, true)) {
            return $this->approvedResult(
                scanned: false,
                reason: 'نوع الملف غير مدعوم في الفحص الدلالي، وتم الاكتفاء بالتحقق المحلي.'
            );
        }

        if ($fileSize <= 0 || $fileSize > self::MAX_INLINE_BYTES) {
            return $this->approvedResult(
                scanned: false,
                reason: 'حجم الملف غير مناسب للفحص المباشر، وتم الاكتفاء بالتحقق المحلي.'
            );
        }

        $apiKey = config('services.gemini.api_key');

        if (
            ! config('services.gemini.enabled')
            || ! is_string($apiKey)
            || trim($apiKey) === ''
        ) {
            return $this->approvedResult(
                scanned: false,
                reason: 'خدمة فحص المرفقات غير مفعلة.'
            );
        }

        $binary = file_get_contents($realPath);

        if ($binary === false) {
            return $this->failedResult(
                'تعذر قراءة بيانات الملف.'
            );
        }

        $model = (string) config(
            'services.gemini.moderation_model',
            config(
                'services.gemini.model',
                'gemini-3.1-flash-lite'
            )
        );

        $timeout = (int) config(
            'services.gemini.timeout',
            45
        );

        $contextJson = json_encode(
            [
                'sender_role' =>
                    $user->role,

                'source_type' =>
                    $sourceType,

                'original_name' =>
                    $originalName,

                'mime_type' =>
                    $mimeType,

                'recipient_role' =>
                    $context['recipient_role'] ?? null,

                'conversation_type' =>
                    $context['conversation_type'] ?? null,
            ],
            JSON_UNESCAPED_UNICODE
        );

        $prompt = <<<PROMPT
أنت نظام رقابة مرفقات لمنصة خدمات هندسية.

افحص الصورة أو المستند أو الصوت المرفق بحسب السياق.
أعد JSON صالحًا فقط دون Markdown.

المخالفات:
- threats_violence
- abuse_harassment
- hate_content
- sexual_content
- explicit_sexual_content
- nudity
- sexual_harassment
- sexual_minors
- fraud_extortion
- sensitive_personal_data
- off_platform_solicitation
- external_payment_request
- contact_information_request
- dangerous_content

قواعد:
- المخططات الهندسية وصور المباني والمستندات المهنية الطبيعية مسموحة.
- لا تعتبر أرقام الأبعاد أو المساحات أو الأسعار أو أرقام المخططات بيانات تواصل.
- أي محتوى جنسي متعلق بقاصر خطر حرج ويجب رفضه فورًا.
- العري الصريح أو المحتوى الجنسي الصريح أو التهديد الواضح مرفوض.
- عند عدم وضوح المحتوى اختر needs_review.
- approved يعني السماح.
- needs_review يعني منع مؤقت للمراجعة دون تحذير تلقائي.
- rejected يعني منع واضح.
- issue_warning يكون true فقط عند مخالفة واضحة.
- suspend_immediately للحالات الحرجة جدًا فقط.

السياق:
{$contextJson}

أعد:
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
                                [
                                    'text' => $prompt,
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => base64_encode($binary),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0,
                        'maxOutputTokens' => 500,
                        'responseMimeType' =>
                            'application/json',
                    ],
                ]);

            if (! $response->successful()) {
                Log::error(
                    'Gemini attachment moderation failed.',
                    [
                        'status' => $response->status(),
                        'response' => $response->json(),
                        'mime_type' => $mimeType,
                        'file_size' => $fileSize,
                    ]
                );

                return $this->approvedResult(
                    scanned: false,
                    reason: 'تعذر فحص المرفق آليًا، وتم الاكتفاء بالتحقق المحلي.'
                );
            }

            $rawText = collect(
                $response->json(
                    'candidates.0.content.parts',
                    []
                )
            )
                ->pluck('text')
                ->filter(
                    fn ($value) => is_string($value)
                )
                ->implode("\n");

            $decoded = json_decode(
                trim($rawText),
                true
            );

            if (! is_array($decoded)) {
                Log::warning(
                    'Gemini attachment moderation returned invalid JSON.',
                    [
                        'raw' =>
                            Str::limit($rawText, 1000),
                    ]
                );

                return $this->approvedResult(
                    scanned: false,
                    reason: 'تعذر تفسير نتيجة فحص المرفق.'
                );
            }

            $decision = $this->sanitizeDecision(
                $decoded
            );

            if ($decision['decision'] === 'approved') {
                return $this->approvedResult(
                    scanned: true,
                    reason: $decision['reason']
                );
            }

            return $this->persistDecision(
                user: $user,
                sourceType: $sourceType,
                sourceId: $sourceId,
                originalName: $originalName,
                mimeType: $mimeType,
                fileSize: $fileSize,
                fileHash: $fileHash,
                decision: $decision,
                providerResponse: $decoded,
                model: $model
            );
        } catch (Throwable $exception) {
            Log::error(
                'Attachment moderation exception.',
                [
                    'message' =>
                        $exception->getMessage(),

                    'mime_type' =>
                        $mimeType,

                    'file_size' =>
                        $fileSize,
                ]
            );

            return $this->approvedResult(
                scanned: false,
                reason: 'تعذر فحص المرفق آليًا، وتم الاكتفاء بالتحقق المحلي.'
            );
        }
    }

    private function sanitizeDecision(
        array $data
    ): array {
        $allowedDecisions = [
            'approved',
            'needs_review',
            'rejected',
        ];

        $allowedRiskLevels = [
            'low',
            'medium',
            'high',
            'critical',
        ];

        $decision = in_array(
            $data['decision'] ?? null,
            $allowedDecisions,
            true
        )
            ? $data['decision']
            : 'needs_review';

        $riskLevel = in_array(
            $data['risk_level'] ?? null,
            $allowedRiskLevels,
            true
        )
            ? $data['risk_level']
            : 'medium';

        $categories = collect(
            $data['categories'] ?? []
        )
            ->filter(
                fn ($category) =>
                    is_string($category)
            )
            ->map(
                fn ($category) =>
                    trim($category)
            )
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'decision' => $decision,
            'risk_level' => $riskLevel,
            'categories' => $categories,
            'reason' => trim(
                (string) (
                    $data['reason']
                    ?? 'المرفق يحتاج إلى مراجعة.'
                )
            ),
            'issue_warning' =>
                (bool) (
                    $data['issue_warning'] ?? false
                ),
            'suspend_immediately' =>
                (bool) (
                    $data['suspend_immediately']
                    ?? false
                ),
        ];
    }

    private function persistDecision(
        User $user,
        string $sourceType,
        ?int $sourceId,
        string $originalName,
        string $mimeType,
        int $fileSize,
        ?string $fileHash,
        array $decision,
        array $providerResponse,
        string $model
    ): array {
        $moderation = ContentModeration::create([
            'user_id' => $user->id,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'file_path' => null,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_hash' => $fileHash,
            'status' => 'completed',
            'decision' => $decision['decision'],
            'risk_level' => $decision['risk_level'],
            'detected_categories' =>
                $decision['categories'],
            'category_scores' => [],
            'reason' => $decision['reason'],
            'provider' => 'gemini',
            'model' => $model,
            'provider_response' =>
                $providerResponse,
            'warning_issued' => false,
            'processed_at' => now(),
        ]);

        $warningIssued = false;
        $accountSuspended = false;

        if (
            $decision['decision'] === 'rejected'
            && $decision['issue_warning']
        ) {
            $warning =
                $this->warningService->issueWarning(
                    user: $user,
                    reason: $decision['reason'],
                    moderation: $moderation,
                    category:
                        $decision['categories'][0]
                            ?? 'attachment_policy',
                    issuedByType: 'ai'
                );

            $warningIssued = true;
            $accountSuspended =
                (bool) $warning->account_suspended;
        }

        if (
            $decision['suspend_immediately']
            && ! $accountSuspended
        ) {
            $user->forceFill([
                'status' =>
                    'suspended_pending_review',

                'suspended_at' =>
                    now(),

                'suspension_reason' =>
                    $decision['reason'],

                'suspension_source' =>
                    'attachment_moderation',
            ])->save();

            $accountSuspended = true;
        }

        return [
            'allowed' => false,
            'scanned' => true,
            'decision' => $decision['decision'],
            'risk_level' => $decision['risk_level'],
            'category' =>
                $decision['categories'][0] ?? null,
            'categories' => $decision['categories'],
            'reason' => $decision['reason'],
            'user_message' =>
                $decision['decision']
                    === 'needs_review'
                        ? 'تم إيقاف المرفق مؤقتًا وإرساله إلى الإدارة للمراجعة.'
                        : 'تم منع المرفق لأنه يخالف سياسة استخدام المنصة.',
            'moderation_id' => $moderation->id,
            'warning_issued' => $warningIssued,
            'account_suspended' =>
                $accountSuspended,
        ];
    }

    private function approvedResult(
        bool $scanned = true,
        string $reason = 'المرفق آمن.'
    ): array {
        return [
            'allowed' => true,
            'scanned' => $scanned,
            'decision' => 'approved',
            'risk_level' => 'low',
            'category' => null,
            'categories' => [],
            'reason' => $reason,
            'user_message' => '',
            'moderation_id' => null,
            'warning_issued' => false,
            'account_suspended' => false,
        ];
    }

    private function failedResult(
        string $reason
    ): array {
        return [
            'allowed' => false,
            'scanned' => false,
            'decision' => 'rejected',
            'risk_level' => 'medium',
            'category' => 'invalid_attachment',
            'categories' => [
                'invalid_attachment',
            ],
            'reason' => $reason,
            'user_message' => $reason,
            'moderation_id' => null,
            'warning_issued' => false,
            'account_suspended' => false,
        ];
    }
}
