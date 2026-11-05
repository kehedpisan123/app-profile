<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Concerns\TranslatesExistingRecord;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Media\Schemas\MediaForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class EditMedia extends EditRecord
{
    use TranslatesExistingRecord;

    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MediaForm::withFileDetails($data);
    }
}
