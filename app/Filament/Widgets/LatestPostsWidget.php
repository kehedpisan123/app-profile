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

    protected function getTableHeading(): string
    {
        return __('Latest Articles');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Post::query()
                ->with(['category', 'user'])
                ->latest())
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->limit(60),
                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('user.name')
                    ->label(__('Author')),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->since(),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label(__('Change'))
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
