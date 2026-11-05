<?php

namespace App\Filament\Resources\QuestionTopics\Pages;

use App\Filament\Concerns\TranslatesManagedRecords;
use App\Filament\Resources\QuestionTopics\QuestionTopicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ManageQuestionTopics extends ManageRecords
{
    use TranslatesManagedRecords;

    protected static string $resource = QuestionTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
