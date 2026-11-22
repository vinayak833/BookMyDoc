<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass-123']);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = $this->makeUser('patient');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_dashboard_redirects_each_role_to_its_own_area(): void
    {
        foreach (['patient', 'doctor', 'admin'] as $role) {
            $this->actingAs($this->makeUser($role))
                ->get('/dashboard')
                ->assertRedirect(route($role.'.dashboard'));
        }
    }

    public function test_each_role_can_open_its_own_dashboard(): void
    {
        foreach (['patient', 'doctor', 'admin'] as $role) {
            $this->actingAs($this->makeUser($role))
                ->get(route($role.'.dashboard'))
                ->assertOk();
        }
    }

    public function test_patient_cannot_open_admin_or_doctor_areas(): void
    {
        $patient = $this->makeUser('patient');

        $this->actingAs($patient)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($patient)->get(route('doctor.dashboard'))->assertForbidden();
    }

    public function test_doctor_cannot_open_admin_area(): void
    {
        $this->actingAs($this->makeUser('doctor'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('/login');
    }

    public function test_new_registrations_default_to_patient_role(): void
    {
        $this->post('/register', [
            'name' => 'New Patient',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertSame('patient', User::where('email', 'new@example.com')->first()->role);
    }
}
