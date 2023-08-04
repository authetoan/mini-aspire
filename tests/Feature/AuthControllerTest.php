<?php

// tests/Unit/AuthControllerTest.php

namespace Tests\Feature;

use App\Domain\Entities\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLoginWithValidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'), // You can use the bcrypt() function to hash the password.
        ]);

        $credentials = [
            'email' => 'user@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'token']);
    }

    public function testUserCannotLoginWithInvalidCredentials()
    {
        $credentials = [
            'email' => 'user@example.com',
            'password' => 'invalid_password',
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    public function testUserCannotLoginWithMissingCredentials()
    {
        $response = $this->postJson('/api/login');

        $response->assertStatus(401)
            ->assertJson(['message' => 'The email field is required.']);
    }
}
