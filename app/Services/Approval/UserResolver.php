<?php

namespace App\Services\Approval;

use App\Models\ApprovalStep;
use App\Models\User;

class UserResolver
{

    public function resolve(
        ApprovalStep $step
    ): User {

        if ($step->user_id) {

            return User::findOrFail(
                $step->user_id
            );
        }

        if ($step->role_id) {

            return User::role(
                $step->role->name
            )->firstOrFail();
        }

        throw new \Exception(
            'No approver configured.'
        );
    }
}
