<?php

namespace Tests\Feature\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{UserRole, bool}>
     */
    public static function userManagementAccess(): array
    {
        return [
            'admin manages users' => [UserRole::Admin, true],
            'editor cannot manage users' => [UserRole::Editor, false],
            'viewer cannot manage users' => [UserRole::Viewer, false],
        ];
    }

    #[DataProvider('userManagementAccess')]
    public function test_user_management_is_admin_only(UserRole $role, bool $canManage): void
    {
        $user = User::factory()->create(['role' => $role]);
        $other = User::factory()->create();

        $this->assertSame($canManage, $user->can('viewAny', User::class));
        $this->assertSame($canManage, $user->can('create', User::class));
        $this->assertSame($canManage, $user->can('update', $other));
        $this->assertSame($canManage, $user->can('delete', $other));
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('delete', $admin));
    }
}
