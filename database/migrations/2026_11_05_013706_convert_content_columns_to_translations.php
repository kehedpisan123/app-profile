<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Visitor-facing columns, with the plain-text type used if this migration is rolled back.
     *
     * @var array<string, array<string, string>>
     */
    private array $columns = [
        'categories' => [
            'name' => 'string',
            'description' => 'text',
        ],
        'posts' => [
            'title' => 'string',
            'summary' => 'text',
            'content' => 'longText',
        ],
        'pages' => [
            'title' => 'string',
            'content' => 'longText',
        ],
        'media' => [
            'alt_text' => 'string',
        ],
        'menu_items' => [
            'label' => 'string',
        ],
        'slides' => [
            'title' => 'string',
            'description' => 'text',
        ],
        'question_topics' => [
            'name' => 'string',
            'description' => 'text',
        ],
        'faqs' => [
            'question' => 'string',
            'answer' => 'longText',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach (array_keys($columns) as $column) {
                if ($this->alreadyStoresTranslations($table, $column)) {
                    continue;
                }

                $this->convertToTranslations($table, $column);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column => $legacyType) {
                if (! $this->alreadyStoresTranslations($table, $column)) {
                    continue;
                }

                $this->restorePlainText($table, $column, $legacyType);
            }
        }
    }

    private function alreadyStoresTranslations(string $table, string $column): bool
    {
        return in_array(Schema::getColumnType($table, $column), ['json', 'jsonb'], true);
    }

    private function convertToTranslations(string $table, string $column): void
    {
        $temporary = $column.'_translated';

        Schema::table($table, function (Blueprint $blueprint) use ($temporary): void {
            $blueprint->json($temporary)->nullable();
        });

        DB::table($table)->orderBy('id')->chunkById(100, function ($rows) use ($table, $column, $temporary): void {
            foreach ($rows as $row) {
                $value = $row->{$column};

                DB::table($table)->where('id', $row->id)->update([
                    $temporary => $value === null
                        ? null
                        : json_encode(['id' => $value], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column): void {
            $blueprint->dropColumn($column);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column, $temporary): void {
            $blueprint->renameColumn($temporary, $column);
        });
    }

    private function restorePlainText(string $table, string $column, string $legacyType): void
    {
        $temporary = $column.'_plain';

        Schema::table($table, function (Blueprint $blueprint) use ($temporary, $legacyType): void {
            match ($legacyType) {
                'text' => $blueprint->text($temporary)->nullable(),
                'longText' => $blueprint->longText($temporary)->nullable(),
                default => $blueprint->string($temporary)->nullable(),
            };
        });

        DB::table($table)->orderBy('id')->chunkById(100, function ($rows) use ($table, $column, $temporary): void {
            foreach ($rows as $row) {
                $raw = $row->{$column};
                $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
                $value = is_array($decoded) ? ($decoded['id'] ?? (reset($decoded) ?: null)) : $raw;

                DB::table($table)->where('id', $row->id)->update([
                    $temporary => $value,
                ]);
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column): void {
            $blueprint->dropColumn($column);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column, $temporary): void {
            $blueprint->renameColumn($temporary, $column);
        });
    }
};
