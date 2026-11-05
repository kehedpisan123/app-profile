<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Concerns\TranslatesNewRecord;
use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class CreatePage extends CreateRecord
{
    use TranslatesNewRecord;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
