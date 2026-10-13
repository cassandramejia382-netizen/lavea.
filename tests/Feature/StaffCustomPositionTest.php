<?php

use App\Models\Staff;
use App\Models\User;
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

test('staff forms omit email and load custom positions as Other', function (string $role): void {
    $worker = StaffFactory::new()->create(['role' => 'Quality Checker', 'email' => 'old-worker@example.test']);
    $this->actingAs(User::factory()->create(['role' => $role]))->get(route($role.'.staff.create'))->assertDontSee('name="email"', false)->assertSee('Other')->assertSee('Custom Job Position');
    $this->get(route($role.'.staff.edit', $worker))->assertDontSee('name="email"', false)->assertSee('value="Quality Checker"', false)->assertSee('value="Other" selected', false);
})->with(['admin', 'manager']);

test('staff can be created without email or a login and custom positions can be edited', function (): void {
    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager)->post(route('manager.staff.store'), ['name' => 'Worker', 'role' => 'Other', 'custom_position' => '  Quality Checker  ', 'status' => 'Active'])->assertSessionHasNoErrors();
    $worker = Staff::firstOrFail();
    expect($worker->role)->toBe('Quality Checker');
    expect($worker->email)->toBeNull();
    $this->put(route('manager.staff.update', $worker), ['name' => 'Worker', 'role' => 'Other', 'custom_position' => 'Packing Assistant', 'status' => 'Active'])->assertSessionHasNoErrors();
    expect($worker->fresh()->role)->toBe('Packing Assistant');
    $this->assertDatabaseCount('users', 1);
    expect($manager->fresh()->email)->toBe($manager->email);
});

test('editing staff without email preserves staff data and matching manager login emails', function (): void {
    $manager = User::factory()->create(['role' => 'manager']);
    $worker = StaffFactory::new()->create(['email' => $manager->email, 'role' => 'Legacy Position']);
    $this->actingAs($manager)->put(route('manager.staff.update', $worker), ['name' => $worker->name, 'role' => 'Other', 'custom_position' => 'Legacy Position', 'status' => 'Inactive'])->assertSessionHasNoErrors();
    expect($worker->fresh()->email)->toBe($manager->email);
    expect($worker->fresh()->role)->toBe('Legacy Position');
    expect($manager->fresh()->email)->toBe($manager->email);
    expect($manager->fresh()->role)->toBe('manager');
});

test('Other requires a nonblank custom position of at most 100 characters', function (mixed $position): void {
    $this->actingAs(User::factory()->create(['role' => 'manager']))->post(route('manager.staff.store'), ['name' => 'Worker', 'role' => 'Other', 'custom_position' => $position, 'status' => 'Active'])->assertSessionHasErrors('custom_position');
    $this->assertDatabaseCount('staff', 0);
})->with([null, '', '   ', str_repeat('a', 101), [['invalid']]]);

test('standard positions ignore hidden custom text and allow switching from custom', function (): void {
    $worker = StaffFactory::new()->create(['role' => 'Quality Checker']);
    $this->actingAs(User::factory()->create(['role' => 'manager']))->put(route('manager.staff.update', $worker), ['name' => $worker->name, 'role' => 'Laundry Attendant', 'custom_position' => 'Ignored value', 'status' => 'Active'])->assertSessionHasNoErrors();
    expect($worker->fresh()->role)->toBe('Laundry Attendant');
});
