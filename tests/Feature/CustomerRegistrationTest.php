<?php

use App\Models\User;
use App\Notifications\SystemNotification;
use Database\Factories\CustomerFactory;
use Database\Factories\StaffFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;

test('Gmail SMTP accepts registration and resend verification and the code opens the customer dashboard', function (): void {
    if (getenv('LAVEA_GMAIL_SMTP_TEST') !== '1') {
        $this->markTestSkipped('Enable explicitly to send two verification emails to the configured Gmail sender.');
    }

    config(['mail.default' => 'smtp']);
    Mail::purge();
    $recipient = config('mail.mailers.smtp.username');
    expect($recipient)->not->toBeEmpty();

    $this->post(route('register.submit'), registrationPayload($recipient))
        ->assertRedirect(route('verification.notice'))->assertSessionHas('status')->assertSessionMissing('email_error');
    $customer = User::sole();
    expect($customer->hasVerifiedEmail())->toBeFalse();
    $this->get(route('customer.dashboard'))->assertRedirect(route('verification.notice'));
    $this->from(route('verification.notice'))->post(route('verification.send'))
        ->assertRedirect(route('verification.notice'))->assertSessionHas('status')->assertSessionMissing('email_error');

    $this->post(route('verification.verify'), ['verification_code' => $customer->fresh()->verification_code])->assertRedirect(route('customer.dashboard'));
    expect($customer->fresh()->hasVerifiedEmail())->toBeTrue();
    $this->get(route('customer.dashboard'))->assertOk()->assertSee('Your email has been verified successfully!');
});

beforeEach(function (): void {
    Schema::create('customers', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->string('address')->nullable();
        $table->timestamps();
    });
    Schema::create('services', function (Blueprint $table): void {
        $table->id();
        $table->string('service_name');
        $table->decimal('price', 10, 2);
        $table->string('image')->nullable();
        $table->text('description')->nullable();
        $table->timestamps();
    });
    Schema::create('orders', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('customer_id')->constrained();
        $table->foreignId('service_id')->constrained();
        $table->integer('quantity');
        $table->string('status')->default('Pending');
        $table->decimal('total_price', 10, 2)->default(0);
        $table->timestamps();
    });

    $this->artisan('migrate', ['--no-interaction' => true])->assertExitCode(0);
});

function registrationPayload(string $email): array
{
    return ['name' => 'New Customer', 'email' => $email, 'phone' => '09123456789', 'address' => '123 Laundry Street',
        'password' => 'StrongPassword123!', 'password_confirmation' => 'StrongPassword123!'];
}

test('new customer registration creates one normalized login and profile and sends verification', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    Notification::fake();
    $this->post(route('register.submit'), registrationPayload('  Fresh.Customer@Example.COM  '))
        ->assertRedirect(route('verification.notice'))->assertSessionHasNoErrors();
    $this->assertDatabaseCount('users', 2);
    $this->assertDatabaseCount('customers', 1);
    $this->assertDatabaseHas('users', ['email' => 'fresh.customer@example.com', 'role' => 'customer', 'email_verified_at' => null]);
    $this->assertDatabaseHas('customers', ['email' => 'fresh.customer@example.com', 'name' => 'New Customer', 'phone' => '09123456789', 'address' => '123 Laundry Street']);
    $customer = User::where('email', 'fresh.customer@example.com')->sole();
    $this->assertAuthenticatedAs($customer);
    expect(Hash::check('StrongPassword123!', $customer->password))->toBeTrue();
    expect($customer->verification_code)->toMatch('/^\d{6}$/');
    Notification::assertSentTo($admin, SystemNotification::class);
});

test('repeated registration resumes an unverified customer only with their correct password', function (): void {
    Notification::fake();
    Mail::fake();
    $user = User::factory()->unverified()->create(['role' => 'customer', 'email' => '  Existing@Example.COM  ', 'name' => 'Original Customer', 'password' => 'StrongPassword123!', 'verification_code' => '123456', 'verification_code_expires_at' => now()->addMinutes(10)]);
    $profile = CustomerFactory::new()->create(['email' => $user->email, 'name' => 'Original Customer']);
    $this->post(route('register.submit'), registrationPayload('existing@example.com'))
        ->assertRedirect(route('verification.notice'))->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('customers', 1);
    expect($user->fresh()->name)->toBe('Original Customer');
    expect($profile->fresh()->name)->toBe('Original Customer');
    expect($user->fresh()->verification_code)->toBe('123456');
    $this->get(route('verification.notice'))->assertOk()->assertSee('name="verification_code"', false);
    $this->post(route('register.submit'), registrationPayload('existing@example.com'))->assertRedirect(route('verification.notice'));
    $this->assertDatabaseCount('users', 1);
    Notification::assertNothingSent();
    Mail::assertNothingSent();
});

test('registration cannot resume verified customers or privileged accounts even with their password', function (string $role): void {
    $user = User::factory()->create(['role' => $role, 'email' => 'existing@example.com', 'password' => 'StrongPassword123!']);
    $this->post(route('register.submit'), registrationPayload($user->email))->assertSessionHasErrors('email');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 1);
})->with(['customer', 'admin', 'manager']);

test('failed admin registration notification does not leave a created customer on sign up', function (): void {
    Exceptions::fake();
    User::factory()->create(['role' => 'admin']);
    Notification::shouldReceive('send')->once()->andThrow(new RuntimeException('Notification unavailable'));
    $this->post(route('register.submit'), registrationPayload('fresh@example.com'))->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs(User::where('email', 'fresh@example.com')->sole());
    $this->assertDatabaseCount('customers', 1);
    Exceptions::assertReported(RuntimeException::class);
});

test('registration rejects emails owned by any login role including legacy accounts without profiles', function (string $role, string $storedEmail): void {
    $user = User::factory()->unverified()->create(['role' => $role, 'email' => $storedEmail]);
    Notification::fake();
    $this->post(route('register.submit'), registrationPayload('  Existing@Example.com  '))
        ->assertSessionHasErrors(['email' => 'This email is already associated with an existing account or customer/staff record. Sign in, reset your password, or contact the shop for help.']);
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('customers', 0);
    $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $storedEmail, 'role' => $role]);
    $this->assertGuest();
    Notification::assertNothingSent();
})->with(['customer', 'admin', 'staff', 'user'])->with(['existing@example.com', '  Existing@Example.COM  ']);

test('registration rejects profile-only customer and staff emails without overwriting their information', function (string $profile): void {
    $record = $profile === 'customers'
        ? CustomerFactory::new()->create(['email' => 'Existing@Example.com', 'name' => 'Existing Customer'])
        : StaffFactory::new()->create(['email' => 'Existing@Example.com', 'name' => 'Existing Staff']);
    Notification::fake();
    $this->post(route('register.submit'), registrationPayload('existing@example.com'))->assertSessionHasErrors('email');
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseHas($profile, ['id' => $record->id, 'email' => 'Existing@Example.com', 'name' => $record->name]);
    $this->assertGuest();
    Notification::assertNothingSent();
})->with(['customers', 'staff']);

test('password confirmation and invalid emails still prevent registration', function (string $field, mixed $value): void {
    Notification::fake();
    $this->post(route('register.submit'), array_replace(registrationPayload('new@example.com'), [$field => $value]))
        ->assertSessionHasErrors($field === 'password_confirmation' ? 'password' : $field);
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('customers', 0);
    Notification::assertNothingSent();
})->with([
    'invalid email' => ['email', 'not-an-email'],
    'array email' => ['email', ['new@example.com']],
    'missing confirmation' => ['password_confirmation', 'different-password'],
    'address longer than database column' => ['address', str_repeat('a', 256)],
]);

test('login works for every role with normalized input and existing mixed case emails', function (string $role, string $storedEmail): void {
    $user = User::factory()->create(['role' => $role, 'email' => $storedEmail, 'password' => 'StrongPassword123!']);
    $this->post(route('login.submit'), ['email' => '  EXISTING@example.COM ', 'password' => 'StrongPassword123!'])
        ->assertRedirect(route($role === 'user' ? 'customer.dashboard' : $role.'.dashboard'));
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseCount('users', 1);
})->with(['admin', 'manager', 'customer'])->with(['Existing@Example.com', '  Existing@Example.com  ']);

test('unverified customers still must verify their email after login', function (): void {
    $user = User::factory()->unverified()->create(['role' => 'customer', 'email' => 'verify@example.com', 'password' => 'StrongPassword123!']);
    $this->post(route('login.submit'), ['email' => 'VERIFY@example.com', 'password' => 'StrongPassword123!'])
        ->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs($user);
});

test('wrong passwords remain rejected after email normalization', function (): void {
    User::factory()->create(['role' => 'customer', 'email' => 'existing@example.com']);
    $this->post(route('login.submit'), ['email' => 'EXISTING@example.com', 'password' => 'incorrect'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('customer verification codes mark the account verified and allow the dashboard', function (): void {
    $user = User::factory()->unverified()->create(['role' => 'customer', 'verification_code' => '123456', 'verification_code_expires_at' => now()->addMinutes(10)]);
    $this->actingAs($user)->post(route('verification.verify'), ['verification_code' => '123456'])->assertRedirect(route('customer.dashboard'));
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    expect($user->fresh()->verification_code)->toBeNull();
    $this->get(route('customer.dashboard'))->assertOk();
});

test('email reset code unlocks password change and cannot be reused', function (): void {
    $user = User::factory()->create(['role' => 'customer', 'email' => 'otp@example.com', 'email_verified_at' => null]);
    $code = null;
    Mail::shouldReceive('raw')->once()->withArgs(function (string $text, $callback) use (&$code): bool {
        preg_match('/\b[0-9]{6}\b/', $text, $matches);
        $code = $matches[0];

        return true;
    });
    $this->post(route('password.code.send'), ['email' => $user->email])->assertRedirect(route('password.code'));
    $this->post(route('password.code.verify'), ['code' => $code])->assertRedirect(route('password.code.new'));
    $response = $this->get(route('password.code.new'))->assertOk()->assertViewIs('auth.reset-password')->assertSee('New Password')->assertSee('Confirm New Password');
    $this->post(route('password.update'), ['email' => $user->email, 'token' => $response->viewData('token'), 'password' => 'New-password-123', 'password_confirmation' => 'New-password-123'])->assertRedirect(route('customer.dashboard'));
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    expect(Hash::check('New-password-123', $user->fresh()->password))->toBeTrue();
    expect(session()->has('password_reset_verified'))->toBeFalse();
});

test('expired or exhausted reset codes do not unlock password change', function (int $expiry, int $attempts): void {
    $this->withSession(['password_reset_code' => ['email' => 'otp@example.com', 'hash' => Hash::make('123456'), 'expires_at' => $expiry, 'attempts' => $attempts]])
        ->post(route('password.code.verify'), ['code' => '123456'])->assertRedirect(route('password.request'))->assertSessionMissing('password_reset_code');
})->with([fn () => [now()->subMinute()->timestamp, 0], fn () => [now()->addMinutes(10)->timestamp, 5]]);

test('password reset email uses the configured application address', function (): void {
    config(['app.url' => 'http://lavea.test']);
    $user = User::factory()->create();
    $message = (new ResetPassword('test-reset-token'))->toMail($user);

    expect($message->actionUrl)->toStartWith('http://lavea.test/reset-password/test-reset-token')
        ->toContain('email=');
    $this->get($message->actionUrl)->assertOk()->assertSee('test-reset-token');
});

test('password reset emails find existing accounts using normalized input', function (): void {
    $user = User::factory()->create(['role' => 'customer', 'email' => 'Existing@Example.com']);
    Notification::fake();
    $this->post(route('password.email'), ['email' => '  EXISTING@example.COM '])->assertSessionHas('status');
    Notification::assertSentTo($user, ResetPassword::class);
    $this->assertDatabaseHas('password_reset_tokens', ['email' => 'Existing@Example.com']);
});

test('password reset keeps working with normalized input and the existing stored email', function (): void {
    $user = User::factory()->create(['role' => 'customer', 'email' => 'Existing@Example.com']);
    $token = Password::createToken($user);
    $this->post(route('password.update'), ['email' => '  EXISTING@example.COM ', 'token' => $token,
        'password' => 'NewStrongPassword123!', 'password_confirmation' => 'NewStrongPassword123!'])
        ->assertRedirect(route('login'))->assertSessionHasNoErrors();
    expect(Hash::check('NewStrongPassword123!', $user->fresh()->password))->toBeTrue();
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'Existing@Example.com']);
});

test('password reset tokens alone do not reserve an email for registration', function (): void {
    DB::table('password_reset_tokens')->insert(['email' => 'new@example.com', 'token' => 'old-unused-token', 'created_at' => now()]);
    Notification::fake();
    $this->post(route('register.submit'), registrationPayload('new@example.com'))->assertRedirect(route('verification.notice'));
    $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => 'customer']);
    expect(User::where('email', 'new@example.com')->sole()->verification_code)->toMatch('/^\d{6}$/');
});

test('resending verification emails a new code without creating accounts', function (): void {
    $user = User::factory()->unverified()->create(['role' => 'customer']);
    Notification::fake();
    $this->actingAs($user)->from(route('verification.notice'))->post(route('verification.send'))
        ->assertRedirect(route('verification.notice'))->assertSessionHas('status', 'A new verification code has been sent to your email.');
    expect($user->fresh()->verification_code)->toMatch('/^\d{6}$/');
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('customers', 0);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('failed verification sending is reported and keeps the customer unverified', function (string $flow): void {
    Exceptions::fake();
    Mail::shouldReceive('raw')->once()->andThrow(new RuntimeException('SMTP authentication failed'));
    if ($flow === 'registration') {
        $response = $this->post(route('register.submit'), registrationPayload('new@example.com'));
    } else {
        $this->actingAs(User::factory()->unverified()->create(['role' => 'customer']));
        $response = $this->from(route('verification.notice'))->post(route('verification.send'));
    }
    $response->assertRedirect(route('verification.notice'))->assertSessionHas('email_error');
    Exceptions::assertReported(RuntimeException::class);
    $this->assertDatabaseCount('users', 1);
    expect(User::sole()->hasVerifiedEmail())->toBeFalse();
    $this->assertAuthenticated();
})->with(['registration', 'resend']);

test('verification codes cannot verify the wrong account or an incorrect code', function (string $invalid): void {
    $user = User::factory()->unverified()->create(['role' => 'customer', 'verification_code' => '123456', 'verification_code_expires_at' => now()->addMinutes(10)]);
    $other = User::factory()->unverified()->create(['role' => 'customer', 'verification_code' => '654321', 'verification_code_expires_at' => now()->addMinutes(10)]);
    $code = $invalid === 'account' ? $other->verification_code : '999999';
    $this->actingAs($user)->from(route('verification.notice'))->post(route('verification.verify'), ['verification_code' => $code])
        ->assertSessionHasErrors('verification_code');
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    expect($other->fresh()->hasVerifiedEmail())->toBeFalse();
})->with(['account', 'email']);

test('expired verification codes are rejected', function (): void {
    $user = User::factory()->unverified()->create(['role' => 'customer', 'verification_code' => '123456', 'verification_code_expires_at' => now()->subMinute()]);
    $this->actingAs($user)->from(route('verification.notice'))->post(route('verification.verify'), ['verification_code' => '123456'])
        ->assertSessionHasErrors(['verification_code' => 'The verification code has expired. Please request a new code.']);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verified customers are redirected instead of receiving more verification mail', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    Notification::fake();
    $this->actingAs($user)->post(route('verification.send'))->assertRedirect(route('customer.dashboard'));
    Notification::assertNothingSent();
});

test('registration renders and delivers the real verification notification to the configured mail transport', function (): void {
    $this->post(route('register.submit'), registrationPayload('new@example.com'))
        ->assertRedirect(route('verification.notice'))->assertSessionHasNoErrors()->assertSessionMissing('email_error');
    $messages = Mail::mailer()->getSymfonyTransport()->messages();
    expect($messages)->toHaveCount(1);
    $email = $messages->first()->getOriginalMessage();
    expect($email->getSubject())->toBe('LAVEA Email Verification Code');
    expect($email->getTo()[0]->getAddress())->toBe('new@example.com');
    expect($email->getTextBody())->toContain(User::sole()->verification_code, 'expire in 10 minutes');
    expect(User::sole()->hasVerifiedEmail())->toBeFalse();

    $this->get(route('customer.dashboard'))->assertRedirect(route('verification.notice'));
    $this->from(route('verification.notice'))->post(route('verification.send'))
        ->assertRedirect(route('verification.notice'))->assertSessionHas('status')->assertSessionMissing('email_error');
    expect(Mail::mailer()->getSymfonyTransport()->messages())->toHaveCount(2);

    $customer = User::sole();
    $this->post(route('verification.verify'), ['verification_code' => $customer->fresh()->verification_code])->assertRedirect(route('customer.dashboard'));
    expect($customer->fresh()->hasVerifiedEmail())->toBeTrue();
    $this->get(route('customer.dashboard'))->assertOk()->assertSee('Your email has been verified successfully!');
});
