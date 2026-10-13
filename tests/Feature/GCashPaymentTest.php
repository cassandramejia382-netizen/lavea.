<?php

use App\GCashAccount;
use App\Models\Payment;
use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

beforeEach(function (): void {
    Storage::fake('public');
    Storage::fake('local');
});

test('admin saves a real configured account and optional qr image', function (): void {
    $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('admin.settings.gcash'), [
        'gcash_number' => '09123456789', 'gcash_account_name' => 'Configured Test Merchant',
        'gcash_qr' => UploadedFile::fake()->image('qr.png'),
    ])->assertRedirect(route('admin.settings'))->assertSessionHasNoErrors();
    $settings = DB::table('shop_settings')->find(1);
    expect($settings->gcash_number)->toBe('09123456789');
    expect($settings->gcash_account_name)->toBe('Configured Test Merchant');
    Storage::disk('public')->assertExists($settings->gcash_qr_path);
    expect(GCashAccount::details()['available'])->toBeTrue();
    $this->get(route('admin.settings'))->assertSee('Configured Test Merchant')->assertSee('Saved shop GCash QR code');
});

test('staff and customers cannot manage the shop gcash account', function (string $role): void {
    $this->actingAs(User::factory()->create(['role' => $role]))->post(route('admin.settings.gcash'), [
        'gcash_number' => '09123456789', 'gcash_account_name' => 'Unauthorized',
    ])->assertRedirect(route($role.'.dashboard'));
    expect(GCashAccount::details()['available'])->toBeFalse();
    $this->get(route($role.'.settings'))->assertDontSee('name="gcash_number"', false);
})->with(['manager', 'customer']);

test('account configuration rejects invalid numbers incomplete accounts and unsafe qr files', function (string $case): void {
    $details = ['gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant'];
    if ($case === 'number') {
        $details['gcash_number'] = 'abc';
    }
    if ($case === 'name') {
        $details['gcash_account_name'] = '';
    }
    if ($case === 'missing_number') {
        $details['gcash_number'] = '';
    }
    if ($case === 'image') {
        $details['gcash_qr'] = UploadedFile::fake()->create('qr.svg', 1, 'image/svg+xml');
    }
    if ($case === 'large') {
        $details['gcash_qr'] = UploadedFile::fake()->image('qr.png')->size(5121);
    }
    $field = match ($case) {
        'number','missing_number' => 'gcash_number','name' => 'gcash_account_name',default => 'gcash_qr'
    };
    $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('admin.settings.gcash'), $details)->assertSessionHasErrors($field);
    expect(GCashAccount::details()['available'])->toBeFalse();
})->with(['number', 'name', 'missing_number', 'image', 'large']);

test('saved details are reused by staff and customer payment pages', function (): void {
    DB::table('shop_settings')->insert(['id' => 1, 'shop_name' => 'Test Shop', 'gcash_number' => '09123456789', 'gcash_account_name' => 'Merchant <Example>']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->get(route('manager.payments.create'))
        ->assertOk()->assertSee('09123456789')->assertSee('Merchant &lt;Example&gt;', false)->assertSee('Copy Number');
    $owner = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $owner->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 300]);
    $this->actingAs($owner)->get(route('customer.payments.checkout', $order))->assertOk()
        ->assertSee('09123456789')->assertSee('Merchant &lt;Example&gt;', false)->assertSee('Send your payment to this GCash account. After paying, enter the reference number and upload your payment screenshot.');
});

test('unconfigured accounts block gcash submissions for both payment roles', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 300]);
    $route = $role === 'manager' ? route('manager.payments.store') : route('customer.payments.checkout.store', $order);
    $this->actingAs($user)->post($route, [
        'order_id' => $order->id, 'amount' => 100, 'payment_method' => 'GCash', 'reference_number' => 'TEST-GCASH',
        'payment_date' => today()->toDateString(), 'proof_image' => UploadedFile::fake()->image('proof.png'),
    ])->assertSessionHasErrors(['payment_method' => 'GCash payment is currently unavailable']);
    $this->assertDatabaseCount('payments', 0);
    expect(Storage::disk('local')->allFiles('payment-proofs'))->toBe([]);
})->with(['manager', 'customer']);

test('configured gcash proof submissions stay pending until admin approval', function (string $role): void {
    DB::table('shop_settings')->insert(['id' => 1, 'shop_name' => 'Test Shop', 'gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant']);
    $user = User::factory()->create(['role' => $role]);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 300, 'payment_status' => 'Unpaid']);
    $route = $role === 'manager' ? route('manager.payments.store') : route('customer.payments.checkout.store', $order);
    $this->actingAs($user)->post($route, [
        'order_id' => $order->id, 'amount' => 100, 'payment_method' => 'GCash', 'reference_number' => 'TEST-GCASH',
        'payment_date' => today()->toDateString(), 'proof_image' => UploadedFile::fake()->image('proof.png'), 'status' => 'Confirmed',
    ])->assertSessionHasNoErrors();
    $payment = Payment::firstOrFail();
    expect($payment->status)->toBe('Pending');
    expect($payment->reviewed_by)->toBeNull();
    expect($order->fresh()->payment_status)->toBe('Unpaid');
    Storage::disk('local')->assertExists($payment->proof_image_path);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->patch(route('admin.payments.review', $payment), ['status' => 'Confirmed'])->assertSessionHasNoErrors();
    expect($payment->fresh()->status)->toBe('Confirmed');
    expect($order->fresh()->payment_status)->toBe('Partial');
})->with(['manager', 'customer']);

test('admin may remove the qr or disable the account without changing payments', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->post(route('admin.settings.gcash'), [
        'gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant', 'gcash_qr' => UploadedFile::fake()->image('qr.png'),
    ])->assertSessionHasNoErrors();
    $previous = DB::table('shop_settings')->find(1)->gcash_qr_path;
    $this->post(route('admin.settings.gcash'), ['gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant', 'remove_gcash_qr' => 1])->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($previous);
    expect(GCashAccount::details()['available'])->toBeTrue();
    $this->post(route('admin.settings.gcash'), ['gcash_number' => '', 'gcash_account_name' => ''])->assertSessionHasNoErrors();
    expect(GCashAccount::details()['available'])->toBeFalse();
});
