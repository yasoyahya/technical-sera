<?php

namespace App\Actions\Address;

use App\Models\Address;

class ListAddress
{
    public function handle()
    {
        return Address::with('user')->get();
    }
}
