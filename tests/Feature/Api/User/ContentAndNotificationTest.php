<?php

namespace Tests\Feature\Api\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_endpoints_return_localized_terms_privacy_and_about_pages(): void
    {
        $termsResponse = $this->withHeader('Accept-Language', 'ar')
            ->getJson('/api/v1/user/content/terms-and-conditions');
        $termsResponse->assertOk()
            ->assertJsonPath('data.title', 'الشروط والأحكام')
            ->assertJsonPath('data.sections.0.title', 'استخدام الخدمة')
            ->assertJsonMissingPath('data.slug');

        $this->withHeader('Accept-Language', 'en')
            ->getJson('/api/v1/user/content/privacy-policy')
            ->assertOk()
            ->assertJsonPath('data.title', 'Privacy Policy')
            ->assertJsonMissingPath('data.slug');
        $this->withHeader('Accept-Language', 'en')
            ->getJson('/api/v1/user/content/about-us')
            ->assertOk()
            ->assertJsonPath('data.title', 'About Us')
            ->assertJsonMissingPath('data.slug');

        $this->getJson('/api/v1/user/content/not-a-page')->assertNotFound();
    }

    public function test_each_notification_preference_can_be_enabled_or_disabled(): void
    {
        $user = User::factory()->create([
            'phone' => '+201000000008',
            'phone_verified_at' => now(),
        ]);
        $this->actingAs($user, 'api');

        $this->patchJson('/api/v1/user/notifications/requests', ['enabled' => false])
            ->assertOk()
            ->assertJsonPath('data.requests_enabled', false);
        $this->patchJson('/api/v1/user/notifications/offers', ['enabled' => true])
            ->assertOk()
            ->assertJsonPath('data.offers_enabled', true);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'requests_notifications_enabled' => false,
            'offers_notifications_enabled' => true,
        ]);
    }

    public function test_notification_preference_requires_authentication_and_boolean_value(): void
    {
        $this->patchJson('/api/v1/user/notifications/requests', ['enabled' => true])
            ->assertUnauthorized();

        $user = User::factory()->create([
            'phone' => '+201000000009',
            'phone_verified_at' => now(),
        ]);
        $this->actingAs($user, 'api');

        $this->patchJson('/api/v1/user/notifications/requests', ['enabled' => 'sometimes'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'The notification status field must be true or false.')
            ->assertJsonPath('data', null)
            ->assertJsonMissingPath('exception');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'requests_notifications_enabled' => true,
        ]);
    }
}
