<?php

namespace App\Actions\Users;
use App\Models\User;

class ListUsers
{
    public function handle()
    {
        return User::with('addresses')->get();
    }
}
