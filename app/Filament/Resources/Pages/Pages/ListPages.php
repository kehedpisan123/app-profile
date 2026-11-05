<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Concerns\TranslatesRecordIndex;
use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ListPages extends ListRecords
{
    use TranslatesRecordIndex;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
