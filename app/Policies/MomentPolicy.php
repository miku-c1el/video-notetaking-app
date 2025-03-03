<?php

namespace App\Policies;

use App\Models\Moment;
use App\Models\User;
use App\Models\Note;
use Illuminate\Auth\Access\Response;


class MomentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Moment $moment): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Note $note): bool
    {
        return $user->id === $note->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Moment $moment): bool
    {
        return $user->id === $moment->note->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Moment $moment): bool
    {
        return $user->id === $moment->note->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Moment $moment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Moment $moment): bool
    {
        //
    }
}
