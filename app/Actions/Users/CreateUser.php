<?php

namespace App\Actions\Users;

use App\Models\User;

class CreateUser
{
    public function handle(array $data): User
    {
        return User::create($data);
    }
}
