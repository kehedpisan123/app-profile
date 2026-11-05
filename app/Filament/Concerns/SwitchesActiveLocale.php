<?php

namespace App\Filament\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait SwitchesActiveLocale
{
    public function swapActiveLocaleForm(): void
    {
        if (filament('spatie-translatable')->getPersistLocale()) {
            session()->put('spatie_translatable_active_locale', $this->activeLocale);
        }

        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->resetValidation();

        $translatableAttributes = static::getResource()::getTranslatableAttributes();
        $state = $this->form->getRawState();

        if ($state instanceof Arrayable) {
            $state = $state->toArray();
        }

        $this->rememberLocaleState(Arr::only($state, $translatableAttributes), (string) $this->oldActiveLocale);

        $this->form->fill([
            ...Arr::except($state, $translatableAttributes),
            ...($this->otherLocaleData[$this->activeLocale] ?? array_fill_keys($translatableAttributes, null)),
        ]);

        unset($this->otherLocaleData[$this->activeLocale]);
    }

    public function forgetBlankTranslations(): void
    {
        foreach (array_keys($this->otherLocaleData) as $locale) {
            $this->rememberLocaleState($this->otherLocaleData[$locale], (string) $locale);
        }
    }

    /**
     * @param  array<string, mixed>  $localeState
     */
    private function rememberLocaleState(array $localeState, string $locale): void
    {
        if ($this->shouldRememberLocaleState($localeState, $locale)) {
            $this->otherLocaleData[$locale] = $localeState;

            return;
        }

        unset($this->otherLocaleData[$locale]);
    }

    /**
     * @param  array<string, mixed>  $localeState
     */
    private function shouldRememberLocaleState(array $localeState, string $locale): bool
    {
        if (! $this->localeStateIsBlank($localeState)) {
            return true;
        }

        $record = method_exists($this, 'getRecord') ? $this->getRecord() : null;

        if (! $record instanceof Model || ! method_exists($record, 'getTranslation')) {
            return false;
        }

        foreach (array_keys($localeState) as $attribute) {
            if (! method_exists($record, 'isTranslatableAttribute') || ! $record->isTranslatableAttribute($attribute)) {
                continue;
            }

            if (filled($record->getTranslation($attribute, $locale, false))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $state
     */
    /**
     * @param  array<string, mixed>  $state
     */
    private function localeStateIsBlank(array $state): bool
    {
        foreach ($state as $value) {
            if (! $this->valueIsBlank($value)) {
                return false;
            }
        }

        return true;
    }

    private function valueIsBlank(mixed $value): bool
    {
        if (! is_array($value)) {
            return blank($value);
        }

        if (($value['type'] ?? null) === 'doc') {
            return blank($this->richTextToPlainText($value));
        }

        return $this->localeStateIsBlank($value);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private function richTextToPlainText(array $node): string
    {
        $text = is_string($node['text'] ?? null) ? $node['text'] : '';

        foreach ($node['content'] ?? [] as $child) {
            if (is_array($child)) {
                $text .= $this->richTextToPlainText($child);
            }
        }

        return $text;
    }
}
