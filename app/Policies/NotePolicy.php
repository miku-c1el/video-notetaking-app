<?php

namespace App\Policies;

use App\Models\User;

class NotePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, Note $note)
    {
        return $user->id === $note->user_id;
    }
}
