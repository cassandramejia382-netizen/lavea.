<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
    DB::table('shop_settings')->updateOrInsert(['id' => 1], ['shop_name' => 'Test Shop', 'gcash_number' => '09123456789', 'gcash_account_name' => 'Test Merchant']);
});

/** @return array{order_id: int, amount: int, payment_method: string, reference_number: string, payment_date: string} */
function paymentApprovalDetails(Order $order, string $method = 'Cash'): array
{
    return ['order_id' => $order->id, 'amount' => 100, 'payment_method' => $method,
        'reference_number' => 'REF-'.$order->id, 'payment_date' => today()->toDateString()];
}

beforeEach(function (): void {
    Storage::fake('local');
    Notification::fake();
});

test('staff submits cash as pending regardless of supplied approval fields', function (): void {
    $staff = User::factory()->create(['role' => 'manager']);
    $order = OrderFactory::new()->create(['total' => 300, 'payment_status' => 'Unpaid']);
    $this->actingAs($staff)->post(route('manager.payments.store'), array_merge(paymentApprovalDetails($order), [
        'status' => 'Confirmed', 'reviewed_by' => $staff->id,
    ]))->assertSessionHasNoErrors();
    $payment = Payment::firstOrFail();
    expect($payment->status)->toBe('Pending');
    expect($payment->processed_by)->toBe($staff->id);
    expect($payment->reviewed_by)->toBeNull();
    expect($payment->proof_image_path)->toBeNull();
    expect($order->fresh()->payment_status)->toBe('Unpaid');
    $this->get(route('manager.payments.show', $payment))->assertOk()->assertDontSee('Confirm Payment')->assertDontSee('Print Receipt');
});

test('staff electronic payments store private image proof and remain pending', function (string $method, string $extension): void {
    $staff = User::factory()->create(['role' => 'manager']);
    $order = OrderFactory::new()->create(['total' => 300]);
    $this->actingAs($staff)->post(route('manager.payments.store'), array_merge(paymentApprovalDetails($order, $method), [
        'proof_image' => UploadedFile::fake()->image('proof.'.$extension),
    ]))->assertSessionHasNoErrors();
    $payment = Payment::firstOrFail();
    Storage::disk('local')->assertExists($payment->proof_image_path);
    expect($payment->status)->toBe('Pending');
    $this->get(route('payments.proof', $payment))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
})->with([['GCash', 'jpg'], ['GCash', 'png'], ['Bank Transfer', 'webp']]);

test('electronic payments reject missing oversized or invalid proofs', function (string $case): void {
    $staff = User::factory()->create(['role' => 'manager']);
    $order = OrderFactory::new()->create(['total' => 300]);
    $details = paymentApprovalDetails($order, 'GCash');
    if ($case === 'large') {
        $details['proof_image'] = UploadedFile::fake()->image('large.jpg')->size(10241);
    } elseif ($case === 'text') {
        $details['proof_image'] = UploadedFile::fake()->create('image.jpg', 1, 'text/plain');
    } elseif ($case === 'svg') {
        $details['proof_image'] = UploadedFile::fake()->create('image.svg', 1, 'image/svg+xml');
    }
    $this->actingAs($staff)->post(route('manager.payments.store'), $details)->assertSessionHasErrors('proof_image');
    $this->assertDatabaseCount('payments', 0);
    expect(Storage::disk('local')->allFiles('payment-proofs'))->toBe([]);
})->with(['missing', 'large', 'text', 'svg']);

test('admin confirmation updates paid totals once without changing the processor', function (): void {
    $staff = User::factory()->create(['role' => 'manager']);
    $admin = User::factory()->create(['role' => 'admin']);
    $order = OrderFactory::new()->create(['total' => 300, 'payment_status' => 'Unpaid']);
    $payment = PaymentFactory::new()->for($order)->create(['status' => 'Pending', 'amount' => 100, 'processed_by' => $staff->id]);
    $pendingIncome = $this->actingAs($admin)->get(route('admin.dashboard'))->viewData('stats')['income'];
    expect((float) $pendingIncome)->toBe(0.0);
    $this->patch(route('admin.payments.review', $payment), ['status' => 'Confirmed'])->assertSessionHasNoErrors();
    expect($payment->fresh()->reviewed_by)->toBe($admin->id);
    expect($payment->fresh()->processed_by)->toBe($staff->id);
    expect($order->fresh()->payment_status)->toBe('Partial');
    expect((float) $this->get(route('admin.dashboard'))->viewData('stats')['income'])->toBe(100.0);
    $this->patch(route('admin.payments.review', $payment), ['status' => 'Confirmed'])->assertStatus(409);
    expect((float) $order->payments()->where('status', 'Confirmed')->sum('amount'))->toBe(100.0);
    $second = PaymentFactory::new()->for($order)->create(['status' => 'Pending', 'amount' => 200]);
    $this->patch(route('admin.payments.review', $second), ['status' => 'Confirmed'])->assertSessionHasNoErrors();
    expect($order->fresh()->payment_status)->toBe('Paid');
});

test('admin rejects with a required reason visible to staff and the payment owner', function (): void {
    $owner = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $owner->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 300, 'payment_status' => 'Unpaid']);
    $payment = PaymentFactory::new()->for($order)->create(['status' => 'Pending']);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->patch(route('admin.payments.review', $payment), ['status' => 'Rejected'])
        ->assertSessionHasErrors('rejection_reason');
    expect($payment->fresh()->status)->toBe('Pending');
    $this->patch(route('admin.payments.review', $payment), ['status' => 'Rejected', 'rejection_reason' => 'Reference does not match <script>bad</script>'])
        ->assertSessionHasNoErrors();
    expect($order->fresh()->payment_status)->toBe('Unpaid');
    foreach ([['manager', 'manager.payments.show'], ['customer', 'customer.payments.show']] as [$role,$route]) {
        $user = $role === 'customer' ? $owner : User::factory()->create(['role' => 'manager']);
        $this->actingAs($user)->get(route($route, $payment))->assertSee('Reference does not match &lt;script&gt;bad&lt;/script&gt;', false);
    }
    $this->get(route('payments.receipt', $payment))->assertForbidden();
});

test('staff resubmits a rejected customer payment using its existing proof', function (): void {
    $staff = User::factory()->create(['role' => 'manager']);
    $order = OrderFactory::new()->create(['total' => 300]);
    Storage::disk('local')->put('payment-proofs/original.jpg', 'existing private proof');
    $payment = PaymentFactory::new()->for($order)->create(['status' => 'Rejected', 'payment_method' => 'GCash', 'amount' => 100,
        'proof_image_path' => 'payment-proofs/original.jpg', 'rejection_reason' => 'Incorrect amount']);
    $this->actingAs($staff)->put(route('manager.payments.update', $payment), array_merge(paymentApprovalDetails($order, 'GCash'), ['amount' => 120, 'status' => 'Confirmed']))
        ->assertSessionHasNoErrors();
    expect($payment->fresh()->status)->toBe('Pending');
    expect($payment->fresh()->rejection_reason)->toBeNull();
    expect($payment->fresh()->proof_image_path)->toBe('payment-proofs/original.jpg');
    expect($payment->fresh()->processed_by)->toBe($staff->id);
    $this->assertDatabaseCount('payments', 1);
    Storage::disk('local')->assertExists('payment-proofs/original.jpg');
});

test('pending and confirmed payments cannot be edited by staff', function (string $status): void {
    $payment = PaymentFactory::new()->create(['status' => $status]);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->get(route('manager.payments.edit', $payment))->assertForbidden();
    $this->put(route('manager.payments.update', $payment), paymentApprovalDetails($payment->order))->assertForbidden();
})->with(['Pending', 'Confirmed']);

test('duplicate submissions and amounts exceeding reserved balances are rejected', function (): void {
    $order = OrderFactory::new()->create(['total' => 300]);
    $this->actingAs(User::factory()->create(['role' => 'manager']));
    $details = paymentApprovalDetails($order);
    $this->post(route('manager.payments.store'), $details)->assertSessionHasNoErrors();
    $this->post(route('manager.payments.store'), $details)->assertSessionHasErrors('reference_number');
    $this->post(route('manager.payments.store'), array_merge($details, ['amount' => 201, 'reference_number' => 'OTHER']))->assertSessionHasErrors('amount');
    $this->assertDatabaseCount('payments', 1);
});

test('only admin may review payments and only staff may submit staff payments', function (string $role): void {
    $payment = PaymentFactory::new()->create(['status' => 'Pending']);
    $this->actingAs(User::factory()->create(['role' => $role]))->patch(route('admin.payments.review', $payment), ['status' => 'Confirmed'])->assertRedirect(route($role.'.dashboard'));
    expect($payment->fresh()->status)->toBe('Pending');
    $this->patch('/manager/payments/'.$payment->id.'/review', ['status' => 'Confirmed'])->assertNotFound();
    if ($role === 'customer') {
        $this->post(route('manager.payments.store'), paymentApprovalDetails($payment->order))->assertRedirect(route('customer.dashboard'));
    }
})->with(['manager', 'customer']);

test('customer submission reuses its secure upload and ownership restrictions', function (): void {
    $owner = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $owner->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 300]);
    $details = paymentApprovalDetails($order, 'Bank Transfer');
    $details['proof_image'] = UploadedFile::fake()->image('customer.png');
    $this->actingAs($owner)->post(route('customer.payments.checkout.store', $order), $details)->assertSessionHasNoErrors();
    $payment = Payment::firstOrFail();
    expect($payment->status)->toBe('Pending');
    $this->get(route('payments.proof', $payment))->assertOk();
    $this->actingAs(User::factory()->create(['role' => 'manager']))->get(route('manager.payments.show', $payment))->assertOk()->assertSee('>View</span>', false);
    $this->actingAs(User::factory()->create(['role' => 'customer']))->get(route('payments.proof', $payment))->assertNotFound();
    $this->post(route('customer.payments.checkout.store', $order), $details)->assertNotFound();
    $this->get(route('customer.payments.show', $payment))->assertNotFound();
});

test('official receipts are available only for confirmed payments', function (string $role, string $status, int $expected): void {
    $user = User::factory()->create(['role' => $role]);
    $customer = CustomerFactory::new()->create(['email' => $user->email]);
    $order = OrderFactory::new()->for($customer)->create();
    $payment = PaymentFactory::new()->for($order)->create(['status' => $status, 'processed_by' => $user->id]);
    $this->actingAs($user)->get(route('payments.receipt', $payment))->assertStatus($expected);
})->with([
    ['admin', 'Pending', 403], ['admin', 'Rejected', 403], ['admin', 'Confirmed', 200],
    ['manager', 'Pending', 404], ['manager', 'Confirmed', 200], ['customer', 'Pending', 403], ['customer', 'Confirmed', 200],
]);

test('confirmation rejects missing electronic proof and legacy overpayment', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = OrderFactory::new()->create(['total' => 100]);
    $payment = PaymentFactory::new()->for($order)->create(['status' => 'Pending', 'amount' => 100, 'payment_method' => 'GCash']);
    $this->actingAs($admin)->patch(route('admin.payments.review', $payment), ['status' => 'Confirmed'])->assertSessionHasErrors('proof_image');
    $payment->update(['payment_method' => 'Cash', 'amount' => 101]);
    $this->patch(route('admin.payments.review', $payment), ['status' => 'Confirmed'])->assertSessionHasErrors('amount');
    expect($payment->fresh()->status)->toBe('Pending');
});

test('approval migration preserves historical receipts and derives the correct balance', function (): void {
    $path = 'database/migrations/2026_10_13_192750_add_admin_review_fields_to_payments_table.php';
    $migration = require base_path($path);
    $migration->down();
    $order = OrderFactory::new()->create(['total' => 150, 'payment_status' => 'Paid']);
    $payment = PaymentFactory::new()->for($order)->create(['amount' => 75, 'status' => 'Completed', 'proof_image_path' => 'payment-proofs/legacy.jpg']);
    $rejected = PaymentFactory::new()->for($order)->create(['amount' => 50, 'status' => 'Failed']);
    $migration->up();
    $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'Confirmed', 'amount' => 75, 'proof_image_path' => 'payment-proofs/legacy.jpg']);
    $this->assertDatabaseHas('payments', ['id' => $rejected->id, 'status' => 'Rejected']);
    $this->assertDatabaseCount('payments', 2);
    expect($order->fresh()->payment_status)->toBe('Partial');
});

test('replacement proofs survive resubmission while failed attempts leave no orphan files', function (): void {
    $order = OrderFactory::new()->create(['total' => 150]);
    Storage::disk('local')->put('payment-proofs/original.jpg', 'old image');
    $payment = PaymentFactory::new()->for($order)->create(['status' => 'Rejected', 'proof_image_path' => 'payment-proofs/original.jpg', 'payment_method' => 'GCash']);
    $this->actingAs(User::factory()->create(['role' => 'manager']));
    $details = array_merge(paymentApprovalDetails($order, 'GCash'), ['amount' => 151, 'proof_image' => UploadedFile::fake()->image('new.png')]);
    $this->put(route('manager.payments.update', $payment), $details)->assertSessionHasErrors('amount');
    expect(Storage::disk('local')->allFiles('payment-proofs'))->toBe(['payment-proofs/original.jpg']);
    $details['amount'] = 100;
    $this->put(route('manager.payments.update', $payment), $details)->assertSessionHasNoErrors();
    Storage::disk('local')->assertMissing('payment-proofs/original.jpg');
    Storage::disk('local')->assertExists($payment->fresh()->proof_image_path);
    expect($payment->fresh()->status)->toBe('Pending');
});

test('duplicate transfer references cannot be submitted on different orders', function (): void {
    $this->actingAs(User::factory()->create(['role' => 'manager']));
    $first = OrderFactory::new()->create(['total' => 300]);
    $second = OrderFactory::new()->create(['total' => 300]);
    $details = paymentApprovalDetails($first, 'GCash');
    $details['proof_image'] = UploadedFile::fake()->image('proof.png');
    $this->post(route('manager.payments.store'), $details)->assertSessionHasNoErrors();
    $details['order_id'] = $second->id;
    $details['reference_number'] = strtolower($details['reference_number']);
    $this->post(route('manager.payments.store'), $details)->assertSessionHasErrors('reference_number');
    $this->assertDatabaseCount('payments', 1);
    expect(count(Storage::disk('local')->allFiles('payment-proofs')))->toBe(1);
});

test('historical duplicate transfers cannot be confirmed twice', function (): void {
    $order = OrderFactory::new()->create(['total' => 300]);
    Storage::disk('local')->put('payment-proofs/proof.png', 'existing image');
    $first = PaymentFactory::new()->for($order)->create(['status' => 'Pending', 'amount' => 100, 'payment_method' => 'GCash', 'reference_number' => 'DUPLICATE', 'proof_image_path' => 'payment-proofs/proof.png']);
    $second = PaymentFactory::new()->for($order)->create(['status' => 'Pending', 'amount' => 100, 'payment_method' => 'GCash', 'reference_number' => 'DUPLICATE', 'proof_image_path' => 'payment-proofs/proof.png']);
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $this->patch(route('admin.payments.review', $first), ['status' => 'Confirmed'])->assertSessionHasNoErrors();
    $this->patch(route('admin.payments.review', $second), ['status' => 'Confirmed'])->assertSessionHasErrors('reference_number');
    expect($second->fresh()->status)->toBe('Pending');
});

test('customer upload validation still rejects missing proofs and cross-owner files', function (): void {
    $owner = User::factory()->create(['role' => 'customer']);
    $customer = CustomerFactory::new()->create(['email' => $owner->email]);
    $order = OrderFactory::new()->for($customer)->create(['total' => 300]);
    $this->actingAs($owner)->post(route('customer.payments.checkout.store', $order), paymentApprovalDetails($order, 'GCash'))->assertSessionHasErrors('proof_image');
    $this->assertDatabaseCount('payments', 0);
    $this->actingAs(User::factory()->create(['role' => 'customer']))->post(route('customer.payments.checkout.store', $order), paymentApprovalDetails($order))->assertNotFound();
});

test('pending conflict page identifies the confirmed receipt and reports all blocking issues', function (): void {
    Storage::disk('local')->put('payment-proofs/conflict.jpg', 'existing proof');
    $confirmed = PaymentFactory::new()->create(['status' => 'Confirmed', 'payment_method' => 'GCash', 'reference_number' => 'CONFLICT']);
    $order = OrderFactory::new()->create(['total' => 150]);
    $pending = PaymentFactory::new()->for($order)->create(['status' => 'Pending', 'amount' => 165, 'payment_method' => 'GCash', 'reference_number' => 'CONFLICT', 'proof_image_path' => 'payment-proofs/conflict.jpg']);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.payments.show', $pending))
        ->assertOk()->assertSee('Payment #'.$pending->id.' is still Pending and needs correction.')
        ->assertSee('Confirmed Payment #'.$confirmed->id)->assertSee(route('admin.payments.show', $confirmed), false)
        ->assertSee('165.00')->assertSee('150.00');
    $this->patch(route('admin.payments.review', $pending), ['status' => 'Confirmed'])->assertSessionHasErrors(['reference_number', 'amount']);
    expect($pending->fresh()->status)->toBe('Pending');
});
