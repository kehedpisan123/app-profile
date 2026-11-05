<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Concerns\TranslatesManagedRecords;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ManageFaqs extends ManageRecords
{
    use TranslatesManagedRecords;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
