<?php

namespace App\Filament\Concerns;

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use LaraZeus\SpatieTranslatable\Resources\Pages\ManageRecords\Concerns\Translatable;

trait TranslatesManagedRecords
{
    use BootsActiveLocale;
    use Translatable;

    /**
     * Translations for locales other than the one currently shown in a modal.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $otherLocaleData = [];

    public function updatingActiveLocale(): void
    {
        $this->oldActiveLocale = $this->activeLocale;
    }

    public function updatedActiveLocale(): void
    {
        if (filament('spatie-translatable')->getPersistLocale()) {
            session()->put('spatie_translatable_active_locale', $this->activeLocale);
        }

        if (blank($this->oldActiveLocale)) {
            return;
        }

        $schema = $this->getMountedActionSchema();

        if (! $schema instanceof Schema) {
            return;
        }

        $statePath = $schema->getStatePath();
        $state = is_string($statePath) ? data_get($this, $statePath) : null;

        if (! is_array($state)) {
            return;
        }

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        $this->otherLocaleData[$this->oldActiveLocale] = Arr::only($state, $translatableAttributes);

        $schema->fill([
            ...Arr::except($state, $translatableAttributes),
            ...($this->otherLocaleData[$this->activeLocale] ?? $this->translationsForMountedRecord($translatableAttributes)),
        ]);

        unset($this->otherLocaleData[$this->activeLocale]);
    }

    /**
     * @param  array<int, string>  $attributes
     * @return array<string, mixed>
     */
    private function translationsForMountedRecord(array $attributes): array
    {
        $record = $this->getMountedAction()?->getRecord();

        if (! $record instanceof Model || ! method_exists($record, 'getTranslation')) {
            return [];
        }

        $translations = [];

        foreach ($attributes as $attribute) {
            if (! $record->isTranslatableAttribute($attribute)) {
                continue;
            }

            $translations[$attribute] = $record->getTranslation($attribute, (string) $this->activeLocale, false);
        }

        return $translations;
    }
}
