<?php

namespace App\Policies;

use App\Models\User;

class QuestionPolicy extends ContentPolicy
{
    /**
     * Questions are submitted by the public through the API, never from the panel.
     */
    public function create(User $user): bool
    {
        return false;
    }
}
