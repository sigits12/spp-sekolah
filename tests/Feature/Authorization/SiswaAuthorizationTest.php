<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SiswaAuthorizationTest extends TestCase
{
    public function test_admin_authentication(): void
    {
        $user = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('nama', 'admin');
            })
            ->firstOrFail();

        Sanctum::actingAs($user);

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_access_siswa_index(): void
    {
        $user = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('nama', 'admin');
            })
            ->firstOrFail();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/siswa-index');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_guest_cannot_access_siswa_index(): void
    {
        $response = $this->getJson('/api/v1/siswa-index');

        $response->assertStatus(401);
    }
}