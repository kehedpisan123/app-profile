<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Faq;
use App\Models\QuestionTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_active_faqs(): void
    {
        $active = Faq::factory()->create();
        Faq::factory()->inactive()->create();

        $response = $this->getJson(route('api.v1.faqs.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $active->id);
    }

    public function test_index_filters_faqs_by_topic_slug(): void
    {
        $topic = QuestionTopic::factory()->create(['slug' => 'perizinan']);
        $match = Faq::factory()->for($topic, 'topic')->create();
        Faq::factory()->create();

        $response = $this->getJson(route('api.v1.faqs.index', ['topic' => 'perizinan']));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id)
            ->assertJsonPath('data.0.topic.slug', 'perizinan');
    }
}
