<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_token_and_user(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123', 'role' => 'doctor']);

        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'secret-pass-123'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('user.role', 'doctor');
    }

    public function test_api_login_rejects_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);

        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'nope'])
            ->assertStatus(401);
    }

    public function test_api_login_validates_input(): void
    {
        $this->postJson('/api/v1/login', [])->assertStatus(422);
    }

    public function test_me_requires_a_token(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_me_returns_the_logged_in_user(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);
        $token = $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'secret-pass-123'])
            ->json('token');

        $this->withToken($token)->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('email', $user->email);
    }

    public function test_logout_deletes_the_token(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);
        $token = $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'secret-pass-123'])
            ->json('token');

        $this->withToken($token)->postJson('/api/v1/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_clinics_endpoint_lists_only_active_clinics(): void
    {
        Clinic::create(['name' => 'Active Clinic', 'address' => 'Bengaluru', 'is_active' => true]);
        Clinic::create(['name' => 'Closed Clinic', 'address' => 'Bengaluru', 'is_active' => false]);

        $this->getJson('/api/v1/clinics')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Active Clinic');
    }
}
