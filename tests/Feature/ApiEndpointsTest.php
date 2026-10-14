<?php

use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
    DB::table('shop_settings')->updateOrInsert(['id' => 1], ['shop_name' => 'Test Shop', 'gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant']);
});

test('public service endpoints expose only active database records as JSON', function (): void {
    $service = ServiceFactory::new()->create(['service_name' => 'Wash and Fold']);
    $inactive = ServiceFactory::new()->create(['is_active' => false]);
    foreach (['/api/services', '/api/v1/services'] as $path) {
        $this->get($path)->assertOk()->assertJsonPath('data.0.id', $service->id)
            ->assertJsonCount(1, 'data')->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonMissingPath('data.0.required_job_position');
    }
    $this->get('/api/v1/services/'.$service->id)->assertOk()->assertJsonPath('data.price', fn (mixed $price): bool => (float) $price === (float) $service->price);
    $this->get('/api/v1/services/'.$inactive->id)->assertNotFound()->assertHeader('Content-Type', 'application/json');
    $this->get('/api/v1/services?per_page=101')->assertUnprocessable()->assertJsonValidationErrors('per_page');
    $this->get('/api/v1/services?search=nonexistent')->assertOk()->assertJsonCount(0, 'data');
});

test('protected endpoints require credentials without an accept header', function (string $path): void {
    $this->get($path)->assertUnauthorized()->assertHeader('Content-Type', 'application/json');
    $this->withBasicAuth('missing@example.com', 'wrong')->get($path)->assertUnauthorized();
})->with(['/api/v1/orders', '/api/v1/orders/1', '/api/v1/payments', '/api/v1/payments/1']);

test('customers only read their own records without private information', function (): void {
    $user = User::factory()->create(['password' => 'password']);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $ownOrder = OrderFactory::new()->for($customer)->create();
    $otherOrder = OrderFactory::new()->create();
    $ownPayment = PaymentFactory::new()->for($ownOrder)->create();
    $otherPayment = PaymentFactory::new()->for($otherOrder)->create();
    $this->withBasicAuth($user->email, 'password');
    $this->get('/api/v1/orders')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ownOrder->id)
        ->assertJsonMissingPath('data.0.customer')->assertJsonMissingPath('data.0.delivery_address')->assertJsonMissingPath('data.0.notes');
    $this->get('/api/v1/orders/'.$ownOrder->id)->assertOk();
    $this->get('/api/v1/orders/'.$otherOrder->id)->assertNotFound();
    $this->get('/api/v1/payments')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ownPayment->id)
        ->assertJsonMissingPath('data.0.reference_number')->assertJsonMissingPath('data.0.proof_image_path');
    $this->get('/api/v1/payments/'.$ownPayment->id)->assertOk();
    $this->get('/api/v1/payments/'.$otherPayment->id)->assertNotFound();
    $this->get('/api/v1/orders?status=invalid')->assertUnprocessable()->assertJsonValidationErrors('status');
    $this->get('/api/v1/payments?per_page=0')->assertUnprocessable()->assertJsonValidationErrors('per_page');
});

test('admins and managers can read operational records', function (string $role): void {
    $user = User::factory()->create(['role' => $role, 'password' => 'password']);
    $order = OrderFactory::new()->create();
    $payment = PaymentFactory::new()->for($order)->create();
    $this->withBasicAuth($user->email, 'password');
    $this->get('/api/v1/orders')->assertOk()->assertJsonPath('data.0.id', $order->id);
    $this->get('/api/v1/payments/'.$payment->id)->assertOk();
})->with(['admin', 'manager']);

test('unverified customers and unsupported roles are forbidden', function (string $role): void {
    $user = User::factory()->create(['role' => $role, 'email_verified_at' => null, 'password' => 'password']);
    $this->withBasicAuth($user->email, 'password')->get('/api/v1/orders')->assertForbidden();
})->with(['customer', 'staff']);

test('customers without a profile get empty lists and missing API routes return JSON', function (): void {
    $user = User::factory()->create(['password' => 'password']);
    OrderFactory::new()->create();
    $this->withBasicAuth($user->email, 'password');
    $this->get('/api/v1/orders')->assertOk()->assertJsonCount(0, 'data');
    $this->get('/api/v1/payments')->assertOk()->assertJsonCount(0, 'data');
    $this->get('/api/v1/missing')->assertNotFound()->assertHeader('Content-Type', 'application/json');
});
