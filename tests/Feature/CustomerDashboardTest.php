<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ServiceFactory;
use Database\Factories\StaffFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
    DB::table('shop_settings')->updateOrInsert(['id' => 1], ['shop_name' => 'Test Shop', 'gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant']);
});

test('each payment method requires a reference number for customer submissions', function (string $method): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 150]);
    $this->actingAs($user)->post(route('customer.payments.checkout.store', $order), ['amount' => 50, 'payment_method' => $method])
        ->assertSessionHasErrors('reference_number');
    $this->assertDatabaseCount('payments', 0);
})->with(['Cash', 'GCash', 'Card', 'Bank Transfer']);

test('staff corrects rejected payments without deleting history or marking orders paid', function (): void {
    Storage::fake('local');
    $staff = User::factory()->create(['role' => 'manager']);
    $order = OrderFactory::new()->create(['total' => 150, 'payment_status' => 'Unpaid']);
    $payment = PaymentFactory::new()->for($order)->create(['amount' => 150, 'status' => 'Rejected', 'proof_image_path' => 'payment-proofs/receipt.jpg']);
    Storage::disk('local')->put('payment-proofs/receipt.jpg', 'proof');
    $this->actingAs($staff)->get(route('manager.payments.index'))->assertOk()->assertSee(route('manager.payments.edit', $payment), false);
    $this->get(route('manager.payments.edit', $payment))->assertOk()->assertSee('Edit Payment');
    $details = ['amount' => 50, 'payment_method' => 'GCash', 'payment_date' => today()->toDateString(), 'status' => 'Confirmed', 'reference_number' => 'CORRECTED-123'];
    $this->put(route('manager.payments.update', $payment), $details)->assertRedirect(route('manager.payments.show', $payment))->assertSessionHasNoErrors();
    expect($order->fresh()->payment_status)->toBe('Unpaid');
    expect($payment->fresh()->status)->toBe('Pending');
    expect($payment->fresh()->reference_number)->toBe('CORRECTED-123');
    $this->put(route('manager.payments.update', $payment), [...$details, 'amount' => 150])->assertForbidden();
    $this->delete(route('manager.payments.destroy', $payment))->assertForbidden();
    $this->assertDatabaseHas('payments', ['id' => $payment->id]);
    Storage::disk('local')->assertExists('payment-proofs/receipt.jpg');
});

test('payment corrections reject invalid amounts and customer access', function (): void {
    $payment = PaymentFactory::new()->create(['status' => 'Rejected']);
    $staff = User::factory()->create(['role' => 'manager']);
    $this->actingAs($staff)->put(route('manager.payments.update', $payment), ['amount' => -1])->assertSessionHasErrors('amount');
    $customer = User::factory()->create(['role' => 'customer']);
    $this->actingAs($customer)->get(route('manager.payments.edit', $payment))->assertRedirect();
    $this->put(route('manager.payments.update', $payment), ['amount' => 1])->assertRedirect();
    $this->delete(route('manager.payments.destroy', $payment))->assertRedirect();
    $this->assertDatabaseHas('payments', ['id' => $payment->id]);
});

test('inactive services cannot receive new orders and existing records remain accessible', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create(['service_name' => 'Unavailable laundry', 'is_active' => false]);
    $order = OrderFactory::new()->for($customer)->for($service)->create();

    $this->actingAs($user)->get(route('customer.services.index'))->assertOk()->assertSee('Unavailable laundry')->assertSee('Inactive');
    $this->get(route('customer.services.show', $service))->assertOk()->assertSee('Currently unavailable')->assertDontSee('Order this service');
    $this->post(route('customer.services.order', $service), ['quantity' => 2])->assertForbidden();
    $this->assertDatabaseCount('orders', 1);
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'service_id' => $service->id]);

    $service->update(['is_active' => true]);
    $this->get(route('customer.services.index'))->assertOk()->assertSee('Unavailable laundry');
    $this->get(route('customer.services.show', $service))->assertOk();
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
    PaymentFactory::new()->for($pending)->create(['amount' => 900, 'status' => 'Rejected']);
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
})->with(['admin', 'manager']);

test('accounts without a customer profile see empty data instead of other customer data', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $otherOrder = OrderFactory::new()->create();
    PaymentFactory::new()->for($otherOrder)->create();

    $this->actingAs($user)->get(route('customer.dashboard'))->assertOk()
        ->assertSee('No orders yet')->assertSee('₱0.00')
        ->assertViewHas('stats', fn (array $stats): bool => $stats['total'] === 0 && $stats['pending'] === 0 && $stats['completed'] === 0);
    $this->get(route('customer.payments.index'))->assertSee('No payment records match your search.');
    $this->get(route('customer.profile'))->assertSee('Your customer profile is not linked yet.');
    $this->patch(route('customer.profile.update'), ['name' => 'New Name'])->assertNotFound();
});

test('customers update only their own contact details and cannot change account ownership or role', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $otherCustomer = CustomerFactory::new()->create(['name' => 'Other Customer']);

    $this->actingAs($user)->get(route('customer.profile'))
        ->assertSee('name="_method" value="PATCH"', false);

    $response = $this->post(route('customer.profile.update'), [
        '_method' => 'PATCH',
        'name' => 'Updated Customer',
        'phone' => '09123456789',
        'address' => '123 Laundry Street',
        'customer_id' => $otherCustomer->id,
        'email' => $otherCustomer->email,
        'role' => 'admin',
    ]);

    $response->assertRedirect(route('customer.profile'))->assertSessionHas('profile_saved', 'Your profile was saved successfully.');
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
    $user = User::factory()->create(['role' => 'customer']);

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
    ['manager', 'Manager Account'],
]);

test('customer pages render services profile and saved theme with escaped content', function (): void {
    $user = User::factory()->create(['role' => 'customer', 'name' => '<script>customerName</script>']);
    CustomerFactory::new()->create(['email' => $user->email, 'address' => '<script>address</script>']);
    ServiceFactory::new()->create(['service_name' => '<script>serviceName</script>', 'description' => '<script>serviceDescription</script>']);

    $this->actingAs($user)->withUnencryptedCookie('lavea_theme', 'dark')->get(route('customer.dashboard'))
        ->assertOk()->assertSee('data-lavea-theme="dark"', false)->assertSee('css/lavea-theme.css', false)
        ->assertDontSee('dashboard-theme-toggle')->assertSee('Customer Account')
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

test('customers choose pickup or delivery and persist only the applicable details', function (string $method): void {
    StaffFactory::new()->create(['role' => 'Delivery Rider']);
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email, 'address' => 'Original profile address']);
    $service = ServiceFactory::new()->create(['price' => 100]);
    $date = today()->addDays(3)->toDateString();
    $this->actingAs($user)->post(route('customer.services.order', $service), [
        'quantity' => 2, 'fulfillment_method' => $method, 'pickup_date' => $date,
        'delivery_date' => $date, 'delivery_address' => 'Complete delivery address', 'customer_id' => 999,
    ])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('orders', ['customer_id' => $customer->id, 'fulfillment_method' => $method,
        'delivery_address' => $method === 'Delivery' ? 'Complete delivery address' : null, 'total' => 200]);
    expect($customer->fresh()->address)->toBe('Original profile address');
    $order = Order::sole();
    expect($order->pickup_date?->toDateString())->toBe($method === 'Pickup' ? $date : null);
    expect($order->delivery_date?->toDateString())->toBe($method === 'Delivery' ? $date : null);
    $this->get(route('customer.orders.show', $order))->assertOk()->assertSee($method);
    if ($method === 'Delivery') {
        $this->get(route('customer.orders.show', $order))->assertSee('Complete delivery address');
        $this->actingAs(User::factory()->create(['role' => 'manager']))->get(route('manager.orders.show', $order))->assertSee('Complete delivery address');
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.orders.show', $order))->assertSee('Complete delivery address');
    }
})->with(['Pickup', 'Delivery']);

test('fulfillment validates the selected date and required delivery address', function (array $fields, array $errors): void {
    $user = User::factory()->create(['role' => 'customer']);
    CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create();
    $this->actingAs($user)->post(route('customer.services.order', $service), ['quantity' => 1, ...$fields])->assertSessionHasErrors($errors);
    $this->assertDatabaseCount('orders', 0);
})->with([
    [['fulfillment_method' => 'Pickup'], ['pickup_date']],
    [['fulfillment_method' => 'Delivery'], ['delivery_date', 'delivery_address']],
    [['fulfillment_method' => 'Other'], ['fulfillment_method']],
    [['fulfillment_method' => 'Delivery', 'delivery_date' => '2000-01-01', 'delivery_address' => str_repeat('x', 1001)], ['delivery_date', 'delivery_address']],
]);

test('customers switch fulfillment methods and old details are cleared when editing', function (): void {
    StaffFactory::new()->create(['role' => 'Delivery Rider']);
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create(['fulfillment_method' => 'Pickup', 'pickup_date' => today()->addDays(2)]);
    $this->actingAs($user)->put(route('customer.orders.update', $order), [
        'quantity' => 1, 'fulfillment_method' => 'Delivery', 'delivery_date' => today()->addDays(3)->toDateString(), 'delivery_address' => 'New delivery address',
    ])->assertSessionHasNoErrors();
    expect($order->fresh()->pickup_date)->toBeNull();
    expect($order->fresh()->fulfillment_method)->toBe('Delivery');
    $this->get(route('customer.orders.edit', $order))->assertOk()->assertSee('New delivery address');
    $this->put(route('customer.orders.update', $order), ['quantity' => 1, 'fulfillment_method' => 'Pickup', 'pickup_date' => today()->addDays(4)->toDateString()])->assertSessionHasNoErrors();
    expect($order->fresh()->delivery_date)->toBeNull();
    expect($order->fresh()->delivery_address)->toBeNull();
    expect($order->fresh()->fulfillment_method)->toBe('Pickup');
});

test('delivery automatically chooses the least busy active rider and ignores submitted assignment ids', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create();
    $busy = StaffFactory::new()->create(['role' => 'Delivery Rider']);
    $idle = StaffFactory::new()->create(['role' => 'Delivery Rider', 'name' => 'Available Rider']);
    $inactive = StaffFactory::new()->create(['role' => 'Delivery Rider', 'status' => 'Inactive']);
    $managerRecord = StaffFactory::new()->create(['role' => 'Manager']);
    $laundryWorker = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    OrderFactory::new()->create(['delivery_rider_id' => $busy->id, 'fulfillment_method' => 'Delivery', 'status' => 'Processing']);
    OrderFactory::new()->create(['delivery_rider_id' => $idle->id, 'fulfillment_method' => 'Delivery', 'status' => 'Completed']);
    $this->actingAs($user)->post(route('customer.services.order', $service), [
        'quantity' => 1, 'fulfillment_method' => 'Delivery', 'delivery_date' => today()->addDays(2)->toDateString(),
        'delivery_address' => 'Delivery address', 'delivery_rider_id' => $managerRecord->id, 'staff_id' => $laundryWorker->id,
    ])->assertSessionHasNoErrors();
    $order = Order::where('customer_id', $customer->id)->sole();
    expect($order->delivery_rider_id)->toBe($idle->id);
    expect($order->staff_id)->toBeNull();
    $this->get(route('customer.orders.show', $order))->assertSee('Available Rider');
    $this->put(route('customer.orders.update', $order), ['quantity' => 1, 'fulfillment_method' => 'Pickup', 'pickup_date' => today()->addDays(2)->toDateString()])->assertSessionHasNoErrors();
    expect($order->fresh()->delivery_rider_id)->toBeNull();
    $this->actingAs(User::factory()->create(['role' => 'manager']))->delete(route('manager.staff.destroy', $busy))->assertSessionHas('error');
    $this->assertModelExists($busy);
});

test('delivery without an active rider fails without creating an order and pickup still works', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create();
    StaffFactory::new()->create(['role' => 'Delivery Rider', 'status' => 'Inactive']);
    StaffFactory::new()->create(['role' => 'Manager']);
    StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $this->actingAs($user)->post(route('customer.services.order', $service), [
        'quantity' => 1, 'fulfillment_method' => 'Delivery', 'delivery_date' => today()->addDays(2)->toDateString(), 'delivery_address' => 'Address',
    ])->assertSessionHasErrors('fulfillment_method');
    $this->assertDatabaseCount('orders', 0);
    $this->post(route('customer.services.order', $service), ['quantity' => 1, 'fulfillment_method' => 'Pickup', 'pickup_date' => today()->addDays(2)->toDateString()])->assertSessionHasNoErrors();
    expect(Order::sole()->delivery_rider_id)->toBeNull();
});

test('editing delivery preserves an active rider or replaces an unavailable rider independently of laundry staff', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $original = StaffFactory::new()->create(['role' => 'Delivery Rider']);
    $replacement = StaffFactory::new()->create(['role' => 'Delivery Rider']);
    $laundry = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $order = OrderFactory::new()->for($customer)->create(['fulfillment_method' => 'Delivery', 'delivery_rider_id' => $original->id, 'staff_id' => $laundry->id]);
    $details = ['quantity' => 1, 'fulfillment_method' => 'Delivery', 'delivery_date' => today()->addDays(2)->toDateString(), 'delivery_address' => 'Address'];
    $this->actingAs($user)->put(route('customer.orders.update', $order), $details)->assertSessionHasNoErrors();
    expect($order->fresh()->delivery_rider_id)->toBe($original->id);
    $original->update(['status' => 'Inactive']);
    $this->put(route('customer.orders.update', $order), $details)->assertSessionHasNoErrors();
    expect($order->fresh()->delivery_rider_id)->toBe($replacement->id);
    expect($order->fresh()->staff_id)->toBe($laundry->id);
});

test('delivery orders cannot be updated or read by another customer', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->create(['fulfillment_method' => 'Delivery', 'delivery_address' => 'Private address']);
    $this->actingAs($user)->get(route('customer.orders.show', $order))->assertNotFound();
    $this->put(route('customer.orders.update', $order), ['quantity' => 1, 'fulfillment_method' => 'Pickup', 'pickup_date' => today()->addDays(3)->toDateString()])->assertNotFound();
    expect($order->fresh()->delivery_address)->toBe('Private address');
});

test('manager can update progress on delivery orders without requiring a pickup date', function (): void {
    $worker = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $order = OrderFactory::new()->for($service)->create(['staff_id' => $worker->id, 'fulfillment_method' => 'Delivery', 'pickup_date' => null, 'delivery_date' => today()->addDays(3), 'delivery_address' => 'Saved delivery address']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->put(route('manager.orders.update', $order), [
        'customer_id' => $order->customer_id, 'service_id' => $service->id, 'quantity' => $order->quantity,
        'order_date' => $order->order_date->toDateString(), 'delivery_date' => $order->delivery_date->toDateString(), 'status' => 'Processing',
    ])->assertSessionHasNoErrors();
    expect($order->fresh()->status)->toBe('Processing');
    expect($order->fresh()->delivery_address)->toBe('Saved delivery address');
    expect($order->fresh()->fulfillment_method)->toBe('Delivery');
});

test('customers can order decimal laundry weights with the correct total', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create(['price' => 150]);

    $this->actingAs($user)->get(route('customer.services.show', $service))
        ->assertOk()->assertSee('step="0.01"', false);
    $this->post(route('customer.services.order', $service), [
        'quantity' => '1.2',
        'pickup_date' => today()->addDays(3)->toDateString(),
    ])->assertRedirect(route('customer.services.show', $service))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'quantity' => 1.2,
        'total' => 180,
    ]);
});

test('customers can order decimal sets with the correct total', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create(['service_name' => 'Bedsheet Cleaning', 'price' => 150]);

    $this->actingAs($user)->get(route('customer.services.show', $service))
        ->assertOk()->assertSee('step="0.01"', false);
    $this->post(route('customer.services.order', $service), [
        'quantity' => '1.01',
        'pickup_date' => today()->addDays(3)->toDateString(),
    ])->assertRedirect(route('customer.services.show', $service))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'quantity' => 1.01,
        'total' => 151.5,
    ]);

    $this->post(route('customer.services.order', $service), [
        'quantity' => '1.001',
        'pickup_date' => today()->addDays(3)->toDateString(),
    ])->assertSessionHasErrors('quantity');
});

test('customers edit and cancel their pending unpaid orders', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create(['price' => 150]);
    $order = OrderFactory::new()->for($customer)->for($service)->create();
    $this->actingAs($user)->get(route('customer.orders.index'))->assertSee(route('customer.orders.edit', $order), false);
    $this->get(route('customer.orders.edit', $order))->assertOk();
    $this->put(route('customer.orders.update', $order), [
        'quantity' => '1.2', 'pickup_date' => today()->addDays(3)->toDateString(), 'notes' => 'Handle carefully',
        'status' => 'Completed', 'customer_id' => 999, 'total' => 1,
    ])->assertRedirect(route('customer.orders.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'quantity' => 1.2, 'total' => 180, 'status' => 'Pending', 'customer_id' => $customer->id]);
    $this->patch(route('customer.orders.cancel', $order))->assertRedirect(route('customer.orders.index'));
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Cancelled']);
});

test('customers cannot modify protected or other customer orders', function (string $condition): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create();
    if ($condition === 'other') {
        $order->update(['customer_id' => CustomerFactory::new()->create()->id]);
    } elseif (in_array($condition, ['Processing', 'Completed', 'Cancelled'], true)) {
        $order->update(['status' => $condition]);
    } elseif ($condition === 'Paid') {
        $order->update(['payment_status' => 'Paid']);
    } else {
        PaymentFactory::new()->for($order)->create(['status' => $condition]);
    }
    $expectedStatus = $condition === 'other' ? 404 : 403;
    $original = $order->fresh()->getAttributes();
    $this->actingAs($user)->get(route('customer.orders.edit', $order))->assertStatus($expectedStatus);
    $this->put(route('customer.orders.update', $order), [
        'quantity' => '1.2', 'pickup_date' => today()->addDays(3)->toDateString(),
    ])->assertStatus($expectedStatus);
    $this->patch(route('customer.orders.cancel', $order))->assertStatus($expectedStatus);
    expect($order->fresh()->getAttributes())->toBe($original);
})->with(['other', 'Processing', 'Completed', 'Cancelled', 'Paid', 'Pending']);

test('customer order edits reject invalid weights and pickup dates', function (): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create();
    $this->actingAs($user)->put(route('customer.orders.update', $order), [
        'quantity' => '9.5', 'pickup_date' => today()->subDay()->toDateString(),
    ])->assertSessionHasErrors(['quantity', 'pickup_date']);
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'quantity' => 2, 'total' => 150]);
});

test('customers attach private payment proof for electronic payment methods', function (string $method): void {
    Storage::fake('local');
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 200]);
    $this->actingAs($user)->get(route('customer.payments.checkout', $order))->assertOk()->assertSee('multipart/form-data');
    $this->post(route('customer.payments.checkout.store', $order), [
        'amount' => 100, 'payment_method' => $method, 'reference_number' => 'REF-123',
        'proof_image' => UploadedFile::fake()->image('receipt.png'),
    ])->assertRedirect()->assertSessionHasNoErrors();
    $payment = Payment::sole();
    expect($payment->status)->toBe('Pending');
    expect($payment->proof_image_path)->toStartWith('payment-proofs/');
    Storage::disk('local')->assertExists($payment->proof_image_path);
    $this->get(route('payments.proof', $payment))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
    $this->get(route('customer.payments.show', $payment))->assertSee(route('payments.proof', $payment), false);
})->with(['GCash', 'Bank Transfer', 'Card']);

test('payment proof access is restricted to the owner and staff accounts', function (string $role): void {
    Storage::fake('local');
    $owner = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $owner->email]);
    $order = OrderFactory::new()->for($customer)->create();
    $payment = PaymentFactory::new()->for($order)->create(['proof_image_path' => 'payment-proofs/receipt.png']);
    Storage::disk('local')->put($payment->proof_image_path, 'proof');
    if ($role === 'guest') {
        $this->get(route('payments.proof', $payment))->assertRedirect(route('login'));
    } else {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user)->get(route('payments.proof', $payment))->assertStatus(in_array($role, ['admin', 'manager'], true) ? 200 : 404);
    }
})->with(['admin', 'manager', 'customer', 'guest']);

test('invalid payment proof does not create a payment or save files', function (): void {
    Storage::fake('local');
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create();
    $this->actingAs($user)->post(route('customer.payments.checkout.store', $order), [
        'amount' => 100, 'payment_method' => 'Card',
        'proof_image' => UploadedFile::fake()->create('proof.pdf', 20, 'application/pdf'),
    ])->assertSessionHasErrors('proof_image');
    $this->assertDatabaseCount('payments', 0);
    Storage::disk('local')->assertDirectoryEmpty('payment-proofs');
});

test('customer receipts are available only for verified payments', function (string $status): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create();
    $payment = PaymentFactory::new()->for($order)->create(['status' => $status, 'reference_number' => 'REF-RECEIPT']);
    $response = $this->actingAs($user)->get(route('customer.payments.show', $payment))->assertOk();
    if ($status !== 'Confirmed') {
        $response->assertDontSee('id="payment-receipt"', false);
    } else {
        $response->assertSee('id="payment-receipt"', false)->assertSee('REF-RECEIPT');
        $response->assertSee('Payment Receipt')->assertSee('Print Receipt');
    }
})->with(['Pending', 'Confirmed', 'Rejected']);

test('all payment portals render the same modern receipt', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create();
    $payment = PaymentFactory::new()->for($order)->create(['status' => 'Confirmed', 'processed_by' => $user->id]);
    $response = $this->actingAs($user)->get(route($role.'.payments.show', $payment))->assertOk();
    $response->assertSee('class="receipt-header"', false)->assertSee('class="receipt-amount"', false)
        ->assertSee('Verified payment')->assertSee('Print Receipt');
    expect(substr_count($response->getContent(), 'id="payment-receipt"'))->toBe(1);
})->with(['admin', 'manager', 'customer']);

test('receipt endpoint enforces role ownership confirmation and processor access', function (string $role, string $status, bool $ownsPayment, bool $processedPayment, int $expectedStatus): void {
    $user = User::factory()->create(['role' => $role]);
    $customer = CustomerFactory::new()->create(['email' => $ownsPayment ? $user->email : 'other-receipt@example.com']);
    $order = OrderFactory::new()->for($customer)->create();
    $payment = PaymentFactory::new()->for($order)->create([
        'status' => $status,
        'processed_by' => $processedPayment ? $user->id : null,
    ]);

    $response = $this->actingAs($user)->get(route('payments.receipt', $payment));
    $response->assertStatus($expectedStatus);
    if ($expectedStatus === 200) {
        $response->assertSee('id="payment-receipt"', false)->assertSee('window.print()', false);
    } else {
        $response->assertDontSee('id="payment-receipt"', false);
    }
})->with([
    'admin confirmed' => ['admin', 'Confirmed', false, false, 200],
    'admin pending' => ['admin', 'Pending', false, false, 403],
    'admin failed' => ['admin', 'Rejected', false, false, 403],
    'staff processor' => ['manager', 'Confirmed', false, true, 200],
    'staff other transaction' => ['manager', 'Confirmed', false, false, 200],
    'customer confirmed own' => ['customer', 'Confirmed', true, false, 200],
    'customer pending own' => ['customer', 'Pending', true, false, 403],
    'customer failed own' => ['customer', 'Rejected', true, false, 403],
    'customer other receipt' => ['customer', 'Confirmed', false, false, 404],
    'legacy customer own' => ['user', 'Confirmed', true, false, 404],
]);

test('receipt endpoint requires login', function (): void {
    $payment = PaymentFactory::new()->create();
    $this->get(route('payments.receipt', $payment))->assertRedirect(route('login'));
});

test('admin confirmation records the reviewer while preserving the staff processor', function (): void {
    $staff = User::factory()->create(['role' => 'manager']);
    $admin = User::factory()->create(['role' => 'admin']);
    $payment = PaymentFactory::new()->create(['status' => 'Pending', 'processed_by' => $staff->id]);
    $this->actingAs($staff)->get(route('payments.receipt', $payment))->assertNotFound();
    $this->actingAs($admin)->patch(route('admin.payments.review', $payment), [
        'status' => 'Confirmed', 'processed_by' => 999,
    ])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'Confirmed', 'processed_by' => $staff->id, 'reviewed_by' => $admin->id]);
    $this->actingAs($staff)->get(route('payments.receipt', $payment))->assertOk()->assertSee('Verified payment');
});

test('staff payment creation records the processor for receipt permissions', function (): void {
    $staff = User::factory()->create(['role' => 'manager']);
    $otherStaff = User::factory()->create(['role' => 'manager']);
    $order = OrderFactory::new()->create();
    $this->actingAs($staff)->post(route('manager.payments.store'), [
        'order_id' => $order->id, 'amount' => 150, 'payment_method' => 'Cash',
        'reference_number' => 'RECEIPT-CASH', 'payment_date' => today()->toDateString(),
        'status' => 'Confirmed', 'processed_by' => $otherStaff->id,
    ])->assertSessionHasNoErrors();

    $payment = Payment::where('reference_number', 'RECEIPT-CASH')->firstOrFail();
    expect($payment->processed_by)->toBe($staff->id);
    $this->get(route('payments.receipt', $payment))->assertNotFound();
    $this->actingAs(User::factory()->create(['role' => 'admin']))->patch(route('admin.payments.review', $payment), ['status' => 'Confirmed'])->assertSessionHasNoErrors();
    $this->actingAs($staff)->get(route('payments.receipt', $payment))->assertOk();
    $this->actingAs($otherStaff)->get(route('payments.receipt', $payment))->assertOk();
    $this->get(route('manager.payments.show', $payment))->assertSee('Print Receipt')->assertSee('id="payment-receipt"', false);
});

test('staff editing a payment cannot claim another processor receipt', function (): void {
    $processor = User::factory()->create(['role' => 'manager']);
    $editor = User::factory()->create(['role' => 'manager']);
    $payment = PaymentFactory::new()->create(['processed_by' => $processor->id, 'reference_number' => 'RECEIPT-EDIT']);
    $this->actingAs($editor)->put(route('manager.payments.update', $payment), [
        'amount' => 150, 'payment_method' => 'Cash', 'reference_number' => 'RECEIPT-EDIT',
        'payment_date' => today()->toDateString(), 'status' => 'Confirmed',
        'processed_by' => $editor->id,
    ])->assertForbidden();
    expect($payment->fresh()->processed_by)->toBe($processor->id);
    $this->get(route('payments.receipt', $payment))->assertOk();
});

test('customer record actions only allow editing and cancelling eligible orders', function (string $state): void {
    $user = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 150, 'status' => $state === 'Cancelled' ? 'Cancelled' : 'Pending']);
    if (in_array($state, ['Pending', 'Confirmed'], true)) {
        $payment = PaymentFactory::new()->for($order)->create(['amount' => 150, 'status' => $state]);
    }
    $response = $this->actingAs($user)->get(route('customer.orders.index'))->assertOk()
        ->assertSee(route('customer.orders.show', $order), false)->assertDontSee('View Order');
    $response->assertDontSee(route('customer.payments.checkout', $order), false);
    if ($state === 'Unpaid') {
        $response->assertSee(route('customer.orders.edit', $order), false)->assertSee('Cancel');
    } else {
        $response->assertDontSee(route('customer.orders.edit', $order), false)->assertSee('&mdash;', false);
    }
    if (isset($payment)) {
        $response->assertDontSee(route('customer.payments.show', $payment), false);
    }
})->with(['Unpaid', 'Pending', 'Confirmed', 'Cancelled']);

test('service orders use sets and items instead of kilograms', function (string $name, string $unit): void {
    $user = User::factory()->create(['role' => 'customer']);
    CustomerFactory::new()->create(['email' => $user->email]);
    $service = ServiceFactory::new()->create(['service_name' => $name, 'price' => 150]);
    expect($service->billingUnit())->toBe($unit);
    $this->actingAs($user)->get(route('customer.services.show', $service))->assertOk()
        ->assertSee('per '.$unit)->assertSee('step="'.($unit === 'set' ? '0.01' : '1').'"', false)->assertDontSee('Maximum: 9 kg');
    $decimalResponse = $this->post(route('customer.services.order', $service), [
        'quantity' => '1.2', 'pickup_date' => today()->addDays(3)->toDateString(),
    ]);
    if ($unit === 'item') {
        $decimalResponse->assertSessionHasErrors('quantity');
        $this->assertDatabaseCount('orders', 0);
    } else {
        $decimalResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('orders', ['service_id' => $service->id, 'quantity' => 1.2, 'total' => 180]);
    }
    $this->post(route('customer.services.order', $service), [
        'quantity' => 2, 'pickup_date' => today()->addDays(3)->toDateString(),
    ])->assertSessionHasNoErrors()->assertRedirect(route('customer.services.show', $service));
    $this->assertDatabaseHas('orders', ['service_id' => $service->id, 'quantity' => 2, 'total' => 300]);
})->with([
    ['Bedsheet Cleaning', 'set'], ['Comforter Cleaning', 'set'], ['Curtain Cleaning', 'set'], ['Dry Cleaning', 'item'],
]);

test('customers can delete their account while preserving anonymized transaction history', function (bool $hasOrders): void {
    Storage::fake('public');
    Storage::disk('public')->put('customer-profiles/avatar.png', 'photo');
    $user = User::factory()->create(['role' => 'customer', 'password' => 'Delete-password-123', 'profile_photo_path' => 'customer-profiles/avatar.png']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $other = User::factory()->create(['role' => 'customer']);
    if ($hasOrders) {
        $order = OrderFactory::new()->for($customer)->create();
        $payment = PaymentFactory::new()->for($order)->create();
    }
    $this->actingAs($user)->delete(route('customer.profile.destroy'), ['password' => 'Delete-password-123', 'confirm_deletion' => 1, 'user_id' => $other->id])
        ->assertRedirect(route('home'));
    $this->assertGuest();
    $this->assertModelMissing($user);
    $this->assertModelExists($other);
    Storage::disk('public')->assertMissing('customer-profiles/avatar.png');
    if ($hasOrders) {
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Deleted customer', 'email' => null, 'phone' => '', 'address' => null]);
        $this->assertModelExists($order);
        $this->assertModelExists($payment);
    } else {
        $this->assertModelMissing($customer);
    }
})->with([false, true]);

test('account deletion requires the correct password and confirmation', function (array $payload): void {
    $user = User::factory()->create(['role' => 'customer', 'password' => 'Delete-password-123']);
    $this->actingAs($user)->delete(route('customer.profile.destroy'), $payload)->assertSessionHasErrorsIn('deleteAccount');
    $this->assertModelExists($user);
    $this->assertAuthenticatedAs($user);
})->with([
    [['password' => 'wrong', 'confirm_deletion' => 1]],
    [['password' => 'Delete-password-123']],
]);

test('staff and admins cannot delete accounts through the customer endpoint', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user)->delete(route('customer.profile.destroy'), ['password' => 'password', 'confirm_deletion' => 1])->assertRedirect(route($role.'.dashboard'));
    $this->assertModelExists($user);
})->with(['admin', 'manager']);
