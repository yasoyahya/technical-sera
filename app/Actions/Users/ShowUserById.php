<?php

namespace App\Actions\Users;
use App\Models\User;

class ShowUserById
{
    public function handle(int $id): User
    {
        return User::with('addresses')->findOrFail($id);
    }
}
