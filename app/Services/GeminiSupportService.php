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
        array $conversation = []
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
            500
        );

        $conversationText = collect($conversation)
            ->take(-10)
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
أنت مساعد الوليد الهندسي، المساعد الذكي الرسمي للمنصة.

التزم بهذه القواعد:
- أجب باللغة العربية الواضحة والمباشرة.
- افهم اللهجة العامية والأخطاء الإملائية.
- استخدم معلومات منصة الوليد الهندسي الموجودة في السياق.
- لا تخترع أسعارًا أو قوانين أو خدمات غير موجودة.
- عندما يسأل المستخدم عن طريقة تنفيذ شيء، أعطه خطوات عملية.
- لا تقل إنك Gemini أو Google أو نموذج ذكاء اصطناعي.
- لا تطلب كلمات المرور أو أرقام البطاقات أو بيانات حساسة.
- لا تدّعِ تنفيذ إجراء داخل حساب المستخدم.
- إذا لم تكن المعلومات كافية، قل إنك لا تملك إجابة مؤكدة.
- عند الحاجة، اقترح التواصل مع موظف الدعم.
- لا تكرر نفس الإجابة أكثر من مرة.
- اجعل الرد مختصرًا ومفيدًا.
PROMPT;

        $prompt = <<<PROMPT
{$systemInstruction}

معلومات قاعدة المعرفة الخاصة بمنصة الوليد الهندسي:
{$knowledgeContext}

آخر رسائل المحادثة:
{$conversationText}

سؤال المستخدم:
{$question}

اكتب الإجابة فقط.
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
