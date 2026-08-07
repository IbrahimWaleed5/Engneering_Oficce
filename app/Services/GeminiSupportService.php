<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiSupportService
{
    public function answer(
        string $question,
        string $knowledgeContext = '',
        array $conversation = [],
        string $userContext = ''
    ): ?string {
        if (! config('services.gemini.enabled')) {
            Log::warning('Gemini support is disabled.');

            return null;
        }

        $apiKey = config('services.gemini.api_key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            Log::warning('Gemini API key is missing.');

            return null;
        }

        $model = config(
            'services.gemini.model',
            'gemini-3.1-flash-lite'
        );

        $timeout = (int) config(
            'services.gemini.timeout',
            45
        );

        $maxOutputTokens = (int) config(
            'services.gemini.max_output_tokens',
            900
        );

        $conversationText = collect($conversation)
            ->take(-12)
            ->map(function (array $message): string {
                $senderType =
                    $message['sender_type']
                    ?? 'system';

                $sender = match ($senderType) {
                    'customer' => 'المستخدم',
                    'employee' => 'موظف الدعم',
                    'admin' => 'إدارة المنصة',
                    'bot' => 'المساعد الذكي',
                    default => 'النظام',
                };

                $text = trim(
                    (string) (
                        $message['message']
                        ?? ''
                    )
                );

                return $text !== ''
                    ? $sender . ': ' . $text
                    : '';
            })
            ->filter()
            ->implode("\n");

        $systemInstruction = <<<'PROMPT'
أنت "مساعد الوليد الهندسي"، المساعد الرسمي والفعلي للعملاء داخل منصة الوليد الهندسي.

مهمتك:
- ساعد المستخدم في أي سؤال يتعلق باستخدام المنصة وخدماتها.
- افهم العربية الفصحى واللهجات العامية والأخطاء الإملائية.
- أجب بخطوات عملية ومباشرة.
- اعتمد أولًا على معلومات المنصة ومقالات قاعدة المعرفة والسياق المرسل لك.
- إذا لم توجد مقالة مطابقة، لا تقل مباشرة "لا أملك معلومات".
- استخدم خريطة النظام العامة الموجودة في السياق لاستنتاج الخطوات التشغيلية الآمنة.
- لا تخترع أسعارًا أو مددًا أو حالة طلب أو دفعة أو بيانات خاصة غير موجودة.
- عندما يحتاج السؤال إلى معلومة فعلية من حساب المستخدم ولم تُرسل لك، اشرح له أين يجدها داخل المنصة بدل اختراعها.
- لا تدّعِ أنك نفذت إجراء داخل الحساب.
- لا تقل إنك Gemini أو Google أو نموذج ذكاء اصطناعي.
- لا تطلب كلمة مرور أو رمز OTP أو بيانات بطاقة أو أي بيانات حساسة.
- لا تقترح التواصل خارج المنصة إذا كانت الخدمة يجب أن تتم داخلها.
- لا تكرر الإجابة بلا داعٍ.
- اجعل الرد واضحًا ومفيدًا، ويمكن أن يكون أطول قليلًا إذا كانت الخطوات تحتاج ذلك.

التحويل الأمني:
إذا كان السؤال عن اختراق حساب، سرقة حساب، دخول غير مصرح به، كلمة مرور مسروقة،
رمز تحقق أو OTP، تسريب بيانات، كشف بيانات مستخدم آخر، تغيير صلاحيات بغير حق،
ثغرة أمنية، تجاوز حماية، انتحال هوية، دفع مشبوه، أو حادث أمني فعلي:
- لا تقدم إرشادات قد تساعد على تجاوز الحماية.
- أخبر المستخدم أن الحالة تحتاج موظف دعم.
- اطلب تحويل المحادثة إلى موظف الدعم.
- لا تطلب منه إرسال بيانات حساسة.

قاعدة مهمة:
المشكلات العادية في استخدام المنصة لا تُحوّل للدعم تلقائيًا.
حاول حلها أولًا بنفسك من معلومات النظام والسياق.
PROMPT;

        $resolvedUserContext =
            trim($userContext) !== ''
                ? trim($userContext)
                : 'لا توجد معلومات إضافية عن المستخدم الحالي.';

        $resolvedKnowledgeContext =
            trim($knowledgeContext) !== ''
                ? trim($knowledgeContext)
                : 'لا توجد مقالات إضافية، استخدم خريطة النظام العامة إن كانت موجودة في السياق.';

        $resolvedConversation =
            trim($conversationText) !== ''
                ? trim($conversationText)
                : 'لا توجد رسائل سابقة.';

        $prompt = <<<PROMPT
{$systemInstruction}

====================
سياق المستخدم الحالي
====================
{$resolvedUserContext}

====================
معلومات المنصة وقاعدة المعرفة
====================
{$resolvedKnowledgeContext}

====================
آخر رسائل المحادثة
====================
{$resolvedConversation}

====================
سؤال المستخدم الحالي
====================
{$question}

اكتب الإجابة النهائية للمستخدم فقط، بدون شرح داخلي أو JSON.
PROMPT;

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            rawurlencode($model)
        );

        try {
            $response = Http::acceptJson()
                ->timeout($timeout)
                ->retry(
                    2,
                    700,
                    throw: false
                )
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
                            ],
                        ],
                    ],

                    'generationConfig' => [
                        'maxOutputTokens' =>
                            $maxOutputTokens,

                        'temperature' => 0.35,
                    ],
                ]);

            if (! $response->successful()) {
                Log::error(
                    'Gemini support request failed.',
                    [
                        'status' =>
                            $response->status(),

                        'response' =>
                            $response->json(),
                    ]
                );

                return null;
            }

            $parts = $response->json(
                'candidates.0.content.parts',
                []
            );

            $answer = collect($parts)
                ->pluck('text')
                ->filter(
                    fn ($text) =>
                        is_string($text)
                        && trim($text) !== ''
                )
                ->implode("\n");

            if (trim($answer) === '') {
                Log::warning(
                    'Gemini returned an empty response.',
                    [
                        'response' =>
                            $response->json(),
                    ]
                );

                return null;
            }

            return trim($answer);
        } catch (Throwable $exception) {
            Log::error(
                'Gemini support exception.',
                [
                    'message' =>
                        $exception->getMessage(),
                ]
            );

            return null;
        }
    }
}
