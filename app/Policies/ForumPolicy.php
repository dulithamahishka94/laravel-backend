<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function approveForum(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function rejectForum(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function postWithoutApproval(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}
