<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Slide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlideControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_active_slides_within_schedule(): void
    {
        $showing = Slide::factory()->create([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);
        Slide::factory()->inactive()->create();
        Slide::factory()->expired()->create();
        Slide::factory()->upcoming()->create();

        $response = $this->getJson(route('api.v1.slides.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $showing->id);
    }

    public function test_index_includes_slide_ending_today(): void
    {
        $endingToday = Slide::factory()->create(['end_date' => today()]);

        $response = $this->getJson(route('api.v1.slides.index'));

        $response->assertOk()->assertJsonPath('data.0.id', $endingToday->id);
    }
}
