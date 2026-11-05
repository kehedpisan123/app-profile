<?php

namespace App\Filament\Concerns;

trait BootsActiveLocale
{
    /**
     * Choose the content locale before the page fills the form.
     * The page mount hook runs too late: the form would otherwise read the UI locale.
     */
    public function bootBootsActiveLocale(): void
    {
        $locales = $this->getTranslatableLocales();
        $stored = session('spatie_translatable_active_locale');

        $this->activeLocale = is_string($stored) && in_array($stored, $locales, true)
            ? $stored
            : ($locales[0] ?? app()->getLocale());
    }
}
