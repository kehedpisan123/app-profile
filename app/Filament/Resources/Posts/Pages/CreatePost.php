<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Concerns\TranslatesNewRecord;
use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class CreatePost extends CreateRecord
{
    use TranslatesNewRecord;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
