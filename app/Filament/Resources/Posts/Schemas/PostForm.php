<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
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
                        Textarea::make('summary')
                            ->label('Ringkasan')
                            ->rows(3)
                            ->maxLength(500),
                        RichEditor::make('content')
                            ->label('Isi Artikel')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->fileAttachmentsVisibility('public'),
                    ]),
                Section::make('Publikasi')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->options(PostStatus::class)
                            ->default(PostStatus::Draft)
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal Terbit')
                            ->helperText('Kosongkan untuk memakai waktu saat artikel diterbitkan.'),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('user_id')
                            ->label('Penulis')
                            ->relationship('user', 'name')
                            ->default(fn (): ?int => auth()->id())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('Artikel Unggulan'),
                        FileUpload::make('image')
                            ->label('Gambar Unggulan')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('posts')
                            ->visibility('public')
                            ->maxSize(2048),
                    ]),
            ]);
    }
}
