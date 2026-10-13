<?php

use App\Models\Order;
use App\Models\Schedule;
use App\Models\Staff;
use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\StaffFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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
    DB::table('shop_settings')->updateOrInsert(['id' => 1], ['shop_name' => 'Test Shop', 'gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant']);
});

test('admin creates manager logins without creating worker records or accepting a different role', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->get(route('admin.managers.create'))->assertSee('Add Manager Account');
    $this->post(route('admin.managers.store'), ['name' => 'Shop Manager', 'email' => 'manager@example.test', 'phone' => '09123456789', 'password' => 'Manager-password-123', 'password_confirmation' => 'Manager-password-123', 'role' => 'admin'])
        ->assertRedirect(route('admin.managers.index'))->assertSessionHasNoErrors();
    $manager = User::where('email', 'manager@example.test')->firstOrFail();
    expect($manager->role)->toBe('manager');
    expect(Hash::check('Manager-password-123', $manager->password))->toBeTrue();
    $this->assertDatabaseCount('staff', 0);
    $this->put(route('admin.managers.update', $manager), ['name' => 'Updated Manager', 'email' => $manager->email, 'phone' => '09222222222', 'password' => '', 'role' => 'customer'])->assertSessionHasNoErrors();
    expect($manager->fresh()->role)->toBe('manager');
    expect(Hash::check('Manager-password-123', $manager->fresh()->password))->toBeTrue();
});

test('manager account routes cannot be used to edit admin or customer accounts', function (string $role): void {
    $target = User::factory()->create(['role' => $role]);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.managers.edit', $target))->assertNotFound();
    $this->put(route('admin.managers.update', $target), [])->assertNotFound();
})->with(['admin', 'customer']);

test('nonadmins cannot create manager accounts or change services and shop settings', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);
    CustomerFactory::new()->create(['email' => $user->email]);
    $this->actingAs($user)->post(route('admin.managers.store'), ['name' => 'Forged', 'email' => 'forged@example.test', 'password' => 'Forged-password-123', 'password_confirmation' => 'Forged-password-123'])
        ->assertRedirect(route($role.'.dashboard'));
    $this->assertDatabaseMissing('users', ['email' => 'forged@example.test']);
    $this->post(route('admin.services.store'), [])->assertRedirect(route($role.'.dashboard'));
    $this->post(route('admin.settings.gcash'), [])->assertRedirect(route($role.'.dashboard'));
})->with(['manager', 'customer']);

test('manager manages ordinary workers without creating changing or deleting a matching login', function (): void {
    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager)->get(route('manager.staff.create'))->assertSee('Job Position')->assertDontSee('name="password"', false);
    $this->post(route('manager.staff.store'), ['name' => 'Washer One', 'phone' => '09123456789', 'email' => $manager->email, 'role' => 'Laundry Attendant', 'status' => 'Active'])
        ->assertRedirect(route('manager.staff.index'))->assertSessionHasNoErrors();
    $worker = Staff::firstOrFail();
    $this->assertDatabaseCount('users', 1);
    $this->put(route('manager.staff.update', $worker), ['name' => 'Washer Updated', 'email' => 'worker@example.test', 'role' => 'Ironing Attendant', 'status' => 'Inactive'])->assertSessionHasNoErrors();
    expect($manager->fresh()->email)->toBe($manager->email);
    expect($worker->fresh()->role)->toBe('Ironing Attendant');
    $this->delete(route('manager.staff.destroy', $worker))->assertRedirect(route('manager.staff.index'));
    $this->assertModelExists($manager);
});

test('manager assigns active workers and updates progress using the existing relationship', function (): void {
    $manager = User::factory()->create(['role' => 'manager']);
    $worker = StaffFactory::new()->create(['name' => 'Assigned Washer', 'role' => 'Laundry Attendant', 'status' => 'Active']);
    $inactive = StaffFactory::new()->create(['name' => 'Inactive Worker', 'status' => 'Inactive']);
    $order = OrderFactory::new()->create(['staff_id' => null, 'quantity' => 2]);
    $order->service->update(['required_job_position' => 'Laundry Attendant']);
    $this->actingAs($manager)->get(route('manager.orders.edit', $order))->assertSee('Assigned Staff');
    $this->get(route('manager.services.staff-options', $order->service))->assertJsonPath('staff.0.name', 'Assigned Washer')->assertJsonCount(1, 'staff');
    $details = ['customer_id' => $order->customer_id, 'service_id' => $order->service_id, 'quantity' => 2, 'order_date' => today()->toDateString(), 'status' => 'Processing', 'staff_id' => $worker->id];
    $this->put(route('manager.orders.update', $order), $details)->assertRedirect(route('manager.orders.show', $order))->assertSessionHasNoErrors();
    expect($order->fresh()->staff->id)->toBe($worker->id);
    expect($order->fresh()->status)->toBe('Processing');
    $this->get(route('manager.orders.show', $order))->assertSee('Assigned Washer');
    $this->put(route('manager.orders.update', $order), array_merge($details, ['staff_id' => $inactive->id]))->assertSessionHasErrors('staff_id');
    expect($order->fresh()->staff_id)->toBe($worker->id);
    $this->post(route('manager.orders.store'), array_merge($details, ['staff_id' => $inactive->id]))->assertSessionHasErrors('staff_id');
    $this->post(route('manager.orders.store'), $details)->assertSessionHasNoErrors();
    $this->assertDatabaseCount('orders', 2);
    expect(Order::latest('id')->first()->staff_id)->toBe($worker->id);
    $this->delete(route('manager.staff.destroy', $worker))->assertSessionHas('error');
    $this->assertModelExists($worker);
    expect($order->fresh()->staff_id)->toBe($worker->id);
});

test('manager sees all schedules and can create and update them without a worker profile', function (): void {
    $manager = User::factory()->create(['role' => 'manager']);
    $order = OrderFactory::new()->create();
    $details = ['order_id' => $order->id, 'type' => 'Pickup', 'schedule_date' => today()->addDay()->toDateString(), 'schedule_time' => '10:00', 'status' => 'Scheduled'];
    $this->actingAs($manager)->post(route('manager.schedules.store'), $details)->assertRedirect(route('manager.schedules.index'))->assertSessionHasNoErrors();
    $schedule = Schedule::firstOrFail();
    $this->get(route('manager.schedules.index'))->assertSee($order->customer->name);
    $this->put(route('manager.schedules.update', $schedule), array_merge($details, ['status' => 'Completed']))->assertSessionHasNoErrors();
    expect($schedule->fresh()->status)->toBe('Completed');
    $this->delete(route('manager.schedules.destroy', $schedule))->assertRedirect(route('manager.schedules.index'));
});

test('legacy logins migrate in place and preserve credentials workers assignments and payment history', function (): void {
    $legacy = User::factory()->create(['role' => 'staff', 'phone' => null]);
    $legacyCustomer = User::factory()->create(['role' => 'user']);
    $worker = StaffFactory::new()->create(['email' => $legacy->email, 'phone' => '09123456789', 'role' => 'Washer']);
    $order = OrderFactory::new()->create(['staff_id' => $worker->id, 'payment_status' => 'Paid']);
    $payment = PaymentFactory::new()->for($order)->create(['processed_by' => $legacy->id, 'status' => 'Confirmed']);
    $hash = $legacy->password;
    $migration = require database_path('migrations/2026_10_13_210435_migrate_staff_logins_to_manager_accounts.php');
    $migration->up();
    expect($legacy->fresh()->role)->toBe('manager');
    expect($legacy->fresh()->password)->toBe($hash);
    expect($legacy->fresh()->phone)->toBe('09123456789');
    expect($legacyCustomer->fresh()->role)->toBe('customer');
    expect($order->fresh()->staff_id)->toBe($worker->id);
    expect($payment->fresh()->processed_by)->toBe($legacy->id);
    expect($payment->fresh()->status)->toBe('Confirmed');
    expect($worker->fresh()->role)->toBe('Washer');
    $this->assertDatabaseCount('users', 2);
    $this->assertDatabaseCount('staff', 1);
});

test('obsolete roles cannot sign in or access manager pages', function (string $role): void {
    $user = User::factory()->create(['role' => $role, 'password' => 'Legacy-password-123']);
    $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'Legacy-password-123'])->assertSessionHasErrors('email');
    $this->assertGuest();
    $this->actingAs($user)->get(route('manager.dashboard'))->assertForbidden();
})->with(['staff', 'user', 'washer']);

test('customer cannot assign workers edit progress or access worker records', function (): void {
    $customer = User::factory()->create(['role' => 'customer']);
    CustomerFactory::new()->create(['email' => $customer->email]);
    $order = OrderFactory::new()->create();
    $this->actingAs($customer)->put(route('manager.orders.update', $order), [])->assertRedirect(route('customer.dashboard'));
    $this->get(route('manager.staff.index'))->assertRedirect(route('customer.dashboard'));
    expect($order->fresh()->status)->toBe($order->status);
});

test('legacy staff links redirect managers to the same feature', function (): void {
    $this->actingAs(User::factory()->create(['role' => 'manager']))->get('/staff/orders?search=14')->assertRedirect(url('manager/orders').'?search=14');
});
