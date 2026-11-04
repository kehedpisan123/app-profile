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
                Section::make(__('Menu'))
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('location')
                            ->label(__('Location'))
                            ->options(MenuLocation::class)
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->inline(false),
                    ]),
                Section::make(__('Menu Items'))
                    ->columnSpanFull()
                    ->schema([
                        self::menuItemsRepeater('rootItems')
                            ->hiddenLabel()
                            ->addActionLabel(__('Add menu item')),
                    ]),
            ]);
    }

    private static function menuItemsRepeater(string $relationship, bool $withChildren = true): Repeater
    {
        $fields = [
            Grid::make(4)->schema([
                TextInput::make('label')
                    ->label(__('Label'))
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label(__('Link Type'))
                    ->options(MenuItemType::class)
                    ->default(MenuItemType::Custom)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('linkable_id', null)),
                Select::make('linkable_id')
                    ->label(__('Target'))
                    ->options(fn (Get $get): array => self::linkableOptions($get('type')))
                    ->searchable()
                    ->required(fn (Get $get): bool => self::resolveType($get('type'))?->linkableModel() !== null)
                    ->visible(fn (Get $get): bool => self::resolveType($get('type'))?->linkableModel() !== null),
                TextInput::make('url')
                    ->label(__('URL'))
                    ->required(fn (Get $get): bool => self::resolveType($get('type')) === MenuItemType::Custom)
                    ->visible(fn (Get $get): bool => self::resolveType($get('type')) === MenuItemType::Custom)
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true)
                    ->inline(false),
            ]),
        ];

        if ($withChildren) {
            $fields[] = self::menuItemsRepeater('children', withChildren: false)
                ->label(__('Submenu'))
                ->addActionLabel(__('Add submenu'))
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
