<?php

use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The original customers, services, and orders migrations are absent from this
 * legacy project. Recreate only their baseline in the isolated SQLite database
 * before running the application's existing migrations.
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

test('dashboard totals and recent orders contain only the signed in customer data', function (): void {
    $user = User::factory()->create(['role' => 'customer', 'name' => 'Maria Santos']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create(['service_name' => 'Wash and Fold']);
    $pending = OrderFactory::new()->for($customer)->for($service)->create();
    $completed = OrderFactory::new()->for($customer)->for($service)->create(['status' => 'Completed']);
    OrderFactory::new()->for($customer)->for($service)->create(['status' => 'Processing']);
    PaymentFactory::new()->for($pending)->create(['amount' => 50]);
    PaymentFactory::new()->for($completed)->create(['amount' => 150]);
    PaymentFactory::new()->for($pending)->create(['amount' => 900, 'status' => 'Pending']);
    PaymentFactory::new()->for($pending)->create(['amount' => 900, 'status' => 'Failed']);
    $otherOrder = OrderFactory::new()->create(['total' => 9876]);
    PaymentFactory::new()->for($otherOrder)->create(['amount' => 9876]);

    $response = $this->actingAs($user)->get(route('customer.dashboard'));

    $response->assertOk()->assertSee('Welcome, Maria Santos')->assertSee('Wash and Fold')
        ->assertSee('₱200.00')->assertDontSee('9,876.00')->assertDontSee($otherOrder->customer->name)
        ->assertViewHas('stats', fn (array $stats): bool => $stats['total'] === 3 && $stats['pending'] === 1 && $stats['completed'] === 1 && (float) $stats['payments'] === 200.0)
        ->assertViewHas('recentOrders', fn ($orders): bool => $orders->count() === 3 && $orders->every(fn ($order): bool => $order->customer_id === $customer->id));
});

test('lists only the customer orders and payments despite a supplied customer id', function (string $route, string $dataKey): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $ownOrder = OrderFactory::new()->for($customer)->create();
    $otherOrder = OrderFactory::new()->create();
    PaymentFactory::new()->for($ownOrder)->create(['reference_number' => 'OWN-REFERENCE']);
    PaymentFactory::new()->for($otherOrder)->create(['reference_number' => 'OTHER-REFERENCE']);

    $response = $this->actingAs($user)->get(route($route, ['customer_id' => $otherOrder->customer_id]));

    $response->assertOk()->assertViewHas($dataKey, fn ($records): bool => $records->total() === 1)
        ->assertDontSee($otherOrder->customer->name)->assertDontSee('OTHER-REFERENCE');
})->with([
    'orders' => ['customer.orders.index', 'orders'],
    'payments' => ['customer.payments.index', 'payments'],
]);

test('returns not found for another customer record', function (string $resource): void {
    $user = User::factory()->create(['role' => 'customer']);
    CustomerFactory::new()->create(['email' => $user->email]);
    $otherOrder = OrderFactory::new()->create();
    $otherPayment = PaymentFactory::new()->for($otherOrder)->create();
    $record = $resource === 'orders' ? $otherOrder : $otherPayment;

    $this->actingAs($user)->get(route('customer.'.$resource.'.show', $record))->assertNotFound();
})->with(['orders', 'payments']);

test('renders customer detail pages for owned records', function (string $resource): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create();
    $payment = PaymentFactory::new()->for($order)->create(['reference_number' => 'MY-RECEIPT']);
    $record = $resource === 'orders' ? $order : $payment;

    $this->actingAs($user)->get(route('customer.'.$resource.'.show', $record))
        ->assertOk()->assertSee('₱150.00')->assertSee('Customer Account');
})->with(['orders', 'payments']);

test('customer pages require login', function (string $route): void {
    $this->get(route($route))->assertRedirect(route('login'));
})->with(['customer.dashboard', 'customer.orders.index', 'customer.services.index', 'customer.payments.index', 'customer.profile']);

test('unverified customers must verify before entering the dashboard', function (): void {
    $user = User::factory()->unverified()->create(['role' => 'customer']);

    $this->actingAs($user)->get(route('customer.dashboard'))->assertRedirect(route('verification.notice'));
});

test('admin and staff users are redirected to their own dashboards', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->get(route('customer.dashboard'))->assertRedirect(route($role.'.dashboard'));
})->with(['admin', 'staff']);

test('accounts without a customer profile see empty data instead of other customer data', function (): void {
    $user = User::factory()->create(['role' => 'user']);
    $otherOrder = OrderFactory::new()->create();
    PaymentFactory::new()->for($otherOrder)->create();

    $this->actingAs($user)->get(route('customer.dashboard'))->assertOk()
        ->assertSee('No orders yet')->assertSee('₱0.00')
        ->assertViewHas('stats', fn (array $stats): bool => $stats['total'] === 0 && $stats['pending'] === 0 && $stats['completed'] === 0);
    $this->get(route('customer.payments.index'))->assertSee('No payments recorded yet.');
    $this->get(route('customer.profile'))->assertSee('Your customer profile is not linked yet.');
    $this->patch(route('customer.profile.update'), ['name' => 'New Name'])->assertNotFound();
});

test('customers update only their own contact details and cannot change account ownership or role', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $otherCustomer = CustomerFactory::new()->create(['name' => 'Other Customer']);

    $response = $this->actingAs($user)->patch(route('customer.profile.update'), [
        'name' => 'Updated Customer',
        'phone' => '09123456789',
        'address' => '123 Laundry Street',
        'customer_id' => $otherCustomer->id,
        'email' => $otherCustomer->email,
        'role' => 'admin',
    ]);

    $response->assertRedirect(route('customer.profile'))->assertSessionHas('success', 'Your profile has been updated.');
    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Updated Customer', 'phone' => '09123456789', 'address' => '123 Laundry Street', 'email' => $user->email]);
    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Customer', 'email' => $user->email, 'role' => 'customer']);
    $this->assertDatabaseHas('customers', ['id' => $otherCustomer->id, 'name' => 'Other Customer']);
});

test('profile updates reject missing contact information without changing saved data', function (): void {
    $user = User::factory()->create(['role' => 'customer', 'name' => 'Original Name']);
    $customer = CustomerFactory::new()->create(['email' => $user->email, 'name' => 'Original Name']);

    $response = $this->actingAs($user)->from(route('customer.profile'))->patch(route('customer.profile.update'), []);

    $response->assertRedirect(route('customer.profile'))->assertSessionHasErrors([
        'name' => 'The name field is required.',
        'phone' => 'The phone field is required.',
        'address' => 'The address field is required.',
    ]);
    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Original Name']);
});

test('legacy user pages redirect to working customer pages', function (string $source, string $destination): void {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get(route($source))->assertRedirect(route($destination));
})->with([
    ['user.dashboard', 'customer.dashboard'],
    ['user.orders', 'customer.orders.index'],
    ['user.services', 'customer.services.index'],
    ['user.payments', 'customer.payments.index'],
    ['user.profile', 'customer.profile'],
]);

test('shared layout keeps admin and staff dashboards working', function (string $role, string $label): void {
    $user = User::factory()->create(['role' => $role]);
    OrderFactory::new()->create();

    $this->actingAs($user)->get(route($role.'.dashboard'))->assertOk()->assertSee($label)->assertDontSee('Customer Account');
})->with([
    ['admin', 'System Administrator'],
    ['staff', 'Staff Account'],
]);

test('customer pages render services profile and theme controls with escaped content', function (): void {
    $user = User::factory()->create(['role' => 'customer', 'name' => '<script>customerName</script>']);
    CustomerFactory::new()->create(['email' => $user->email, 'address' => '<script>address</script>']);
    ServiceFactory::new()->create(['service_name' => '<script>serviceName</script>', 'description' => '<script>serviceDescription</script>']);

    $this->actingAs($user)->withCookie('lavea_theme', 'dark')->get(route('customer.dashboard'))
        ->assertOk()->assertSee('data-lavea-theme="dark"', false)->assertSee('css/lavea-theme.css', false)
        ->assertSee('dashboard-theme-toggle')->assertSee('Customer Account')
        ->assertSee('<script>customerName</script>')->assertDontSee('<script>customerName</script>', false);
    $this->get(route('customer.services.index'))->assertSee('<script>serviceName</script>')
        ->assertSee('<script>serviceDescription</script>')->assertDontSee('<script>serviceName</script>', false)
        ->assertDontSee('<script>serviceDescription</script>', false);
    $this->get(route('customer.profile'))->assertSee('<script>address</script>')->assertDontSee('<script>address</script>', false);
});

test('profile updates validate field types and lengths without saving invalid data', function (string $field, mixed $value, string $message): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email, 'name' => 'Original Name']);
    $details = ['name' => 'Updated Name', 'phone' => '09123456789', 'address' => '123 Laundry Street'];
    $details[$field] = $value;

    $this->actingAs($user)->patch(route('customer.profile.update'), $details)
        ->assertSessionHasErrors([$field => $message]);

    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Original Name']);
})->with([
    'long name' => ['name', str_repeat('a', 256), 'The name field must not be greater than 255 characters.'],
    'long phone' => ['phone', str_repeat('1', 21), 'The phone field must not be greater than 20 characters.'],
    'long address' => ['address', str_repeat('a', 256), 'The address field must not be greater than 255 characters.'],
    'non-string name' => ['name', ['invalid'], 'The name field must be a string.'],
    'non-string phone' => ['phone', ['invalid'], 'The phone field must be a string.'],
    'non-string address' => ['address', ['invalid'], 'The address field must be a string.'],
]);

test('order pagination and recent orders stay scoped and show the newest orders first', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create();
    OrderFactory::new()->for($customer)->for($service)->count(16)->create();
    $latestOrder = OrderFactory::new()->for($customer)->for($service)->create(['order_date' => '2026-10-04']);
    OrderFactory::new()->create(['order_date' => '2026-10-05']);

    $this->actingAs($user)->get(route('customer.orders.index'))->assertOk()->assertSee('Page 1 of 2')
        ->assertViewHas('orders', fn ($orders): bool => $orders->total() === 17 && $orders->count() === 15 && $orders->first()->id === $latestOrder->id);
    $this->get(route('customer.orders.index', ['page' => 2]))->assertOk()->assertSee('Page 2 of 2')
        ->assertViewHas('orders', fn ($orders): bool => $orders->count() === 2 && $orders->every(fn ($order): bool => $order->customer_id === $customer->id));
    $this->get(route('customer.dashboard'))->assertViewHas('recentOrders', fn ($orders): bool => $orders->count() === 6 && $orders->first()->id === $latestOrder->id);
});
