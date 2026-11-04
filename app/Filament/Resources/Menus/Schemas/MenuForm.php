<?php

namespace App\Filament\Resources\Menus\Schemas;

use App\Enums\MenuItemType;
use App\Enums\MenuLocation;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Select::make('location')
                            ->label('Lokasi')
                            ->options(MenuLocation::class)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false),
                    ]),
                Section::make('Item Menu')
                    ->columnSpanFull()
                    ->schema([
                        self::menuItemsRepeater('rootItems')
                            ->hiddenLabel()
                            ->addActionLabel('Tambah item menu'),
                    ]),
            ]);
    }

    private static function menuItemsRepeater(string $relationship, bool $withChildren = true): Repeater
    {
        $fields = [
            Grid::make(4)->schema([
                TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Jenis Tautan')
                    ->options(MenuItemType::class)
                    ->default(MenuItemType::Custom)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('linkable_id', null)),
                Select::make('linkable_id')
                    ->label('Tujuan')
                    ->options(fn (Get $get): array => self::linkableOptions($get('type')))
                    ->searchable()
                    ->required(fn (Get $get): bool => self::resolveType($get('type'))?->linkableModel() !== null)
                    ->visible(fn (Get $get): bool => self::resolveType($get('type'))?->linkableModel() !== null),
                TextInput::make('url')
                    ->label('URL')
                    ->required(fn (Get $get): bool => self::resolveType($get('type')) === MenuItemType::Custom)
                    ->visible(fn (Get $get): bool => self::resolveType($get('type')) === MenuItemType::Custom)
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),
            ]),
        ];

        if ($withChildren) {
            $fields[] = self::menuItemsRepeater('children', withChildren: false)
                ->label('Sub-menu')
                ->addActionLabel('Tambah sub-menu')
                ->defaultItems(0);
        }

        return Repeater::make($relationship)
            ->relationship()
            ->schema($fields)
            ->orderColumn('sort_order')
            ->collapsible()
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
            ->defaultItems(0);
    }

    private static function resolveType(mixed $type): ?MenuItemType
    {
        return $type instanceof MenuItemType ? $type : MenuItemType::tryFrom((string) $type);
    }

    /**
     * @return array<int, string>
     */
    private static function linkableOptions(mixed $type): array
    {
        $type = self::resolveType($type);
        $model = $type?->linkableModel();

        if ($model === null) {
            return [];
        }

        return $model::query()
            ->orderBy($type->linkableTitleAttribute())
            ->pluck($type->linkableTitleAttribute(), 'id')
            ->all();
    }
}
