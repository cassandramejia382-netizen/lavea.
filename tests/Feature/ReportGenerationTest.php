<?php

use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ServiceFactory;
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

test('generate displays the selected report with a working form and preserved filters', function (string $type): void {
    $response = $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('admin.reports', ['report_type' => $type, 'from_date' => '2026-10-10', 'to_date' => '2026-10-13']))
        ->assertOk()->assertSee('method="GET"', false)->assertSee('type="submit"', false)
        ->assertSee('name="from_date"', false)->assertSee('value="2026-10-10"', false);
    foreach (['orders', 'payments', 'services'] as $section) {
        if ($type === 'all' || $section === $type) {
            $response->assertSee('id="'.$section.'-report"', false);
        } else {
            $response->assertDontSee('id="'.$section.'-report"', false);
        }
    }
})->with(['all', 'orders', 'payments', 'services']);

test('orders use inclusive historical order dates and update summary totals', function (): void {
    $customer = CustomerFactory::new()->create();
    $service = ServiceFactory::new()->create();
    $start = OrderFactory::new()->create(['customer_id' => $customer->id, 'service_id' => $service->id, 'order_date' => '2026-10-10', 'total' => 120]);
    $end = OrderFactory::new()->create(['customer_id' => $customer->id, 'service_id' => $service->id, 'order_date' => '2026-10-13', 'total' => 180]);
    OrderFactory::new()->create(['order_date' => '2026-10-09']);
    OrderFactory::new()->create(['order_date' => '2026-10-14']);
    $response = $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.reports', ['report_type' => 'orders', 'from_date' => '2026-10-10', 'to_date' => '2026-10-13']))->assertOk();
    expect($response->viewData('orders')->pluck('id')->all())->toBe([$end->id, $start->id]);
    expect($response->viewData('summary'))->toMatchArray(['orders' => 2, 'customers' => 1, 'services' => 1, 'order_value' => 300]);
});

test('payments filter payment dates and count only confirmed amounts as income', function (): void {
    $order = OrderFactory::new()->create(['order_date' => '2026-09-01']);
    $confirmed = PaymentFactory::new()->create(['order_id' => $order->id, 'payment_date' => '2026-10-10', 'amount' => 40]);
    $pending = PaymentFactory::new()->create(['order_id' => $order->id, 'payment_date' => '2026-10-13', 'amount' => 70, 'status' => 'Pending']);
    $rejected = PaymentFactory::new()->create(['order_id' => $order->id, 'payment_date' => '2026-10-11', 'amount' => 30, 'status' => 'Rejected']);
    PaymentFactory::new()->create(['payment_date' => '2026-10-14', 'amount' => 900]);
    $response = $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.reports', ['report_type' => 'payments', 'from_date' => '2026-10-10', 'to_date' => '2026-10-13']))->assertOk();
    expect($response->viewData('payments')->pluck('id')->all())->toBe([$pending->id, $rejected->id, $confirmed->id]);
    expect($response->viewData('summary'))->toMatchArray(['payments' => 3, 'income' => 40, 'orders' => 0]);
    $this->assertDatabaseHas('payments', ['id' => $pending->id, 'status' => 'Pending']);
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'Unpaid']);
});

test('services aggregate matching orders and show zero usage for unused services', function (): void {
    $service = ServiceFactory::new()->create();
    $unused = ServiceFactory::new()->create();
    OrderFactory::new()->create(['service_id' => $service->id, 'order_date' => '2026-10-10', 'quantity' => 1.5, 'total' => 75]);
    OrderFactory::new()->create(['service_id' => $service->id, 'order_date' => '2026-10-13', 'quantity' => 2, 'total' => 100]);
    OrderFactory::new()->create(['service_id' => $service->id, 'order_date' => '2026-10-14', 'quantity' => 20, 'total' => 1000]);
    $response = $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.reports', ['report_type' => 'services', 'from_date' => '2026-10-10', 'to_date' => '2026-10-13']))->assertOk();
    $rows = $response->viewData('services')->keyBy('id');
    expect($rows[$service->id]->report_order_count)->toBe(2);
    expect((float) $rows[$service->id]->report_quantity)->toBe(3.5);
    expect((float) $rows[$service->id]->report_order_value)->toBe(175.0);
    expect($rows[$unused->id]->report_order_count)->toBe(0);
});

test('invalid report filters return validation errors', function (array $filters, string $field): void {
    $this->actingAs(User::factory()->create(['role' => 'admin']))->from(route('admin.reports'))
        ->get(route('admin.reports', $filters))->assertRedirect(route('admin.reports'))->assertSessionHasErrors($field);
    $this->get(route('admin.reports'))->assertOk()->assertSee('role="alert"', false);
})->with([
    [['report_type' => 'unknown'], 'report_type'],
    [['from_date' => 'not-a-date'], 'from_date'],
    [['from_date' => '2026-10-13', 'to_date' => '2026-10-10'], 'to_date'],
]);

test('open ended ranges and empty reports work', function (): void {
    OrderFactory::new()->create(['order_date' => '2026-10-10']);
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    foreach ([['from_date' => '2026-10-10'], ['to_date' => '2026-10-10'], []] as $filters) {
        $response = $this->get(route('admin.reports', ['report_type' => 'orders', ...$filters]))->assertOk();
        expect($response->viewData('orders')->total())->toBe(1);
    }
    $this->get(route('admin.reports', ['report_type' => 'orders', 'from_date' => '2026-10-11']))->assertOk()->assertSee('No orders match');
    $this->get(route('admin.reports', ['report_type' => 'payments']))->assertOk()->assertSee('No payments match');
});

test('pagination preserves report filters and includes older results', function (): void {
    OrderFactory::new()->count(16)->create(['order_date' => '2026-10-10']);
    $filters = ['report_type' => 'orders', 'from_date' => '2026-10-10', 'to_date' => '2026-10-13'];
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $response = $this->get(route('admin.reports', $filters))->assertOk();
    expect($response->viewData('orders')->total())->toBe(16);
    expect($response->viewData('orders')->nextPageUrl())->toContain('report_type=orders', 'from_date=2026-10-10', 'to_date=2026-10-13', 'orders_page=2');
    $next = $this->get(route('admin.reports', [...$filters, 'orders_page' => 2]))->assertOk();
    expect($next->viewData('orders')->count())->toBe(1);
});

test('reports remain restricted to admins', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);
    CustomerFactory::new()->create(['email' => $user->email]);
    $this->actingAs($user)->get(route('admin.reports', ['report_type' => 'payments']))->assertRedirect(route($role.'.dashboard'));
})->with(['manager', 'customer']);

test('guests cannot generate reports', function (): void {
    $this->get(route('admin.reports'))->assertRedirect(route('login'));
});
