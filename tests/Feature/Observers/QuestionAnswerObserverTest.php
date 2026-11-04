<?php

namespace Tests\Feature\Observers;

use App\Enums\QuestionAnswerStatus;
use App\Enums\QuestionStatus;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Notifications\QuestionAnsweredNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QuestionAnswerObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishing_answer_marks_question_answered_and_notifies_asker(): void
    {
        Notification::fake();
        $question = Question::factory()->create();

        $answer = QuestionAnswer::factory()->published()->for($question)->create();

        $this->assertSame(QuestionStatus::Answered, $question->fresh()->status);
        $this->assertNotNull($answer->answered_at);
        Notification::assertSentTo($question, QuestionAnsweredNotification::class, fn (QuestionAnsweredNotification $notification): bool => $notification->answer->is($answer));
    }

    public function test_draft_answer_does_not_notify_or_change_question_status(): void
    {
        Notification::fake();
        $question = Question::factory()->create();

        $answer = QuestionAnswer::factory()->for($question)->create();

        $this->assertSame(QuestionStatus::Pending, $question->fresh()->status);
        $this->assertNull($answer->answered_at);
        Notification::assertNothingSent();
    }

    public function test_publishing_existing_draft_notifies_asker_once(): void
    {
        Notification::fake();
        $question = Question::factory()->create();
        $answer = QuestionAnswer::factory()->for($question)->create();

        $answer->update(['status' => QuestionAnswerStatus::Published]);
        $answer->update(['body' => '<p>Koreksi kecil</p>']);

        Notification::assertSentToTimes($question, QuestionAnsweredNotification::class, 1);
    }

    public function test_removing_last_published_answer_returns_question_to_pending(): void
    {
        Notification::fake();
        $question = Question::factory()->create();
        $answer = QuestionAnswer::factory()->published()->for($question)->create();

        $answer->delete();

        $this->assertSame(QuestionStatus::Pending, $question->fresh()->status);
    }

    public function test_unpublishing_answer_returns_question_to_pending(): void
    {
        Notification::fake();
        $question = Question::factory()->create();
        $answer = QuestionAnswer::factory()->published()->for($question)->create();

        $answer->update(['status' => QuestionAnswerStatus::Rejected]);

        $this->assertSame(QuestionStatus::Pending, $question->fresh()->status);
    }

    public function test_notification_mail_contains_question_subject_and_plain_answer(): void
    {
        $question = Question::factory()->make(['name' => 'Siti', 'subject' => 'Jam layanan']);
        $answer = QuestionAnswer::factory()->make(['body' => '<p>Buka pukul <strong>08.00</strong></p>']);
        $answer->setRelation('question', $question);

        $mail = (new QuestionAnsweredNotification($answer))->toMail($question);

        $this->assertSame('Pertanyaan Anda telah dijawab: Jam layanan', $mail->subject);
        $this->assertSame('Halo, Siti!', $mail->greeting);
        $this->assertContains('Buka pukul 08.00', $mail->introLines);
    }
}
