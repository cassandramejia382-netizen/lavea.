<?php

use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ScheduleFactory;
use Database\Factories\ServiceFactory;
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

test('every admin page renders the dashboard navigation and topbar once', function (string $page, ?string $recordType): void {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $parameters = match ($recordType) {
        'customer' => CustomerFactory::new()->create(),
        'staff' => StaffFactory::new()->create(),
        'service' => ServiceFactory::new()->create(),
        'order' => OrderFactory::new()->create(),
        'payment' => PaymentFactory::new()->create(),
        'schedule' => ScheduleFactory::new()->create(),
        default => [],
    };
    $dashboard = $this->get(route('admin.dashboard'))->assertOk()->getContent();

    $response = $this->get(route('admin.'.$page, $parameters));

    $response->assertOk()->assertSee('css/admin-layout.css', false)
        ->assertSee('class="content lavea-admin-content"', false);
    $html = $response->getContent();
    $extract = function (string $markup, string $pattern): string {
        preg_match($pattern, $markup, $matches);

        return preg_replace('/\s+(?:class="(?:active)?"|aria-current="page")/', '', $matches[0] ?? '');
    };
    foreach (['/<aside class="sidebar">.*?<\/aside>/s', '/<header class="lavea-admin-topbar">.*?<\/header>/s'] as $pattern) {
        expect($extract($html, $pattern))->not->toBe('')->toBe($extract($dashboard, $pattern));
    }
    expect(substr_count($html, '<aside class="sidebar">'))->toBe(1);
    expect(substr_count($html, '<header class="lavea-admin-topbar">'))->toBe(1);
})->with([
    'dashboard' => ['dashboard', null],
    'customers' => ['customers.index', null],
    'customer details' => ['customers.show', 'customer'],
    'staff' => ['staff.index', null],
    'create staff' => ['staff.create', null],
    'staff details' => ['staff.show', 'staff'],
    'services' => ['services.index', null],
    'create service' => ['services.create', null],
    'edit service' => ['services.edit', 'service'],
    'service details' => ['services.show', 'service'],
    'orders' => ['orders.index', null],
    'order details' => ['orders.show', 'order'],
    'payments' => ['payments.index', null],
    'payment details' => ['payments.show', 'payment'],
    'schedules' => ['schedules.index', null],
    'create schedule' => ['schedules.create', null],
    'edit schedule' => ['schedules.edit', 'schedule'],
    'schedule details' => ['schedules.show', 'schedule'],
    'reports' => ['reports', null],
    'settings' => ['settings', null],
]);
