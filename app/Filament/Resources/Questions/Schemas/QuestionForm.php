<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Enums\QuestionStatus;
use App\Filament\Support\TranslatableOptionLabel;
use App\Models\Question;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(__('Question'))
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('subject')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('body')
                            ->label(__('Question Body'))
                            ->required()
                            ->rows(8),
                    ]),
                Section::make(__('Asker'))
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(QuestionStatus::class)
                            ->disableOptionWhen(fn (string $value): bool => $value === QuestionStatus::Answered->value)
                            ->disabled(fn (?Question $record): bool => $record?->status === QuestionStatus::Answered)
                            ->helperText(__('The "Answered" status is set automatically when an answer is published.'))
                            ->required(),
                        Select::make('topic_id')
                            ->label(__('Topic'))
                            ->relationship('topic', 'name')
                            ->getOptionLabelFromRecordUsing(TranslatableOptionLabel::for('name'))
                            ->preload(),
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(30),
                    ]),
            ]);
    }
}
