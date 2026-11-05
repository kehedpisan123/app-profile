<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Filament\Concerns\TranslatesManagedRecords;
use App\Filament\Resources\Slides\SlideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ManageSlides extends ManageRecords
{
    use TranslatesManagedRecords;

    protected static string $resource = SlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
