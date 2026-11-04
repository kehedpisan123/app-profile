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

        $this->label('Tolak')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak pertanyaan')
            ->modalDescription('Pertanyaan yang ditolak tidak akan ditampilkan ke publik.')
            ->authorize('update')
            ->visible(fn (Question $record): bool => $record->status === QuestionStatus::Pending)
            ->successNotificationTitle('Pertanyaan ditolak')
            ->action(function (Question $record): void {
                $record->update(['status' => QuestionStatus::Rejected]);

                $this->success();
            });
    }
}
