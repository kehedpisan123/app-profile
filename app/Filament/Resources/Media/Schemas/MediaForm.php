<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class MediaForm
{
    public const DISK = 'public';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label(__('Media File'))
                    ->required()
                    ->disk(self::DISK)
                    ->directory('media')
                    ->visibility('public')
                    ->storeFileNamesIn('file_name')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'image/gif',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->maxSize(10240)
                    ->columnSpanFull(),
                TextInput::make('alt_text')
                    ->label(__('Alternative Text'))
                    ->helperText(__('Short image description for accessibility.'))
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withFileDetails(array $data): array
    {
        if (! is_string($data['file_path'] ?? null) || $data['file_path'] === '') {
            return $data;
        }

        $storage = Storage::disk(self::DISK);

        $data['disk'] = self::DISK;
        $data['mime_type'] = $storage->mimeType($data['file_path']) ?: null;
        $data['file_size'] = $storage->size($data['file_path']);
        $data['file_name'] ??= basename($data['file_path']);

        return $data;
    }
}
