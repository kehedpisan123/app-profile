<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Concerns\TranslatesNewRecord;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Media\Schemas\MediaForm;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class CreateMedia extends CreateRecord
{
    use TranslatesNewRecord;

    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return MediaForm::withFileDetails($data);
    }
}
