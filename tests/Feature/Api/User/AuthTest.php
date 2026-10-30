<?php

namespace Tests\Feature\Api\User;

use App\Models\OtpChallenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_otp_returns_the_development_code_and_persists_only_a_hash(): void
    {
        $phone = '+201000000001';
        $response = $this->postJson('/api/v1/user/auth/otp/send', ['phone' => $phone]);

        $response->assertOk()
            ->assertJsonPath('message', 'The verification code has been sent.')
            ->assertJsonPath('data.expires_in', 300)
            ->assertJsonPath('data.otp', '123456');
        $this->assertSame('123456', $response->json('data.otp'));
        $this->assertDatabaseHas('otp_challenges', [
            'phone' => $phone,
            'attempts' => 0,
            'consumed_at' => null,
        ]);

        $challenge = OtpChallenge::query()->where('phone', $phone)->firstOrFail();
        $this->assertTrue(Hash::check('123456', $challenge->code_hash));
    }

    public function test_verifying_the_fixed_otp_creates_a_verified_user_and_returns_a_jwt(): void
    {
        $phone = '+201000000002';
        $this->postJson('/api/v1/user/auth/otp/send', ['phone' => $phone])->assertOk();

        $response = $this->postJson('/api/v1/user/auth/otp/verify', [
            'phone' => $phone,
            'code' => '123456',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'bearer')
            ->assertJsonPath('data.expires_in', null)
            ->assertJsonPath('data.user.phone', $phone)
            ->assertJsonPath('data.user.profile_status', 'pending');
        $token = $response->json('data.access_token');
        $this->assertNotEmpty($token);
        $this->assertDatabaseHas('users', ['phone' => $phone]);
        $this->assertNotNull(User::query()->where('phone', $phone)->firstOrFail()->phone_verified_at);
        $this->assertNotNull(OtpChallenge::query()->where('phone', $phone)->firstOrFail()->consumed_at);

        $this->travel(30)->days();

        $refreshedResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/user/auth/refresh');
        $refreshedResponse->assertOk()
            ->assertJsonPath('data.expires_in', null);

        $this->withHeader('Authorization', 'Bearer '.$refreshedResponse->json('data.access_token'))
            ->getJson('/api/v1/user/auth/me')
            ->assertOk()
            ->assertJsonPath('data.phone', $phone);
    }

    public function test_invalid_phone_and_otp_are_rejected_and_failed_attempts_are_counted(): void
    {
        $this->withHeader('Accept-Language', 'ar')
            ->postJson('/api/v1/user/auth/otp/send', ['phone' => '01000000003'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'صيغة رقم الهاتف غير صحيحة.')
            ->assertJsonPath('data', null)
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('trace');

        $phone = '+201000000003';
        $this->postJson('/api/v1/user/auth/otp/send', ['phone' => $phone])->assertOk();

        $this->withHeader('Accept-Language', 'en')
            ->postJson('/api/v1/user/auth/otp/verify', [
                'phone' => $phone,
                'code' => '000000',
            ])->assertUnprocessable()
            ->assertJsonPath('message', 'The verification code is invalid or has expired.')
            ->assertJsonPath('data', null)
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('trace');

        $this->assertDatabaseHas('otp_challenges', [
            'phone' => $phone,
            'attempts' => 1,
        ]);
        $this->assertDatabaseMissing('users', ['phone' => $phone]);
    }

    public function test_otp_send_is_rate_limited_per_phone(): void
    {
        $phone = '+201000000004';
        $this->postJson('/api/v1/user/auth/otp/send', ['phone' => $phone])->assertOk();

        $response = $this->postJson('/api/v1/user/auth/otp/send', ['phone' => $phone]);

        $response->assertTooManyRequests()
            ->assertJsonPath('message', 'Please wait before requesting another verification code.')
            ->assertJsonPath('data', null)
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('trace')
            ->assertJsonMissingPath('file');

        $this->assertSame(1, OtpChallenge::query()->where('phone', $phone)->count());
    }

    public function test_current_user_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/v1/user/auth/me')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'You must be signed in to continue.')
            ->assertJsonPath('data', null)
            ->assertJsonMissingPath('exception');
    }

    public function test_fixed_otp_delivery_is_disabled_outside_local_and_testing(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        $phone = '+201000000010';

        $response = $this->postJson('/api/v1/user/auth/otp/send', ['phone' => $phone]);

        $response->assertInternalServerError()
            ->assertJsonPath('message', 'An internal error occurred. Please try again later.')
            ->assertJsonPath('data', null)
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('file')
            ->assertJsonMissingPath('trace');

        $this->assertDatabaseMissing('otp_challenges', ['phone' => $phone]);
    }
}
