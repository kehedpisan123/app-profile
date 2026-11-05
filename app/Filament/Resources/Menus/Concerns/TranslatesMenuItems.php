<?php

namespace App\Filament\Resources\Menus\Concerns;

use Filament\Support\Contracts\TranslatableContentDriver;
use LaraZeus\SpatieTranslatable\SpatieTranslatableContentDriver;

trait TranslatesMenuItems
{
    public ?string $activeLocale = null;

    public function bootTranslatesMenuItems(): void
    {
        $locales = $this->getTranslatableLocales();
        $stored = session('spatie_translatable_active_locale');

        $this->activeLocale = is_string($stored) && in_array($stored, $locales, true)
            ? $stored
            : ($locales[0] ?? app()->getLocale());
    }

    /**
     * @return array<int, string>
     */
    public function getTranslatableLocales(): array
    {
        return filament('spatie-translatable')->getDefaultLocales() ?? [app()->getLocale()];
    }

    public function getActiveSchemaLocale(): ?string
    {
        return $this->activeLocale;
    }

    /**
     * @return class-string<TranslatableContentDriver>|null
     */
    public function getFilamentTranslatableContentDriver(): ?string
    {
        return SpatieTranslatableContentDriver::class;
    }

    public function updatedActiveLocale(): void
    {
        if (filament('spatie-translatable')->getPersistLocale()) {
            session()->put('spatie_translatable_active_locale', $this->activeLocale);
        }

        if (($this->record ?? null) !== null) {
            $this->fillForm();
        }
    }
}
