<?php

namespace App\Filament\Resources\Media\Tables;

use App\Models\Media;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Pratinjau')
                    ->disk('public')
                    ->state(fn (Media $record): ?string => $record->isImage() ? $record->file_path : null),
                TextColumn::make('file_name')
                    ->label('Nama Berkas')
                    ->searchable()
                    ->description(fn (Media $record): ?string => $record->alt_text),
                TextColumn::make('mime_type')
                    ->label('Tipe')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->formatStateUsing(fn (int $state): string => Number::fileSize($state))
                    ->sortable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->state(fn (Media $record): string => $record->url())
                    ->copyable()
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Diunggah oleh')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
