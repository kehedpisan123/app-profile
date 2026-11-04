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
                    ->label(__('Preview'))
                    ->disk('public')
                    ->state(fn (Media $record): ?string => $record->isImage() ? $record->file_path : null),
                TextColumn::make('file_name')
                    ->label(__('File Name'))
                    ->searchable()
                    ->description(fn (Media $record): ?string => $record->alt_text),
                TextColumn::make('mime_type')
                    ->label(__('Type'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('file_size')
                    ->label(__('Size'))
                    ->formatStateUsing(fn (int $state): string => Number::fileSize($state))
                    ->sortable(),
                TextColumn::make('url')
                    ->label(__('URL'))
                    ->state(fn (Media $record): string => $record->url())
                    ->copyable()
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label(__('Uploaded By'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Uploaded'))
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
