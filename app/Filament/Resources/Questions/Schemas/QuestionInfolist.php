<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(__('Question'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('subject')
                            ->label(__('Title')),
                        TextEntry::make('body')
                            ->label(__('Question Body'))
                            ->prose(),
                    ]),
                Section::make(__('Asker'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge(),
                        TextEntry::make('topic.name')
                            ->label(__('Topic'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(__('Name')),
                        TextEntry::make('email')
                            ->label(__('Email'))
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label(__('Phone'))
                            ->placeholder('-'),
                        TextEntry::make('ip_address')
                            ->label(__('IP Address'))
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label(__('Submitted'))
                            ->dateTime(),
                    ]),
            ]);
    }
}
