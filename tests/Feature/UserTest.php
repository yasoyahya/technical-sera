<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'Test user',
            'email' => 'user@example.com',
        ];

        $user = User::create($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'Test user',
            'email' => 'user@example.com',
        ]);
    }

    public function it_can_update_a_user()
    {
        $user = User::factory()->create();

        $user->update([
            'name' => 'Test user',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test user',
        ]);
    }

    public function it_can_delete_a_user()
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
