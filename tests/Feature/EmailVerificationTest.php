<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles so registration can assign the 'user' role
        $this->seed(RoleSeeder::class);
    }

    public function test_unverified_user_cannot_access_protected_routes()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'address' => 'Test Address',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/donasi');

        $response->assertRedirect('/email/verify');
    }

    public function test_verification_screen_can_be_rendered()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'address' => 'Test Address',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Email Anda');
    }

    public function test_email_can_be_verified()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'address' => 'Test Address',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $this->assertNull($user->email_verified_at);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Email Anda berhasil diverifikasi!');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_can_be_resent()
    {
        Notification::fake();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'address' => 'Test Address',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_sends_verification_email_and_redirects_to_verification_notice()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'phone' => '08987654321',
            'address' => 'New Address',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/email/verify');
        $response->assertSessionHas('success', 'Registrasi berhasil! Silakan verifikasi email Anda.');

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
