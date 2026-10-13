<?php

use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\ServiceFactory;
use Database\Factories\StaffFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

function assignmentOrderDetails(Service $service, Staff $worker, ?Order $order = null): array
{
    return ['customer_id' => $order?->customer_id ?? CustomerFactory::new()->create()->id, 'service_id' => $service->id, 'staff_id' => $worker->id, 'quantity' => 2, 'order_date' => today()->toDateString(), 'status' => 'Pending'];
}

test('staff forms use supported job positions and preserve a legacy position without changing logins', function (): void {
    $manager = User::factory()->create(['role' => 'manager']);
    $legacy = StaffFactory::new()->create(['role' => 'Washer']);
    $this->actingAs($manager)->get(route('manager.staff.create'))->assertSee('Laundry Attendant')->assertSee('Dry Cleaning Attendant')->assertSee('Ironing Attendant')->assertDontSee('name="password"', false)->assertDontSee('job-positions');
    $this->get(route('manager.staff.edit', $legacy))->assertSee('value="Washer"', false)->assertSee('value="Other" selected', false);
    $this->put(route('manager.staff.update', $legacy), ['name' => $legacy->name, 'role' => 'Washer', 'status' => 'Active'])->assertSessionHasNoErrors();
    expect($legacy->fresh()->role)->toBe('Washer');
    $this->post(route('manager.staff.store'), ['name' => 'Bad position', 'role' => 'Washer', 'status' => 'Active'])->assertSessionHasErrors('role');
    $this->put(route('manager.staff.update', $legacy), ['name' => $legacy->name, 'role' => 'Manager', 'status' => 'Active'])->assertSessionHasErrors('role');
    $this->assertDatabaseCount('users', 1);
});

test('service staff options rank matching active workers by unfinished workload and deterministic ties', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $busy = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $idle = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $alsoIdle = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    StaffFactory::new()->create(['role' => 'Laundry Attendant', 'status' => 'Inactive']);
    StaffFactory::new()->create(['role' => 'Ironing Attendant']);
    foreach (['Pending', 'Processing'] as $status) {
        OrderFactory::new()->for($service)->create(['staff_id' => $busy->id, 'status' => $status]);
    }
    foreach (['Completed', 'Cancelled'] as $status) {
        OrderFactory::new()->for($service)->create(['staff_id' => $idle->id, 'status' => $status]);
    }
    $this->actingAs(User::factory()->create(['role' => 'manager']))->getJson(route('manager.services.staff-options', $service))
        ->assertJsonCount(3, 'staff')->assertJsonPath('staff.0.id', $idle->id)->assertJsonPath('staff.1.id', $alsoIdle->id)->assertJsonPath('staff.2.id', $busy->id)->assertJsonPath('staff.0.unfinished_orders_count', 0)->assertJsonPath('staff.2.unfinished_orders_count', 2);
});

test('configured service ids choose the matching job position after services are renamed', function (string $position): void {
    $service = ServiceFactory::new()->create(['service_name' => 'Custom renamed service', 'required_job_position' => $position]);
    $worker = StaffFactory::new()->create(['role' => $position]);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->getJson(route('manager.services.staff-options', $service))->assertJsonCount(1, 'staff')->assertJsonPath('staff.0.id', $worker->id);
    $this->post(route('manager.orders.store'), assignmentOrderDetails($service, $worker))->assertSessionHasNoErrors();
    expect(Order::firstOrFail()->staff_id)->toBe($worker->id);
})->with(Staff::LAUNDRY_JOB_POSITIONS);

test('orders automatically choose the least busy qualified worker without a posted staff id', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $busy = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    OrderFactory::new()->for($service)->create(['staff_id' => $busy->id]);
    $chosen = StaffFactory::new()->create(['role' => 'Laundry Attendant', 'name' => 'Automatic choice']);
    $details = assignmentOrderDetails($service, $chosen);
    unset($details['staff_id']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->post(route('manager.orders.store'), $details)->assertSessionHasNoErrors();
    $order = Order::latest('id')->firstOrFail();
    expect($order->staff_id)->toBe($chosen->id);
    $this->get(route('manager.orders.index'))->assertSee('Assigned Staff')->assertSee('Automatic choice');
    $this->get(route('manager.orders.show', $order))->assertSee('Automatic choice');
});

test('automatic assignment excludes managers and unrelated positions and rejects manual overrides', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $laundry = StaffFactory::new()->create(['name' => 'Kristina Cassandra', 'role' => 'Laundry Attendant']);
    $dryCleaning = StaffFactory::new()->create(['name' => 'Jared Ragas', 'role' => 'Dry Cleaning Attendant']);
    $ironing = StaffFactory::new()->create(['name' => 'Mae Aguandan', 'role' => 'Ironing Attendant']);
    StaffFactory::new()->create(['name' => 'Manager record', 'role' => 'Manager']);
    StaffFactory::new()->create(['name' => 'Delivery worker', 'role' => 'Delivery Rider']);
    StaffFactory::new()->create(['name' => 'Inactive worker', 'status' => 'Inactive']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->getJson(route('manager.services.staff-options', $service))
        ->assertJsonCount(1, 'staff')->assertJsonPath('staff.0.name', $laundry->name);
    $this->post(route('manager.orders.store'), assignmentOrderDetails($service, $dryCleaning))->assertSessionHasErrors('staff_id');
    $details = assignmentOrderDetails($service, $laundry);
    unset($details['staff_id']);
    $this->post(route('manager.orders.store'), $details)->assertSessionHasNoErrors();
    $order = Order::sole();
    expect($order->staff_id)->toBe($laundry->id);
    $this->put(route('manager.orders.update', $order), assignmentOrderDetails($service, $ironing, $order))->assertSessionHasErrors('staff_id');
    expect($order->fresh()->staff_id)->toBe($laundry->id);
    $this->get(route('manager.orders.edit', $order))->assertSee('id="assigned_staff"', false)->assertDontSee('name="staff_id"', false);
});

test('pending manager assignments are automatically replaced with an active service worker', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $managerRecord = StaffFactory::new()->create(['role' => 'Manager']);
    $worker = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $order = OrderFactory::new()->for($service)->create(['staff_id' => $managerRecord->id]);
    $details = assignmentOrderDetails($service, $worker, $order);
    unset($details['staff_id']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->put(route('manager.orders.update', $order), $details)->assertSessionHasNoErrors();
    expect($order->fresh()->staff_id)->toBe($worker->id);
});

test('saving preserves valid assignments and replaces newly inactive workers automatically', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $assigned = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $replacement = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $order = OrderFactory::new()->for($service)->create(['staff_id' => $assigned->id]);
    OrderFactory::new()->for($service)->create(['staff_id' => $assigned->id]);
    $details = assignmentOrderDetails($service, $assigned, $order);
    unset($details['staff_id']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->put(route('manager.orders.update', $order), $details)->assertSessionHasNoErrors();
    expect($order->fresh()->staff_id)->toBe($assigned->id);
    $assigned->update(['status' => 'Inactive']);
    $this->put(route('manager.orders.update', $order), $details)->assertSessionHasNoErrors();
    expect($order->fresh()->staff_id)->toBe($replacement->id);
});

test('automatic assignment rechecks availability and permits submission without manual selection', function (string $condition): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $worker = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->getJson(route('manager.services.staff-options', $service))->assertJsonCount(1, 'staff');
    $details = assignmentOrderDetails($service, $worker);
    if ($condition === 'inactive') {
        $worker->update(['status' => 'Inactive']);
    } elseif ($condition === 'unrelated') {
        $worker->update(['role' => 'Dry Cleaning Attendant']);
    } elseif ($condition === 'missing') {
        $worker->delete();
    } elseif ($condition === 'unselected') {
        unset($details['staff_id']);
    } else {
        $service->update(['required_job_position' => 'Ironing Attendant']);
    }
    if ($condition === 'unselected') {
        $this->post(route('manager.orders.store'), $details)->assertSessionHasNoErrors();
        expect(Order::sole()->staff_id)->toBe($worker->id);
    } else {
        $this->post(route('manager.orders.store'), $details)->assertSessionHasErrors('staff_id');
        $this->assertDatabaseCount('orders', 0);
    }
})->with(['inactive', 'unrelated', 'missing', 'unselected', 'mapping changed']);

test('unmapped services remain unavailable for assignment until configured by the manager', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => null, 'price' => 75]);
    $worker = StaffFactory::new()->create(['role' => 'Ironing Attendant']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->getJson(route('manager.services.staff-options', $service))->assertJsonCount(0, 'staff')->assertJsonPath('position', null);
    $this->post(route('manager.orders.store'), assignmentOrderDetails($service, $worker))->assertSessionHasErrors('service_id');
    $this->patch(route('manager.services.assignment-position', $service), ['required_job_position' => 'Ironing Attendant', 'price' => 1, 'service_name' => 'Forged', 'is_active' => false])->assertSessionHasNoErrors();
    expect((float) $service->fresh()->price)->toBe(75.0);
    expect($service->fresh()->service_name)->toBe($service->service_name);
    expect($service->fresh()->is_active)->toBeTrue();
    $this->post(route('manager.orders.store'), assignmentOrderDetails($service->fresh(), $worker))->assertSessionHasNoErrors();
});

test('no matching staff and inactive services prevent automatic assignment', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Dry Cleaning Attendant']);
    $worker = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->getJson(route('manager.services.staff-options', $service))->assertJsonCount(0, 'staff');
    $this->post(route('manager.orders.store'), assignmentOrderDetails($service, $worker))->assertSessionHasErrors('staff_id');
    $service->update(['is_active' => false]);
    $this->getJson(route('manager.services.staff-options', $service))->assertJsonCount(0, 'staff')->assertJsonPath('active', false);
    $this->post(route('manager.orders.store'), assignmentOrderDetails($service, $worker))->assertSessionHasErrors('service_id');
    $this->assertDatabaseCount('orders', 0);
});

test('service changes before work starts recalculate automatic staff assignment', function (): void {
    $laundry = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $ironing = ServiceFactory::new()->create(['required_job_position' => 'Ironing Attendant']);
    $laundryWorker = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $ironingWorker = StaffFactory::new()->create(['role' => 'Ironing Attendant']);
    $order = OrderFactory::new()->for($laundry)->create(['staff_id' => $laundryWorker->id]);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->put(route('manager.orders.update', $order), assignmentOrderDetails($ironing, $laundryWorker, $order))->assertSessionHasErrors('staff_id');
    expect($order->fresh()->staff_id)->toBe($laundryWorker->id);
    $details = assignmentOrderDetails($ironing, $ironingWorker, $order);
    unset($details['staff_id']);
    $this->put(route('manager.orders.update', $order), $details)->assertSessionHasNoErrors();
    expect($order->fresh()->service_id)->toBe($ironing->id);
    expect($order->fresh()->staff_id)->toBe($ironingWorker->id);
});

test('started or closed orders preserve staff and service and cannot be reset to unlock assignment', function (string $status): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $worker = StaffFactory::new()->create(['role' => 'Washer', 'status' => 'Inactive']);
    $replacement = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $otherService = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $order = OrderFactory::new()->for($service)->create(['staff_id' => $worker->id, 'status' => $status]);
    $details = array_merge(assignmentOrderDetails($service, $worker, $order), ['status' => $status, 'notes' => 'Progress noted']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->put(route('manager.orders.update', $order), $details)->assertSessionHasNoErrors();
    expect($order->fresh()->staff_id)->toBe($worker->id);
    $this->put(route('manager.orders.update', $order), array_merge($details, ['staff_id' => $replacement->id]))->assertSessionHasErrors('staff_id');
    $this->put(route('manager.orders.update', $order), array_merge($details, ['service_id' => $otherService->id]))->assertSessionHasErrors('service_id');
    $this->put(route('manager.orders.update', $order), array_merge($details, ['status' => 'Pending']))->assertSessionHasErrors('status');
    expect($order->fresh()->staff_id)->toBe($worker->id);
    expect($order->fresh()->service_id)->toBe($service->id);
})->with(['Processing', 'Completed', 'Cancelled']);

test('availability and work status are rechecked after the assignment form was opened', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $worker = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $replacement = StaffFactory::new()->create(['role' => 'Laundry Attendant']);
    $order = OrderFactory::new()->for($service)->create(['staff_id' => $worker->id]);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->get(route('manager.orders.edit', $order))->assertOk();
    $order->update(['status' => 'Processing']);
    $this->put(route('manager.orders.update', $order), array_merge(assignmentOrderDetails($service, $replacement, $order), ['status' => 'Processing']))->assertSessionHasErrors('staff_id');
    expect($order->fresh()->staff_id)->toBe($worker->id);
});

test('legacy pending inactive assignments require available staff before saving or starting work', function (): void {
    $service = ServiceFactory::new()->create(['required_job_position' => 'Laundry Attendant']);
    $worker = StaffFactory::new()->create(['role' => 'Washer', 'status' => 'Inactive']);
    $order = OrderFactory::new()->for($service)->create(['staff_id' => $worker->id]);
    $details = array_merge(assignmentOrderDetails($service, $worker, $order), ['notes' => 'Keep history']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->put(route('manager.orders.update', $order), $details)->assertSessionHasErrors('staff_id');
    $this->put(route('manager.orders.update', $order), array_merge($details, ['status' => 'Processing']))->assertSessionHasErrors('staff_id');
    expect($order->fresh()->staff_id)->toBe($worker->id);
    expect($order->fresh()->status)->toBe('Pending');
});

test('admin service forms save supported mappings and reject unsupported positions', function (): void {
    $service = ServiceFactory::new()->create();
    $this->actingAs(User::factory()->create(['role' => 'admin']))->put(route('admin.services.update', $service), ['service_name' => $service->service_name, 'price' => 75, 'required_job_position' => 'Dry Cleaning Attendant'])->assertSessionHasNoErrors();
    expect($service->fresh()->required_job_position)->toBe('Dry Cleaning Attendant');
    $this->put(route('admin.services.update', $service), ['service_name' => $service->service_name, 'price' => 75, 'required_job_position' => 'Manager'])->assertSessionHasErrors('required_job_position');
    $this->post(route('admin.services.store'), ['service_name' => 'New service', 'price' => 100, 'required_job_position' => 'Ironing Attendant'])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('services', ['service_name' => 'New service', 'required_job_position' => 'Ironing Attendant']);
});

test('customers and guests cannot configure service assignments or read staff candidates', function (string $role): void {
    $service = ServiceFactory::new()->create(['required_job_position' => null]);
    if ($role === 'customer') {
        $user = User::factory()->create(['role' => 'customer']);
        CustomerFactory::new()->create(['email' => $user->email]);
        $this->actingAs($user);
    }
    $expected = $role === 'customer' ? route('customer.dashboard') : route('login');
    $this->get(route('manager.services.staff-options', $service))->assertRedirect($expected);
    $this->patch(route('manager.services.assignment-position', $service), ['required_job_position' => 'Laundry Attendant'])->assertRedirect($expected);
    expect($service->fresh()->required_job_position)->toBeNull();
})->with(['customer', 'guest']);

test('mapping migration configures existing services while preserving worker positions and order assignments', function (): void {
    $migration = require database_path('migrations/2026_10_13_214158_add_required_job_position_to_services_table.php');
    $migration->down();
    $worker = StaffFactory::new()->create(['role' => 'Washer']);
    $names = ['Wash & Fold' => 'Laundry Attendant', 'Bedsheet Cleaning' => 'Laundry Attendant', 'Comforter Cleaning' => 'Laundry Attendant', 'Curtain Cleaning' => 'Laundry Attendant', 'Express Laundry' => 'Laundry Attendant', 'Stain Removal' => 'Laundry Attendant', 'Dry Cleaning' => 'Dry Cleaning Attendant', 'Ironing' => 'Ironing Attendant', 'New custom service' => null];
    foreach ($names as $name => $position) {
        ServiceFactory::new()->create(['service_name' => $name]);
    }
    $order = OrderFactory::new()->create(['service_id' => Service::firstOrFail()->id, 'staff_id' => $worker->id]);
    $migration->up();
    foreach ($names as $name => $position) {
        expect(Service::where('service_name', $name)->firstOrFail()->required_job_position)->toBe($position);
    }
    expect($worker->fresh()->role)->toBe('Washer');
    expect($order->fresh()->staff_id)->toBe($worker->id);
});
