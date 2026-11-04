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
                Section::make('Pertanyaan')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('subject')
                            ->label('Judul'),
                        TextEntry::make('body')
                            ->label('Isi Pertanyaan')
                            ->prose(),
                    ]),
                Section::make('Penanya')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('topic.name')
                            ->label('Topik')
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label('Telepon')
                            ->placeholder('-'),
                        TextEntry::make('ip_address')
                            ->label('Alamat IP')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Dikirim')
                            ->dateTime(),
                    ]),
            ]);
    }
}
