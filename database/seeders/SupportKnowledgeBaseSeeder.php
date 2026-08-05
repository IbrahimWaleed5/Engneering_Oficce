<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SupportKnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'knowledge_base_articles';

        if (! Schema::hasTable($table)) {
            $this->command?->error(
                "Table {$table} does not exist. Run the migration first."
            );
            return;
        }

        $articles = require database_path(
            'data/support_knowledge_base_massive.php'
        );

        $columns = Schema::getColumnListing($table);

        foreach ($articles as $article) {
            $payload = $this->buildPayload($article, $columns);

            $identityColumn = $this->firstExistingColumn(
                $columns,
                ['question', 'title', 'name']
            );

            if (! $identityColumn || empty($payload[$identityColumn])) {
                continue;
            }

            DB::table($table)->updateOrInsert(
                [$identityColumn => $payload[$identityColumn]],
                $payload
            );
        }

        $this->command?->info(
            count($articles) . ' knowledge-base articles imported.'
        );
    }

    private function buildPayload(array $article, array $columns): array
    {
        $payload = [];

        $map = [
            ['question', ['question', 'title', 'name']],
            ['answer', ['answer', 'content', 'body']],
            ['category', ['category', 'section', 'topic']],
            ['keywords', ['keywords', 'tags', 'search_keywords']],
            ['priority', ['priority', 'sort_order', 'weight']],
        ];

        foreach ($map as [$source, $targets]) {
            $target = $this->firstExistingColumn($columns, $targets);

            if ($target) {
                $payload[$target] = $article[$source] ?? null;
            }
        }

        $activeColumn = $this->firstExistingColumn(
            $columns,
            ['is_active', 'active', 'status']
        );

        if ($activeColumn) {
            $payload[$activeColumn] =
                $activeColumn === 'status'
                    ? 'active'
                    : true;
        }

        if (in_array('created_at', $columns, true)) {
            $payload['created_at'] = now();
        }

        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        return $payload;
    }

    private function firstExistingColumn(
        array $columns,
        array $candidates
    ): ?string {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
    }
}
