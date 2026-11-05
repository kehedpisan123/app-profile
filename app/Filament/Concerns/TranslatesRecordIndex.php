<?php

namespace App\Filament\Concerns;

use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable as ListTranslatable;

trait TranslatesRecordIndex
{
    use BootsActiveLocale;
    use ListTranslatable;

    public function updatingActiveLocale(): void
    {
        $this->oldActiveLocale = $this->activeLocale;
    }

    public function updatedActiveLocale(): void
    {
        if (filament('spatie-translatable')->getPersistLocale()) {
            session()->put('spatie_translatable_active_locale', $this->activeLocale);
        }
    }
}
