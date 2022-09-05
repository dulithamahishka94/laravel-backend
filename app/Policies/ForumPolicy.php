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

    /**
     * Policy for approve forum.
     *
     * @param User $user
     * @return bool
     */
    public function approveForum(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Policy for reject forum.
     *
     * @param User $user
     * @return bool
     */
    public function rejectForum(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Policy for post forums without approvals.
     *
     * @param User $user
     * @return bool
     */
    public function postWithoutApproval(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}
