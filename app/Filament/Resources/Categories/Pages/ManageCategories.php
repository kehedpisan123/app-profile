<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Concerns\TranslatesManagedRecords;
use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ManageCategories extends ManageRecords
{
    use TranslatesManagedRecords;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
