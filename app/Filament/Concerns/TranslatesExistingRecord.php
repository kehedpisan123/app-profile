<?php

namespace App\Filament\Concerns;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable as EditTranslatable;

trait TranslatesExistingRecord
{
    use BootsActiveLocale;
    use EditTranslatable {
        EditTranslatable::fillForm as fillTranslatableForm;
    }
    use SwitchesActiveLocale;

    public function updatedActiveLocale(): void
    {
        $this->swapActiveLocaleForm();
    }

    protected function fillForm(): void
    {
        $this->fillTranslatableForm();

        $this->forgetBlankTranslations();
    }
}
