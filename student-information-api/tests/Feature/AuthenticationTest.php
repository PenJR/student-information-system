<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_view_profile_and_logout(): void
    {
        $role = Role::create(['name' => 'STUDENT']);
        $user = User::factory()->create(['email' => 'student@example.test', 'password' => Hash::make('secret123'), 'role_id' => $role->id]);

        $login = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'secret123']);
        $login->assertOk()->assertJsonPath('success', true)->assertJsonMissing(['password' => $user->password]);
        $token = $login->json('data.token');

        $this->withToken($token)->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.email', $user->email);
        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $role = Role::create(['name' => 'STUDENT']);
        User::factory()->create(['email' => 'student@example.test', 'role_id' => $role->id]);

        $this->postJson('/api/v1/auth/login', ['email' => 'student@example.test', 'password' => 'wrong'])
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'The provided credentials are invalid.');
    }

    public function test_current_user_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }
}