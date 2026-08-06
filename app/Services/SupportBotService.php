<?php

namespace App\Services;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SupportBotService
{
    /**
     * البحث التقليدي عن أفضل إجابة.
     *
     * نُبقي هذه الدالة كحل احتياطي إذا فشل Gemini
     * أو كان غير مفعّل.
     */
    public function findAnswer(string $message): ?array
    {
        $words = $this->extractWords($message);

        if ($words->isEmpty()) {
            return null;
        }

        $articles = KnowledgeBaseArticle::query()
            ->where('is_active', true)
            ->get();

        $bestArticle = null;
        $bestScore = 0;

        foreach ($articles as $article) {
            $score = $this->calculateScore(
                $article,
                $words
            );

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestArticle = $article;
            }
        }

        if (! $bestArticle || $bestScore < 1) {
            return null;
        }

        $confidence = min(
            1,
            $bestScore / max($words->count(), 1)
        );

        /*
         * لا نرسل إجابة ثابتة غير مؤكدة.
         */
        if ($confidence < 0.35) {
            return null;
        }

        $bestArticle->increment('views');

        return [
            'article' => $bestArticle,
            'answer' => $bestArticle->answer,
            'confidence' => round(
                $confidence,
                4
            ),
        ];
    }

    /**
     * بناء سياق قاعدة المعرفة لإرساله إلى Gemini.
     *
     * بدل إرسال إجابة واحدة فقط، نرسل أكثر المقالات
     * ارتباطًا بالسؤال حتى يصيغ الذكاء الاصطناعي
     * إجابة واضحة ومناسبة.
     */
    public function buildKnowledgeContext(
        string $message,
        int $limit = 6
    ): string {
        $articles = $this->findRelevantArticles(
            $message,
            $limit
        );

        if ($articles->isEmpty()) {
            return <<<'TEXT'
لا توجد معلومات مطابقة بشكل مؤكد في قاعدة المعرفة.
لا تخترع تفاصيل عن المنصة.
أخبر المستخدم أن المعلومات غير متوفرة بشكل مؤكد،
واقترح التواصل مع موظف الدعم عند الحاجة.
TEXT;
        }

        return $articles
            ->values()
            ->map(
                function (
                    KnowledgeBaseArticle $article,
                    int $index
                ): string {
                    $number = $index + 1;

                    $keywords = $this->normalizeKeywords(
                        $article->keywords
                    );

                    return implode("\n", [
                        "المعلومة رقم {$number}:",
                        'السؤال: ' .
                            trim(
                                (string) $article->question
                            ),
                        'الإجابة المعتمدة: ' .
                            trim(
                                (string) $article->answer
                            ),
                        'الكلمات المفتاحية: ' .
                            (
                                $keywords !== ''
                                    ? $keywords
                                    : 'غير محددة'
                            ),
                        'التصنيف: ' .
                            (
                                $article->category
                                ?? 'عام'
                            ),
                    ]);
                }
            )
            ->implode(
                "\n\n--------------------\n\n"
            );
    }

    /**
     * إرجاع أكثر مقالات قاعدة المعرفة ارتباطًا بالسؤال.
     */
    public function findRelevantArticles(
        string $message,
        int $limit = 6
    ): Collection {
        $limit = max(
            1,
            min($limit, 10)
        );

        $words = $this->extractWords($message);

        if ($words->isEmpty()) {
            return collect();
        }

        $articles = KnowledgeBaseArticle::query()
            ->where('is_active', true)
            ->get();

        return $articles
            ->map(function (
                KnowledgeBaseArticle $article
            ) use ($words) {
                return [
                    'article' => $article,
                    'score' => $this->calculateScore(
                        $article,
                        $words
                    ),
                ];
            })
            ->filter(
                fn (array $item) =>
                    $item['score'] > 0
            )
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('article')
            ->values();
    }

    /**
     * حساب درجة ارتباط المقالة بالسؤال.
     */
    private function calculateScore(
        KnowledgeBaseArticle $article,
        Collection $words
    ): float {
        $questionText = Str::lower(
            (string) $article->question
        );

        $keywordsText = Str::lower(
            $this->normalizeKeywords(
                $article->keywords
            )
        );

        $answerText = Str::lower(
            (string) $article->answer
        );

        $score = 0;

        foreach ($words as $word) {
            /*
             * تطابق السؤال له وزن أكبر؛ لأنه الأكثر دقة.
             */
            if (
                Str::contains(
                    $questionText,
                    $word
                )
            ) {
                $score += 4;
            }

            /*
             * تطابق الكلمات المفتاحية له وزن قوي.
             */
            if (
                Str::contains(
                    $keywordsText,
                    $word
                )
            ) {
                $score += 3;
            }

            /*
             * تطابق نص الإجابة له وزن أقل.
             */
            if (
                Str::contains(
                    $answerText,
                    $word
                )
            ) {
                $score += 1;
            }
        }

        /*
         * إعطاء أولوية إضافية للمقالات المميزة،
         * بشرط وجود عمود priority في الجدول.
         */
        if (
            isset($article->priority)
            && is_numeric($article->priority)
        ) {
            $score +=
                ((float) $article->priority) * 0.1;
        }

        return $score;
    }

    /**
     * تحويل الكلمات المفتاحية إلى نص صالح سواء كانت:
     * نصًا عاديًا أو JSON أو Array.
     */
    private function normalizeKeywords(
        mixed $keywords
    ): string {
        if (is_array($keywords)) {
            return collect($keywords)
                ->filter()
                ->map(
                    fn ($keyword) =>
                        trim((string) $keyword)
                )
                ->implode(', ');
        }

        if (! is_string($keywords)) {
            return '';
        }

        $keywords = trim($keywords);

        if ($keywords === '') {
            return '';
        }

        $decoded = json_decode(
            $keywords,
            true
        );

        if (
            json_last_error()
                === JSON_ERROR_NONE
            && is_array($decoded)
        ) {
            return collect($decoded)
                ->filter()
                ->map(
                    fn ($keyword) =>
                        trim((string) $keyword)
                )
                ->implode(', ');
        }

        return $keywords;
    }

    /**
     * استخراج الكلمات المهمة من سؤال المستخدم.
     */
    private function extractWords(
        string $message
    ): Collection {
        $message = Str::lower(
            trim($message)
        );

        /*
         * توحيد بعض الحروف العربية لتقليل اختلاف الكتابة.
         */
        $message = str_replace(
            [
                'أ',
                'إ',
                'آ',
                'ة',
                'ى',
                'ؤ',
                'ئ',
            ],
            [
                'ا',
                'ا',
                'ا',
                'ه',
                'ي',
                'و',
                'ي',
            ],
            $message
        );

        $message = preg_replace(
            '/[^\p{Arabic}\p{L}\p{N}\s]/u',
            ' ',
            $message
        );

        $ignoredWords = [
            'كيف',
            'شو',
            'ماذا',
            'وين',
            'اين',
            'هل',
            'في',
            'من',
            'على',
            'الي',
            'الى',
            'عن',
            'انا',
            'بدي',
            'اريد',
            'عندي',
            'ممكن',
            'لو',
            'ما',
            'هو',
            'هي',
            'هذا',
            'هذه',
            'مع',
            'او',
            'ثم',
            'كل',
            'اي',
            'ليش',
            'ليه',
            'طريقة',
            'طريقه',
        ];

        return collect(
            preg_split(
                '/\s+/u',
                $message
            )
        )
            ->map(
                fn ($word) =>
                    trim((string) $word)
            )
            ->filter(
                fn ($word) =>
                    mb_strlen($word) >= 2
            )
            ->reject(
                fn ($word) =>
                    in_array(
                        $word,
                        $ignoredWords,
                        true
                    )
            )
            ->unique()
            ->values();
    }
}
