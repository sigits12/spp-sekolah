<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{

    public function test_database_connection(): void
    {
        $this->assertEquals(
            'pgsql',
            config('database.default')
        );
    }

    public function test_user_can_login_with_valid_credentials(): void
    {

        $user = User::with('role')
            ->where('email', 'admin@sekolah.id')
            ->firstOrFail();

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@sekolah.id',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Login berhasil.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => 'admin@sekolah.id',
                    'role' => 'admin',
                ],
            ]);

        $this->assertNotEmpty(
            $response->json('token')
        );
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@sekolah.id',
            'password' => 'password-salah',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Email atau password salah.',
            ]);
    }

    public function test_user_cannot_login_with_wrong_email(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'salah@sekolah.id',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Email atau password salah.',
            ]);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/v1/login', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'password',
            ]);
    }

    public function test_login_requires_valid_email(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'bukan-email',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }
}