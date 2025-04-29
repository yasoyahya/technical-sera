<?php

namespace App\Actions\Users;
use App\Models\User;

class UpdateUser
{
    public function handle(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }
}
