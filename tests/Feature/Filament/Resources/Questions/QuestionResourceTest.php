<?php

namespace Tests\Feature\Filament\Resources\Questions;

use App\Enums\QuestionAnswerStatus;
use App\Enums\QuestionStatus;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\Pages\ViewQuestion;
use App\Filament\Resources\Questions\RelationManagers\AnswersRelationManager;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\User;
use App\Notifications\QuestionAnsweredNotification;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class QuestionResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_publishes_answer_from_relation_manager_and_asker_is_notified(): void
    {
        Notification::fake();
        $editor = User::factory()->create();
        $question = Question::factory()->create();

        $this->actingAs($editor);

        Livewire::test(AnswersRelationManager::class, ['ownerRecord' => $question, 'pageClass' => ViewQuestion::class])
            ->callAction(TestAction::make('create')->table(), [
                'body' => '<p>Silakan datang ke loket 3.</p>',
                'status' => QuestionAnswerStatus::Published->value,
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(QuestionAnswer::class, [
            'question_id' => $question->id,
            'user_id' => $editor->id,
            'status' => QuestionAnswerStatus::Published->value,
        ]);
        $this->assertSame(QuestionStatus::Answered, $question->fresh()->status);
        Notification::assertSentTo($question, QuestionAnsweredNotification::class);
    }

    public function test_viewer_cannot_create_answers(): void
    {
        $question = Question::factory()->create();

        $this->actingAs(User::factory()->viewer()->create());

        Livewire::test(AnswersRelationManager::class, ['ownerRecord' => $question, 'pageClass' => ViewQuestion::class])
            ->assertActionHidden(TestAction::make('create')->table());
    }

    public function test_reject_action_marks_pending_question_rejected(): void
    {
        $question = Question::factory()->create();

        $this->actingAs(User::factory()->create());

        Livewire::test(ListQuestions::class)
            ->callAction(TestAction::make('reject')->table($question))
            ->assertNotified('Pertanyaan ditolak');

        $this->assertSame(QuestionStatus::Rejected, $question->fresh()->status);
    }

    public function test_reject_action_is_hidden_for_viewer(): void
    {
        $question = Question::factory()->create();

        $this->actingAs(User::factory()->viewer()->create());

        Livewire::test(ListQuestions::class)
            ->assertActionHidden(TestAction::make('reject')->table($question));
    }

    public function test_reject_action_is_hidden_for_answered_question(): void
    {
        $question = Question::factory()->answered()->create();

        $this->actingAs(User::factory()->create());

        Livewire::test(ListQuestions::class)
            ->assertActionHidden(TestAction::make('reject')->table($question));
    }
}
