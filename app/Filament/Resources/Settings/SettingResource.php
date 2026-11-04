<?php

namespace App\Filament\Resources\Settings;

use App\Enums\NavigationGroup;
use App\Enums\SettingType;
use App\Filament\Resources\Settings\Pages\ManageSettings;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'pengaturan';

    protected static ?string $pluralModelLabel = 'pengaturan situs';

    protected static ?string $recordTitleAttribute = 'label';

    /**
     * @var array<string, string>
     */
    public const GROUPS = [
        'general' => 'Umum',
        'contact' => 'Kontak',
        'social' => 'Media Sosial',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255),
                TextInput::make('key')
                    ->label('Kunci')
                    ->helperText('Dipakai aplikasi Flutter, jangan diubah setelah dipakai.')
                    ->required()
                    ->maxLength(100)
                    ->regex('/^[a-z0-9_]+$/')
                    ->unique()
                    ->disabledOn('edit'),
                Select::make('group')
                    ->label('Grup')
                    ->options(self::GROUPS)
                    ->default('general')
                    ->required(),
                Select::make('type')
                    ->label('Tipe')
                    ->options(SettingType::class)
                    ->default(SettingType::Text)
                    ->required()
                    ->live(),
                TextInput::make('value')
                    ->label('Nilai')
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Text))
                    ->columnSpanFull(),
                Textarea::make('value')
                    ->label('Nilai')
                    ->rows(4)
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Textarea))
                    ->columnSpanFull(),
                Textarea::make('value')
                    ->label('Nilai (JSON)')
                    ->rows(6)
                    ->json()
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Json))
                    ->columnSpanFull(),
                FileUpload::make('value')
                    ->label('Gambar')
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Image))
                    ->columnSpanFull(),
                Toggle::make('value')
                    ->label('Aktif')
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Boolean)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->defaultGroup(
                Group::make('group')
                    ->label('Grup')
                    ->getTitleFromRecordUsing(fn (Setting $record): string => self::GROUPS[$record->group] ?? Str::headline($record->group)),
            )
            ->defaultSort('key')
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->description(fn (Setting $record): string => $record->key)
                    ->searchable(['label', 'key']),
                TextColumn::make('value')
                    ->label('Nilai')
                    ->limit(60)
                    ->placeholder('-'),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Grup')
                    ->options(self::GROUPS),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSettings::route('/'),
        ];
    }

    private static function typeIs(mixed $state, SettingType $type): bool
    {
        return ($state instanceof SettingType ? $state : SettingType::tryFrom((string) $state)) === $type;
    }
}
