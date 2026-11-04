<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared rules for CMS content: every panel user may read, admins and editors may write.
 */
abstract class ContentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Model $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageContent();
    }

    public function update(User $user, Model $model): bool
    {
        return $user->canManageContent();
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->canManageContent();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageContent();
    }

    public function reorder(User $user): bool
    {
        return $user->canManageContent();
    }
}
