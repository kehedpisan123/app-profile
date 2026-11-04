<?php

namespace Tests\Feature\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_file_stores_file_details(): void
    {
        Storage::fake('public');
        $editor = User::factory()->create();

        $this->actingAs($editor);

        Livewire::test(CreateMedia::class)
            ->fillForm([
                'file_path' => UploadedFile::fake()->create('kantor.jpg', 120, 'image/jpeg'),
                'alt_text' => 'Gedung kantor',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $media = Media::query()->firstOrFail();

        $this->assertSame('kantor.jpg', $media->file_name);
        $this->assertSame('image/jpeg', $media->mime_type);
        $this->assertSame('public', $media->disk);
        $this->assertTrue($media->user->is($editor));
        Storage::disk('public')->assertExists($media->file_path);
        $this->assertSame(Storage::disk('public')->size($media->file_path), $media->file_size);
    }

    public function test_deleting_media_removes_stored_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/brosur.pdf', 'isi');
        $media = Media::factory()->create(['file_path' => 'media/brosur.pdf']);

        $media->delete();

        Storage::disk('public')->assertMissing('media/brosur.pdf');
    }
}
