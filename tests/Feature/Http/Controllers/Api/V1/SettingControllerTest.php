<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_resolves_values_by_setting_type(): void
    {
        Storage::fake('public');

        Setting::factory()->create(['key' => 'a_site_name', 'type' => SettingType::Text, 'value' => 'Dinas Contoh']);
        Setting::factory()->create(['key' => 'b_questions_enabled', 'type' => SettingType::Boolean, 'value' => '0']);
        Setting::factory()->create(['key' => 'c_office_hours', 'type' => SettingType::Json, 'value' => '[{"day":"Senin"}]']);
        Setting::factory()->create(['key' => 'd_site_logo', 'type' => SettingType::Image, 'value' => 'settings/logo.png']);
        Setting::factory()->create(['key' => 'e_empty_logo', 'type' => SettingType::Image, 'value' => null]);

        $response = $this->getJson(route('api.v1.settings.index'));

        $response->assertOk()
            ->assertJsonPath('data.0.value', 'Dinas Contoh')
            ->assertJsonPath('data.1.value', false)
            ->assertJsonPath('data.2.value', [['day' => 'Senin']])
            ->assertJsonPath('data.3.value', Storage::disk('public')->url('settings/logo.png'))
            ->assertJsonPath('data.4.value', null);
    }
}
