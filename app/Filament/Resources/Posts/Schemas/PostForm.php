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
                Section::make(__('Content'))
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->unique(),
                        Textarea::make('summary')
                            ->label(__('Summary'))
                            ->rows(3)
                            ->maxLength(500),
                        RichEditor::make('content')
                            ->label(__('Article Body'))
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->fileAttachmentsVisibility('public'),
                    ]),
                Section::make(__('Publishing'))
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(PostStatus::class)
                            ->default(PostStatus::Draft)
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label(__('Published At'))
                            ->helperText(__('Leave empty to use the time when the article is published.')),
                        Select::make('category_id')
                            ->label(__('Category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('user_id')
                            ->label(__('Author'))
                            ->relationship('user', 'name')
                            ->default(fn (): ?int => auth()->id())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Toggle::make('is_featured')
                            ->label(__('Featured Article')),
                        FileUpload::make('image')
                            ->label(__('Featured Image'))
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
