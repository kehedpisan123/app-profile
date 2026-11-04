<?php

namespace App\Filament\Resources\Questions\Actions;

use App\Enums\QuestionStatus;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class RejectQuestionAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Reject'))
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__('Reject question'))
            ->modalDescription(__('Rejected questions will not be shown to the public.'))
            ->authorize('update')
            ->visible(fn (Question $record): bool => $record->status === QuestionStatus::Pending)
            ->successNotificationTitle(__('Question rejected'))
            ->action(function (Question $record): void {
                $record->update(['status' => QuestionStatus::Rejected]);

                $this->success();
            });
    }
}
