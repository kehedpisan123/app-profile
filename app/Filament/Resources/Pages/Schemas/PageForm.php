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
                        RichEditor::make('content')
                            ->label(__('Page Body'))
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pages/attachments')
                            ->fileAttachmentsVisibility('public'),
                    ]),
                Section::make(__('Settings'))
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required(),
                        Select::make('user_id')
                            ->label(__('Author'))
                            ->relationship('user', 'name')
                            ->default(fn (): ?int => auth()->id())
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('sort_order')
                            ->label(__('Order'))
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->default(0),
                        Toggle::make('show_in_menu')
                            ->label(__('Show in Menu')),
                        FileUpload::make('image')
                            ->label(__('Image'))
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
