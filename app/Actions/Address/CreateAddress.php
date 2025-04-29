<?php

namespace App\Actions\Address;

use App\Models\Address;

class CreateAddress
{
    public function handle(array $data): Address
    {
        return Address::create($data);
    }
}
