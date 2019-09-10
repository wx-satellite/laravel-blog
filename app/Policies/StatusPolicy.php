<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
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


    // 只有用户自己的微博才能删除
    public function destroy(User $currentUser, Status $status) {
        return $currentUser->id === $status->user_id;
    }
}
