<?php

namespace App\Filament\Concerns;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable as CreateTranslatable;

trait TranslatesNewRecord
{
    use BootsActiveLocale;
    use CreateTranslatable;
    use SwitchesActiveLocale;

    public function updatedActiveLocale(): void
    {
        $this->swapActiveLocaleForm();
    }
}
