<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Concerns\TranslatesExistingRecord;
use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class EditPage extends EditRecord
{
    use TranslatesExistingRecord;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }
}
