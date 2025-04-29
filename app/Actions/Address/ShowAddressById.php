<?php

namespace App\Actions\Address;

use App\Models\Address;

class ShowAddressById
{
    public function handle(int $id): Address
    {
        return Address::with('user')->findOrFail($id);
    }
}
