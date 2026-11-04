<?php

namespace Tests\Feature\Policies;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\Question;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContentPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{UserRole, bool}>
     */
    public static function contentWriteAccess(): array
    {
        return [
            'admin can write' => [UserRole::Admin, true],
            'editor can write' => [UserRole::Editor, true],
            'viewer cannot write' => [UserRole::Viewer, false],
        ];
    }

    #[DataProvider('contentWriteAccess')]
    public function test_content_write_abilities_follow_role(UserRole $role, bool $canWrite): void
    {
        $user = User::factory()->create(['role' => $role]);
        $post = Post::factory()->create();

        $this->assertTrue($user->can('viewAny', Post::class));
        $this->assertTrue($user->can('view', $post));
        $this->assertSame($canWrite, $user->can('create', Post::class));
        $this->assertSame($canWrite, $user->can('update', $post));
        $this->assertSame($canWrite, $user->can('delete', $post));
        $this->assertSame($canWrite, $user->can('deleteAny', Post::class));
        $this->assertSame($canWrite, $user->can('reorder', Post::class));
    }

    public function test_questions_cannot_be_created_from_panel_even_by_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('create', Question::class));
    }

    /**
     * @return array<string, array{UserRole, bool}>
     */
    public static function settingWriteAccess(): array
    {
        return [
            'admin can manage settings' => [UserRole::Admin, true],
            'editor cannot manage settings' => [UserRole::Editor, false],
            'viewer cannot manage settings' => [UserRole::Viewer, false],
        ];
    }

    #[DataProvider('settingWriteAccess')]
    public function test_setting_write_abilities_are_admin_only(UserRole $role, bool $canWrite): void
    {
        $user = User::factory()->create(['role' => $role]);
        $setting = Setting::factory()->create();

        $this->assertTrue($user->can('viewAny', Setting::class));
        $this->assertSame($canWrite, $user->can('create', Setting::class));
        $this->assertSame($canWrite, $user->can('update', $setting));
        $this->assertSame($canWrite, $user->can('delete', $setting));
    }
}
