<?php

use App\Models\User;
use App\Notifications\SystemNotification;
use Database\Factories\CustomerFactory;
use Database\Factories\StaffFactory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

/**
 * Match the existing isolated SQLite setup for missing legacy base migrations.
 */
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

function createSettingsAccount(string $role): User
{
    $user = User::factory()->create(['role' => $role]);

    if (in_array($role, ['customer', 'user'])) {
        CustomerFactory::new()->create(['email' => $user->email, 'name' => $user->name]);
    }

    if ($role === 'staff') {
        StaffFactory::new()->create(['email' => $user->email, 'name' => $user->name]);
    }

    return $user;
}

test('each role opens its own settings with personal sections and sidebar link', function (string $role): void {
    $user = createSettingsAccount($role);
    $portal = $role === 'user' ? 'customer' : $role;

    $this->actingAs($user)->get(route($portal.'.settings'))
        ->assertOk()->assertSee('Profile Information')->assertSee('Security')
        ->assertSee('Appearance')->assertSee('Notifications')
        ->assertSee('href="'.route($portal.'.settings').'"', false)
        ->assertSee('action="'.route($portal.'.settings.profile').'"', false)
        ->assertSee('css/lavea-settings.css', false);
})->with(['admin', 'staff', 'customer', 'user']);

test('personal settings exclude shop and administrative controls', function (string $role): void {
    $user = createSettingsAccount($role);
    $portal = $role === 'user' ? 'customer' : $role;

    $this->actingAs($user)->get(route($portal.'.settings'))
        ->assertDontSee('Shop Information')->assertDontSee('admin/settings')
        ->assertDontSee('name="role"', false)->assertDontSee('name="permissions"', false);
})->with(['staff', 'customer', 'user']);

test('admin settings retain the existing shop section', function (): void {
    $user = createSettingsAccount('admin');

    $this->actingAs($user)->get(route('admin.settings'))
        ->assertSee('Shop Information')->assertSee('action="'.route('admin.settings.shop').'"', false);
});

test('guests cannot read or write settings', function (string $role, string $section): void {
    if ($section === 'index') {
        $response = $this->get(route($role.'.settings'));
    } else {
        $response = $this->post(route($role.'.settings.'.$section), []);
    }

    $response->assertRedirect(route('login'));
})->with(['admin', 'staff', 'customer'])->with(['index', 'profile', 'password', 'appearance', 'notifications']);

test('role middleware blocks settings reads and writes belonging to another role', function (string $role, string $targetRole, string $section): void {
    $user = createSettingsAccount($role);
    $portal = $role === 'user' ? 'customer' : $role;
    $original = $user->fresh()->getAttributes();
    $payload = ['name' => 'Unauthorized', 'email' => 'unauthorized@example.com', 'theme' => 'dark', 'show_notification_badge' => 0, 'current_password' => 'password', 'password' => 'unauthorized-password', 'password_confirmation' => 'unauthorized-password'];

    $response = $section === 'index'
        ? $this->actingAs($user)->get(route($targetRole.'.settings'))
        : $this->actingAs($user)->post(route($targetRole.'.settings.'.$section), $payload);

    $response->assertRedirect(route($portal.'.dashboard'))->assertCookieMissing('lavea_theme');
    expect($user->fresh()->getAttributes())->toBe($original);
})->with([
    ['admin', 'staff'], ['admin', 'customer'],
    ['staff', 'admin'], ['staff', 'customer'],
    ['customer', 'admin'], ['customer', 'staff'],
    ['user', 'admin'], ['user', 'staff'],
])->with(['index', 'profile', 'password', 'appearance', 'notifications']);

test('unverified customer accounts cannot read or write settings', function (string $role, string $section): void {
    $user = User::factory()->unverified()->create(['role' => $role]);

    $response = $section === 'index'
        ? $this->actingAs($user)->get(route('customer.settings'))
        : $this->actingAs($user)->post(route('customer.settings.'.$section), []);

    $response->assertRedirect(route('verification.notice'));
})->with(['customer', 'user'])->with(['index', 'profile', 'password', 'appearance', 'notifications']);

test('profile updates ignore account identifiers roles and protected fields', function (string $role): void {
    $user = createSettingsAccount($role);
    $other = User::factory()->create(['name' => 'Other Account', 'role' => 'admin']);
    $portal = $role === 'user' ? 'customer' : $role;
    $originalPassword = $user->password;
    $originalVerification = $user->email_verified_at;

    $this->actingAs($user)->post(route($portal.'.settings.profile'), [
        'name' => 'Updated Account', 'email' => $user->email,
        'phone' => '09123456789', 'address' => '123 Laundry Street',
        'id' => $other->id, 'user_id' => $other->id, 'customer_id' => 999,
        'staff_id' => 999, 'role' => 'admin', 'password' => 'injected-password',
        'email_verified_at' => null, 'show_notification_badge' => 0,
    ])->assertRedirect(route($portal.'.settings'))->assertSessionHas('profile_success');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Account', 'email' => $user->email, 'role' => $role, 'show_notification_badge' => 1]);
    $this->assertDatabaseHas('users', ['id' => $other->id, 'name' => 'Other Account']);
    expect($user->fresh()->password)->toBe($originalPassword);
    expect($user->fresh()->email_verified_at->equalTo($originalVerification))->toBeTrue();
})->with(['admin', 'staff', 'customer', 'user']);

test('customer contact updates affect only their existing profile', function (): void {
    $user = createSettingsAccount('customer');
    $other = CustomerFactory::new()->create(['name' => 'Other Customer', 'phone' => '09000000000']);
    $customer = $user->customerProfile;

    $this->actingAs($user)->post(route('customer.settings.profile'), [
        'name' => 'Updated Customer', 'email' => $user->email,
        'phone' => '09123456789', 'address' => '123 Laundry Street', 'customer_id' => $other->id,
    ])->assertRedirect(route('customer.settings'));

    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Updated Customer', 'email' => $user->email, 'phone' => '09123456789', 'address' => '123 Laundry Street']);
    $this->assertDatabaseHas('customers', ['id' => $other->id, 'name' => 'Other Customer', 'phone' => '09000000000']);
});

test('staff email changes keep their staff record linked without changing job controls', function (): void {
    $user = createSettingsAccount('staff');
    $staff = $user->staffProfile;
    $other = StaffFactory::new()->create(['name' => 'Other Staff']);

    $this->actingAs($user)->post(route('staff.settings.profile'), [
        'name' => 'Updated Staff', 'email' => 'updated.staff@example.com',
        'staff_id' => $other->id, 'role' => 'Manager', 'status' => 'Inactive', 'phone' => '000',
    ])->assertRedirect(route('staff.settings'));

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Staff', 'email' => 'updated.staff@example.com', 'role' => 'staff']);
    $this->assertDatabaseHas('staff', ['id' => $staff->id, 'name' => 'Updated Staff', 'email' => 'updated.staff@example.com', 'role' => 'Staff', 'status' => 'Active', 'phone' => $staff->phone]);
    $this->assertDatabaseHas('staff', ['id' => $other->id, 'name' => 'Other Staff']);
    expect($user->fresh()->staffProfile->id)->toBe($staff->id);
});

test('customer email changes preserve the customer link and require fresh verification', function (string $role): void {
    $user = createSettingsAccount($role);
    $customer = $user->customerProfile;
    Notification::fake([VerifyEmail::class]);

    $this->actingAs($user)->post(route('customer.settings.profile'), [
        'name' => 'Updated Customer', 'email' => 'updated.customer@example.com',
        'phone' => '09123456789', 'address' => '123 Laundry Street',
    ])->assertRedirect(route('verification.notice'))->assertSessionHas('status', 'Your profile was saved. Please verify your new email address.');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'updated.customer@example.com', 'email_verified_at' => null]);
    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'email' => 'updated.customer@example.com']);
    expect($user->fresh()->customerProfile->id)->toBe($customer->id);
    Notification::assertSentTo($user, VerifyEmail::class);
    $this->get(route('customer.settings'))->assertRedirect(route('verification.notice'));
})->with(['customer', 'user']);

test('email delivery failures preserve the saved profile and the verification requirement', function (): void {
    $user = createSettingsAccount('customer');
    Exceptions::fake();
    Notification::shouldReceive('send')->once()->andThrow(new RuntimeException('Email delivery failed'));

    $this->actingAs($user)->post(route('customer.settings.profile'), [
        'name' => $user->name, 'email' => 'updated.customer@example.com',
        'phone' => '09123456789', 'address' => '123 Laundry Street',
    ])->assertRedirect(route('verification.notice'))->assertSessionHas('email_error');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'updated.customer@example.com', 'email_verified_at' => null]);
    $this->assertDatabaseHas('customers', ['email' => 'updated.customer@example.com']);
    Exceptions::assertReported(RuntimeException::class);
});

test('personal settings still work when no linked profile exists', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);
    $portal = $role === 'user' ? 'customer' : $role;

    $this->actingAs($user)->get(route($portal.'.settings'))->assertOk()->assertDontSee('name="phone"', false);
    $this->post(route($portal.'.settings.profile'), ['name' => 'Updated Name', 'email' => $user->email, 'phone' => 'injected', 'address' => 'injected'])
        ->assertRedirect(route($portal.'.settings'));

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    $this->assertDatabaseCount('customers', 0);
    $this->assertDatabaseCount('staff', 0);
})->with(['staff', 'customer', 'user']);

test('profile updates reject an email already belonging to a user customer or staff record', function (string $table): void {
    $user = createSettingsAccount('customer');
    match ($table) {
        'users' => User::factory()->create(['email' => 'taken@example.com']),
        'customers' => CustomerFactory::new()->create(['email' => 'taken@example.com']),
        'staff' => StaffFactory::new()->create(['email' => 'taken@example.com']),
    };

    $this->actingAs($user)->post(route('customer.settings.profile'), [
        'name' => 'Invalid Update', 'email' => 'taken@example.com',
        'phone' => '09123456789', 'address' => '123 Laundry Street',
    ])->assertSessionHasErrors(['email' => 'The email has already been taken.']);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
    $this->assertDatabaseHas('customers', ['id' => $user->customerProfile->id, 'email' => $user->email]);
})->with(['users', 'customers', 'staff']);

test('profile validation rejects invalid account and customer contact details', function (string $field, mixed $value, string $message): void {
    $user = createSettingsAccount('customer');
    $details = ['name' => 'Updated Name', 'email' => $user->email, 'phone' => '09123456789', 'address' => '123 Laundry Street'];
    $details[$field] = $value;

    $this->actingAs($user)->post(route('customer.settings.profile'), $details)
        ->assertSessionHasErrors([$field => $message]);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
})->with([
    'missing name' => ['name', '', 'The name field is required.'],
    'long name' => ['name', str_repeat('a', 256), 'The name field must not be greater than 255 characters.'],
    'invalid name type' => ['name', ['invalid'], 'The name field must be a string.'],
    'missing email' => ['email', '', 'The email field is required.'],
    'invalid email' => ['email', 'invalid', 'The email field must be a valid email address.'],
    'long email' => ['email', str_repeat('a', 250).'@example.com', 'The email field must not be greater than 255 characters.'],
    'missing phone' => ['phone', '', 'The phone field is required.'],
    'long phone' => ['phone', str_repeat('1', 21), 'The phone field must not be greater than 20 characters.'],
    'invalid phone type' => ['phone', ['invalid'], 'The phone field must be a string.'],
    'missing address' => ['address', '', 'The address field is required.'],
    'long address' => ['address', str_repeat('a', 256), 'The address field must not be greater than 255 characters.'],
    'invalid address type' => ['address', ['invalid'], 'The address field must be a string.'],
]);

test('password changes verify the current password hash the new password and affect only the signed in account', function (string $role): void {
    $user = createSettingsAccount($role);
    $other = User::factory()->create(['role' => 'admin']);
    $portal = $role === 'user' ? 'customer' : $role;
    $otherPassword = $other->password;
    $rememberToken = $user->remember_token;

    $this->actingAs($user)->post(route($portal.'.settings.password'), [
        'current_password' => 'password', 'password' => 'new-account-password',
        'password_confirmation' => 'new-account-password', 'user_id' => $other->id,
    ])->assertRedirect(route($portal.'.settings'))->assertSessionHas('password_success', 'Password changed successfully.');

    expect(Hash::check('new-account-password', $user->fresh()->password))->toBeTrue();
    expect(Hash::check('password', $user->fresh()->password))->toBeFalse();
    expect($user->fresh()->remember_token)->not->toBe($rememberToken);
    expect($other->fresh()->password)->toBe($otherPassword);
})->with(['admin', 'staff', 'customer', 'user']);

test('password validation preserves the existing password on failure', function (string $role, array $details, string $field, string $message): void {
    $user = createSettingsAccount($role);
    $portal = $role === 'user' ? 'customer' : $role;
    $originalPassword = $user->password;

    $this->actingAs($user)->post(route($portal.'.settings.password'), $details)
        ->assertSessionHasErrors([$field => $message]);

    expect($user->fresh()->password)->toBe($originalPassword);
})->with(['admin', 'staff', 'customer', 'user'])->with([
    'incorrect current password' => [['current_password' => 'incorrect', 'password' => 'new-password', 'password_confirmation' => 'new-password'], 'current_password', 'The password is incorrect.'],
    'missing current password' => [['password' => 'new-password', 'password_confirmation' => 'new-password'], 'current_password', 'The current password field is required.'],
    'missing new password' => [['current_password' => 'password'], 'password', 'The password field is required.'],
    'short password' => [['current_password' => 'password', 'password' => 'short', 'password_confirmation' => 'short'], 'password', 'The password field must be at least 8 characters.'],
    'mismatched confirmation' => [['current_password' => 'password', 'password' => 'new-password', 'password_confirmation' => 'different-password'], 'password', 'The password field confirmation does not match.'],
]);

test('appearance saves the existing theme cookie and renders the selected role theme', function (string $role, string $theme): void {
    $user = createSettingsAccount($role);
    $portal = $role === 'user' ? 'customer' : $role;

    $this->actingAs($user)->post(route($portal.'.settings.appearance'), ['theme' => $theme])
        ->assertRedirect(route($portal.'.settings'))->assertPlainCookie('lavea_theme', $theme);
    $response = $this->withUnencryptedCookie('lavea_theme', $theme)->get(route($portal.'.settings'));

    $response->assertOk()->assertSee(ucfirst($theme).' Mode is selected.');
    if ($theme === 'dark') {
        $response->assertSee('css/lavea-theme.css', false);
    } else {
        $response->assertDontSee('css/lavea-theme.css', false);
    }
})->with(['admin', 'staff', 'customer', 'user'])->with(['light', 'dark']);

test('invalid appearance values cannot change the theme cookie', function (mixed $theme, string $message): void {
    $user = createSettingsAccount('staff');

    $this->actingAs($user)->post(route('staff.settings.appearance'), ['theme' => $theme])
        ->assertSessionHasErrors(['theme' => $message])->assertCookieMissing('lavea_theme');
})->with([
    ['sepia', 'The selected theme is invalid.'],
    ['', 'The theme field is required.'],
]);

test('notification preferences affect only the account badge and preserve its inbox', function (string $role, int $showBadge): void {
    $user = createSettingsAccount($role);
    $other = User::factory()->create(['role' => 'admin']);
    $portal = $role === 'user' ? 'customer' : $role;
    $user->notify(new SystemNotification('Laundry Update', 'Your update is ready.'));

    $this->actingAs($user)->post(route($portal.'.settings.notifications'), [
        'show_notification_badge' => $showBadge, 'user_id' => $other->id,
    ])->assertRedirect(route($portal.'.settings'))->assertSessionHas('notification_success', 'Notification preferences saved.');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'show_notification_badge' => $showBadge]);
    $this->assertDatabaseHas('users', ['id' => $other->id, 'show_notification_badge' => 1]);
    $this->assertDatabaseHas('notifications', ['notifiable_id' => $user->id, 'read_at' => null]);
    $response = $this->get(route($portal.'.settings'))->assertSee('Laundry Update')->assertSee('Mark as read');
    if ($showBadge === 1) {
        $response->assertSee('class="lavea-notification-count"', false);
    } else {
        $response->assertDontSee('class="lavea-notification-count"', false);
    }
})->with(['admin', 'staff', 'customer', 'user'])->with([0, 1]);

test('notification preferences reject missing or invalid values without updating the account', function (array $details, string $message): void {
    $user = createSettingsAccount('customer');

    $this->actingAs($user)->post(route('customer.settings.notifications'), $details)
        ->assertSessionHasErrors(['show_notification_badge' => $message]);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'show_notification_badge' => 1]);
})->with([
    [[], 'The show notification badge field is required.'],
    [['show_notification_badge' => 'invalid'], 'The show notification badge field must be true or false.'],
]);

test('only admins can save existing shop information', function (bool $existing): void {
    $user = createSettingsAccount('admin');
    if ($existing) {
        DB::table('shop_settings')->insert(['id' => 1, 'shop_name' => 'Old Shop']);
    }

    $this->actingAs($user)->post(route('admin.settings.shop'), [
        'shop_name' => 'LAVEA Laundry Shop', 'contact_number' => '09123456789',
        'address' => '123 Laundry Street', 'id' => 99,
    ])->assertRedirect(route('admin.settings'))->assertSessionHas('shop_success');

    $this->assertDatabaseHas('shop_settings', ['id' => 1, 'shop_name' => 'LAVEA Laundry Shop', 'contact_number' => '09123456789', 'address' => '123 Laundry Street']);
    $this->assertDatabaseCount('shop_settings', 1);
})->with([false, true]);

test('staff and customers cannot save shop information through admin settings', function (string $role): void {
    $user = createSettingsAccount($role);
    $portal = $role === 'user' ? 'customer' : $role;

    $this->actingAs($user)->post(route('admin.settings.shop'), ['shop_name' => 'Unauthorized Shop'])
        ->assertRedirect(route($portal.'.dashboard'));

    $this->assertDatabaseCount('shop_settings', 0);
})->with(['staff', 'customer', 'user']);

test('shop validation preserves saved information on failure', function (string $field, mixed $value, string $message): void {
    $user = createSettingsAccount('admin');
    DB::table('shop_settings')->insert(['id' => 1, 'shop_name' => 'Original Shop']);
    $details = ['shop_name' => 'Updated Shop', 'contact_number' => '09123456789', 'address' => '123 Laundry Street'];
    $details[$field] = $value;

    $this->actingAs($user)->post(route('admin.settings.shop'), $details)
        ->assertSessionHasErrors([$field => $message]);

    $this->assertDatabaseHas('shop_settings', ['id' => 1, 'shop_name' => 'Original Shop']);
})->with([
    ['shop_name', '', 'The shop name field is required.'],
    ['shop_name', str_repeat('a', 256), 'The shop name field must not be greater than 255 characters.'],
    ['contact_number', str_repeat('1', 31), 'The contact number field must not be greater than 30 characters.'],
    ['address', str_repeat('a', 1001), 'The address field must not be greater than 1000 characters.'],
]);

test('settings escape account and customer contact values in either theme', function (string $theme): void {
    $user = User::factory()->create(['role' => 'customer', 'name' => '<script>accountName</script>']);
    CustomerFactory::new()->create(['email' => $user->email, 'address' => '<script>contactAddress</script>']);

    $this->actingAs($user)->withUnencryptedCookie('lavea_theme', $theme)->get(route('customer.settings'))
        ->assertSee('<script>accountName</script>')->assertDontSee('<script>accountName</script>', false)
        ->assertSee('<script>contactAddress</script>')->assertDontSee('<script>contactAddress</script>', false);
})->with(['light', 'dark']);

test('settings pages ignore account ids in query parameters', function (string $role): void {
    $user = createSettingsAccount($role);
    $other = createSettingsAccount($role);
    $portal = $role === 'user' ? 'customer' : $role;

    $this->actingAs($user)->get(route($portal.'.settings', ['user_id' => $other->id, 'id' => $other->id]))
        ->assertSee('value="'.$user->email.'"', false)->assertDontSee('value="'.$other->email.'"', false);
})->with(['admin', 'staff', 'customer', 'user']);

test('settings do not expose account editing routes with user ids', function (string $role): void {
    $user = createSettingsAccount($role);
    $other = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->post('/'.$role.'/settings/'.$other->id, ['name' => 'Unauthorized Name'])
        ->assertNotFound();

    $this->assertDatabaseHas('users', ['id' => $other->id, 'name' => $other->name]);
})->with(['admin', 'staff', 'customer']);
