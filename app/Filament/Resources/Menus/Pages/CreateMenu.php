<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\Concerns\TranslatesMenuItems;
use App\Filament\Resources\Menus\MenuResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class CreateMenu extends CreateRecord
{
    use TranslatesMenuItems;

    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
