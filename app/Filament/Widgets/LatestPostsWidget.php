<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPostsWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Artikel Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Post::query()
                ->with(['category', 'user'])
                ->latest())
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->limit(60),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('user.name')
                    ->label('Penulis'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Ubah')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
