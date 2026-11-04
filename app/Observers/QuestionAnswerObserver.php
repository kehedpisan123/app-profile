<?php

namespace App\Observers;

use App\Models\QuestionAnswer;
use App\Notifications\QuestionAnsweredNotification;

class QuestionAnswerObserver
{
    /**
     * Handle the QuestionAnswer "saving" event.
     */
    public function saving(QuestionAnswer $questionAnswer): void
    {
        if ($questionAnswer->isPublished() && $questionAnswer->answered_at === null) {
            $questionAnswer->answered_at = now();
        }
    }

    /**
     * Handle the QuestionAnswer "created" event.
     */
    public function created(QuestionAnswer $questionAnswer): void
    {
        if ($questionAnswer->isPublished()) {
            $this->notifyAsker($questionAnswer);
        }
    }

    /**
     * Handle the QuestionAnswer "updated" event.
     */
    public function updated(QuestionAnswer $questionAnswer): void
    {
        if ($questionAnswer->isPublished() && $questionAnswer->wasChanged('status')) {
            $this->notifyAsker($questionAnswer);
        }
    }

    /**
     * Handle the QuestionAnswer "saved" event.
     */
    public function saved(QuestionAnswer $questionAnswer): void
    {
        $questionAnswer->question->syncStatusWithAnswers();
    }

    /**
     * Handle the QuestionAnswer "deleted" event.
     */
    public function deleted(QuestionAnswer $questionAnswer): void
    {
        $questionAnswer->question->syncStatusWithAnswers();
    }

    private function notifyAsker(QuestionAnswer $questionAnswer): void
    {
        $questionAnswer->question->notify(new QuestionAnsweredNotification($questionAnswer));
    }
}
