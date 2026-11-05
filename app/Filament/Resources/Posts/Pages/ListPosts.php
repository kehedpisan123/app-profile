<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Concerns\TranslatesRecordIndex;
use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ListPosts extends ListRecords
{
    use TranslatesRecordIndex;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
