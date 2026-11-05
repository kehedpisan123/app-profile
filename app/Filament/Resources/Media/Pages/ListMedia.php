<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Concerns\TranslatesRecordIndex;
use App\Filament\Resources\Media\MediaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ListMedia extends ListRecords
{
    use TranslatesRecordIndex;

    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
