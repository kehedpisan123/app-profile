<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\PageStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Konten')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->unique(),
                        RichEditor::make('content')
                            ->label('Isi Halaman')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pages/attachments')
                            ->fileAttachmentsVisibility('public'),
                    ]),
                Section::make('Pengaturan')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required(),
                        Select::make('user_id')
                            ->label('Penulis')
                            ->relationship('user', 'name')
                            ->default(fn (): ?int => auth()->id())
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->default(0),
                        Toggle::make('show_in_menu')
                            ->label('Tampilkan di Menu'),
                        FileUpload::make('image')
                            ->label('Gambar')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('pages')
                            ->visibility('public')
                            ->maxSize(2048),
                    ]),
            ]);
    }
}
