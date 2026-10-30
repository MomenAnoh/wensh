<?php

namespace Tests\Feature\Api\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_save_optional_profile_details(): void
    {
        $user = User::factory()->create([
            'phone' => '+201000000005',
            'phone_verified_at' => now(),
        ]);
        $this->actingAs($user, 'api');

        $response = $this->putJson('/api/v1/user/profile', [
            'name' => 'Ahmed Mohamed',
            'email' => 'ahmed@example.com',
            'emergency_contact_phone' => '+201000000006',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Ahmed Mohamed')
            ->assertJsonPath('data.email', 'ahmed@example.com')
            ->assertJsonPath('data.emergency_contact_phone', '+201000000006')
            ->assertJsonPath('data.profile_status', 'completed');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ahmed Mohamed',
            'email' => 'ahmed@example.com',
            'profile_skipped_at' => null,
        ]);
    }

    public function test_user_can_skip_profile_completion(): void
    {
        $user = User::factory()->create([
            'phone' => '+201000000007',
            'phone_verified_at' => now(),
        ]);
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/user/profile/skip');

        $response->assertOk()
            ->assertJsonPath('data.profile_status', 'skipped');
        $this->assertNotNull($user->fresh()->profile_skipped_at);
    }

    public function test_profile_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/user/profile')->assertUnauthorized();
    }
}
