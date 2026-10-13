<?php

use App\Models\User;
use Database\Factories\StaffFactory;
use Illuminate\Database\Schema\Blueprint;
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
});

test('admin can view staff details with the existing navigation and theme', function (string $status, string $theme): void {
    $staff = StaffFactory::new()->create(['name' => 'Alex <Example>', 'email' => 'alex@example.com', 'phone' => '09123456789', 'role' => 'Manager', 'status' => $status]);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->withUnencryptedCookie('lavea_theme', $theme)
        ->get(route('admin.staff.show', $staff))->assertOk()->assertViewIs('admin.staff.show')
        ->assertSee('Alex &lt;Example&gt;', false)->assertSee('alex@example.com')->assertSee('09123456789')
        ->assertSee('Manager')->assertSee($status)->assertSee('Back to Staff')->assertSee('STAFF SINCE')
        ->assertSee($staff->created_at->format('F d, Y'))->assertSee('lavea-admin-topbar')->assertSee('class="sidebar"', false);
})->with(['Active', 'Inactive'])->with(['light', 'dark']);

test('admin can edit staff contact details using the populated form', function (): void {
    $staff = StaffFactory::new()->create(['name' => 'Alex Example', 'email' => 'alex@example.com', 'role' => 'Manager', 'status' => 'Inactive']);
    $user = User::factory()->create(['email' => $staff->email, 'role' => 'manager']);

    $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('admin.staff.edit', $staff))->assertOk()
        ->assertSee('Edit Staff')->assertSee('value="Alex Example"', false)
        ->assertSee('name="_method" value="PUT"', false)
        ->assertDontSee('name="password"', false);

    $this->put(route('admin.staff.update', $staff), [
        'name' => 'Updated Staff', 'email' => 'updated@example.com',
        'phone' => '09123456789', 'role' => 'Laundry Attendant', 'status' => 'Active',
    ])->assertRedirect(route('admin.staff.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('staff', ['id' => $staff->id, 'name' => 'Updated Staff', 'email' => 'updated@example.com', 'status' => 'Active']);
    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name, 'email' => 'alex@example.com', 'role' => 'manager']);
});
