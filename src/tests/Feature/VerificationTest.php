<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_verification_email()
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        event(new Registered($user));
        
        Notification::assertSentTo(
            $user,
            CustomVerifyEmail::class
        );
    }

    public function test_verification_show()
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));
        $response->assertStatus(200);
        $response->assertSee('https://mailtrap.io/inboxes/3400198/messages');
    }

    public function test_verification_redirect()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
