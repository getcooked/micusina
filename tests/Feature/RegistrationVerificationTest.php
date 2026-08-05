<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RegistrationOtpSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use Tests\TestCase;

class RegistrationVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_an_email_code_before_creating_an_account(): void
    {
        $this->mock(RegistrationOtpSender::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendEmail')->once()->andReturnTrue();
            $mock->shouldNotReceive('sendSms');
        });

        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '+639123456789',
            'address' => 'Bantayan, Cebu',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('register.verify'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'customer@example.com']);
        $this->assertNotEmpty(session('registration_verification.email_code'));
    }

    public function test_a_valid_email_code_creates_and_verifies_the_account(): void
    {
        $response = $this->withSession([
            'registration_verification' => [
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'phone' => '+639123456789',
                'address' => 'Bantayan, Cebu',
                'password' => Hash::make('secret123'),
                'email_code' => Hash::make('123456'),
                'expires_at' => now()->addMinutes(10)->timestamp,
            ],
        ])->post('/register/verify', [
            'email_code' => '123456',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $user = User::where('email', 'customer@example.com')->firstOrFail();
        $this->assertNotNull($user->email_verified_at);
    }
}
