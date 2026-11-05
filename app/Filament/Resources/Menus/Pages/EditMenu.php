<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\Concerns\TranslatesMenuItems;
use App\Filament\Resources\Menus\MenuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class EditMenu extends EditRecord
{
    use TranslatesMenuItems;

    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }
}
