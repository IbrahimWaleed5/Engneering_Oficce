<?php

namespace App\Services;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SupportBotService
{
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
            $searchableText = Str::lower(
                $article->question . ' ' .
                $article->keywords . ' ' .
                $article->answer
            );

            $score = 0;

            foreach ($words as $word) {
                if (Str::contains($searchableText, $word)) {
                    $score++;
                }
            }

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
         * لا يرسل إجابة غير مؤكدة.
         */
        if ($confidence < 0.35) {
            return null;
        }

        $bestArticle->increment('views');

        return [
            'article' => $bestArticle,
            'answer' => $bestArticle->answer,
            'confidence' => round($confidence, 4),
        ];
    }

    private function extractWords(string $message): Collection
    {
        $message = Str::lower(trim($message));

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
            'أين',
            'هل',
            'في',
            'من',
            'على',
            'إلى',
            'الى',
            'عن',
            'انا',
            'أنا',
            'بدي',
            'اريد',
            'أريد',
            'عندي',
            'ممكن',
        ];

        return collect(
            preg_split('/\s+/u', $message)
        )
            ->filter(fn ($word) => mb_strlen($word) >= 3)
            ->reject(
                fn ($word) => in_array(
                    $word,
                    $ignoredWords,
                    true
                )
            )
            ->unique()
            ->values();
    }
}
