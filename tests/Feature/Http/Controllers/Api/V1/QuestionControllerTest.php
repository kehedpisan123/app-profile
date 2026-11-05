<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\QuestionStatus;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_pending_question_and_returns_201(): void
    {
        $topic = QuestionTopic::factory()->create();

        $response = $this->postJson(route('api.v1.questions.store'), $this->validPayload($topic), ['REMOTE_ADDR' => '10.0.0.5']);

        $response->assertCreated()
            ->assertJsonPath('data.status', QuestionStatus::Pending->value)
            ->assertJsonPath('data.topic.id', $topic->id)
            ->assertJsonPath('message', 'Pertanyaan Anda telah kami terima dan akan segera ditinjau.')
            ->assertJsonMissingPath('data.email')
            ->assertJsonMissingPath('data.phone')
            ->assertJsonMissingPath('data.ip_address');

        $this->assertDatabaseHas(Question::class, [
            'topic_id' => $topic->id,
            'email' => 'warga@example.com',
            'status' => QuestionStatus::Pending->value,
            'ip_address' => '10.0.0.5',
        ]);
    }

    public function test_store_rejects_empty_payload_with_422(): void
    {
        $response = $this->postJson(route('api.v1.questions.store'), [], ['Accept-Language' => 'id']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['topic_id', 'name', 'email', 'subject', 'body'])
            ->assertJsonValidationErrorFor('name')
            ->assertJsonPath('errors.name.0', 'Nama wajib diisi.');

        $this->assertDatabaseCount(Question::class, 0);
    }

    public function test_store_rejects_inactive_topic_with_422(): void
    {
        $topic = QuestionTopic::factory()->inactive()->create();

        $response = $this->postJson(route('api.v1.questions.store'), $this->validPayload($topic), ['Accept-Language' => 'id']);

        $response->assertUnprocessable()
            ->assertJsonPath('errors.topic_id.0', 'Topik yang dipilih tidak valid.');
    }

    public function test_store_rejects_invalid_email_with_422(): void
    {
        $topic = QuestionTopic::factory()->create();

        $response = $this->postJson(route('api.v1.questions.store'), [
            ...$this->validPayload($topic),
            'email' => 'bukan-email',
        ], ['Accept-Language' => 'id']);

        $response->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'Surel harus berupa alamat surel yang valid.');
    }

    public function test_store_returns_429_after_three_submissions_per_minute(): void
    {
        $topic = QuestionTopic::factory()->create();

        foreach (range(1, 3) as $attempt) {
            $this->postJson(route('api.v1.questions.store'), $this->validPayload($topic))->assertCreated();
        }

        $this->postJson(route('api.v1.questions.store'), $this->validPayload($topic))->assertTooManyRequests();

        $this->assertDatabaseCount(Question::class, 3);
    }

    public function test_index_lists_only_answered_questions_with_published_answers(): void
    {
        $answered = Question::factory()->answered()->create();
        QuestionAnswer::factory()->published()->for($answered)->create(['body' => '<p>Jawaban resmi</p>']);
        QuestionAnswer::factory()->for($answered)->create(['body' => '<p>Draf internal</p>']);
        Question::factory()->create();
        Question::factory()->rejected()->create();

        $response = $this->getJson(route('api.v1.questions.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $answered->id)
            ->assertJsonCount(1, 'data.0.answers')
            ->assertJsonPath('data.0.answers.0.body', '<p>Jawaban resmi</p>')
            ->assertJsonMissingPath('data.0.email');
    }

    public function test_show_returns_404_for_pending_question(): void
    {
        $question = Question::factory()->create();

        $this->getJson(route('api.v1.questions.show', $question))->assertNotFound();
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(QuestionTopic $topic): array
    {
        return [
            'topic_id' => $topic->id,
            'name' => 'Budi Santoso',
            'email' => 'warga@example.com',
            'phone' => '081234567890',
            'subject' => 'Bagaimana cara mengurus surat keterangan?',
            'body' => 'Saya ingin mengetahui persyaratan pengurusan surat keterangan domisili.',
        ];
    }
}
