<?php

use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ScheduleFactory;
use Database\Factories\ServiceFactory;
use Database\Factories\StaffFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

test('an existing authenticated session redirects login submission to its role dashboard', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->post(route('login.submit'), ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route(($role === 'user' ? 'customer' : $role).'.dashboard'));

    $this->assertAuthenticatedAs($user);
})->with(['admin', 'staff', 'customer', 'user']);

test('customer login preserves email verification for both new and existing sessions', function (bool $isAlreadyAuthenticated): void {
    $user = User::factory()->unverified()->create(['role' => 'customer', 'password' => 'Verification-test-password-123']);
    if ($isAlreadyAuthenticated) {
        $this->actingAs($user);
    }

    $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'Verification-test-password-123'])
        ->assertRedirect(route('verification.notice'));
})->with([false, true]);

test('homepage login link returns authenticated accounts to their dashboard', function (string $role): void {
    $this->actingAs(User::factory()->create(['role' => $role]));
    $this->get(route('home'))->assertOk()
        ->assertSee('href="'.route('login').'" class="signin-btn"', false);

    $this->get(route('login'))->assertRedirect(route(($role === 'user' ? 'customer' : $role).'.dashboard'));
})->with(['admin', 'staff', 'customer', 'user']);

test('login form submits and authenticates each role without history protection', function (string $role): void {
    $user = User::factory()->create(['role' => $role, 'password' => 'Login-test-password-123']);
    $this->get(route('login'))->assertOk()
        ->assertSee('action="'.route('login.submit').'" method="POST"', false)
        ->assertSee('<button type="submit">Sign In</button>', false)
        ->assertDontSee('session-history.js');

    $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'Login-test-password-123'])
        ->assertRedirect(route(($role === 'user' ? 'customer' : $role).'.dashboard'))
        ->assertSessionHasNoErrors();

    $this->assertAuthenticatedAs($user);
})->with(['admin', 'staff', 'customer', 'user']);

test('guest and email verification routes do not use protected page cache middleware', function (string $routeName): void {
    $middleware = Route::getRoutes()->getByName($routeName)->gatherMiddleware();

    if (str_starts_with($routeName, 'verification.')) {
        expect($middleware)->toContain('auth', 'no-cache');
    } else {
        expect($middleware)->not->toContain('no-cache');
    }
})->with(['login', 'login.submit', 'register', 'register.submit', 'password.request', 'password.email', 'password.reset', 'password.update', 'verification.notice', 'verification.verify', 'verification.send']);

test('every admin staff customer and legacy user route requires authentication', function (): void {
    $protectedRoutes = collect(Route::getRoutes()->getRoutes())
        ->filter(fn (Illuminate\Routing\Route $route): bool => preg_match('#^(admin|staff|customer|user)/#', $route->uri()) === 1);

    expect($protectedRoutes)->not->toBeEmpty();
    foreach ($protectedRoutes as $route) {
        expect($route->gatherMiddleware())->toContain('auth');
    }
});

test('logout destroys the session and protected pages redirect to login for every role', function (string $role): void {
    $user = User::factory()->create(['role' => $role]);
    $prefix = $role === 'user' ? 'customer' : $role;
    $this->actingAs($user)->withSession(['logout_marker' => 'private data', '_token' => 'old-csrf-token']);
    $oldSessionId = session()->getId();
    $page = $this->get(route($prefix.'.services.index'))->assertOk()
        ->assertHeader('Pragma', 'no-cache')
        ->assertDontSee('js/session-history.js', false);
    expect($page->headers->get('Cache-Control'))->toContain('no-store', 'no-cache', 'must-revalidate', 'max-age=0');
    $page->assertHeader('Expires', '0');
    $page->assertSee('action="'.route('logout').'"', false)
        ->assertSee('data-confirm-logout', false)->assertSee('id="logout-dialog"', false)
        ->assertDontSee(route('logout.confirm'), false);

    $this->post(route('logout'))->assertRedirect(route('home'))
        ->assertSessionMissing('logout_marker');

    $this->assertGuest();
    expect(session()->getId())->not->toBe($oldSessionId);
    expect(session()->token())->not->toBe('old-csrf-token')->not->toBeNull();
    foreach (['dashboard', 'services.index', 'settings'] as $pageName) {
        $this->get(route($prefix.'.'.$pageName))->assertRedirect(route('login'));
    }
    $this->get(route('login'))->assertOk();
})->with(['admin', 'staff', 'customer', 'user']);

test('dashboard greeting follows Philippine time regardless of the server timezone', function (string $utcTime, string $greeting): void {
    config(['app.timezone' => 'UTC']);
    $this->travelTo(Carbon::parse($utcTime, 'UTC'));

    $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('<h1>'.$greeting.', Admin!</h1>', false);
})->with([
    'midnight in Manila' => ['2026-10-06 16:00:00', 'Good morning'],
    'before noon in Manila' => ['2026-10-07 03:59:59', 'Good morning'],
    'noon in Manila' => ['2026-10-07 04:00:00', 'Good afternoon'],
    'before evening in Manila' => ['2026-10-07 09:59:59', 'Good afternoon'],
    'evening in Manila' => ['2026-10-07 10:00:00', 'Good evening'],
    'before midnight in Manila' => ['2026-10-07 15:59:59', 'Good evening'],
]);

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

test('admin list searches show matching rows and preserve the search term', function (string $area, string $field): void {
    $admin = User::factory()->create(['role' => 'admin']);
    if ($area === 'services') {
        $match = ServiceFactory::new()->create(['service_name' => 'Matching Laundry', $field => 'needle']);
        ServiceFactory::new()->create(['service_name' => 'Excluded Laundry', 'description' => 'Other description']);
    } elseif ($area === 'staff') {
        $match = StaffFactory::new()->create(['name' => 'Matching Staff', $field => $field === 'email' ? 'needle@example.com' : 'needle']);
        StaffFactory::new()->create(['name' => 'Excluded Staff', 'email' => 'other@example.com', 'phone' => '09123456789']);
    } else {
        $match = OrderFactory::new()->for(ServiceFactory::new()->create(['service_name' => 'needle']))->create();
        OrderFactory::new()->for(ServiceFactory::new()->create(['service_name' => 'Excluded Laundry']))->create();
    }
    $response = $this->actingAs($admin)->get(route('admin.'.$area.'.index', ['search' => 'needle']))
        ->assertOk()->assertSee('name="search"', false)->assertSee('value="needle"', false);
    if ($area === 'services') {
        $response->assertSee($match->service_name)->assertDontSee('Excluded Laundry');
    } elseif ($area === 'staff') {
        $response->assertSee($match->name)->assertDontSee('Excluded Staff');
    } else {
        $response->assertSee('needle')->assertDontSee('Excluded Laundry');
    }
})->with([
    ['services', 'service_name'], ['services', 'description'],
    ['staff', 'name'], ['staff', 'email'], ['staff', 'phone'], ['orders', 'service_name'],
]);

test('all account profiles share the modern design and retain their own save routes', function (string $role, string $badge): void {
    $user = User::factory()->create(['role' => $role]);
    if ($role === 'customer') {
        CustomerFactory::new()->create(['email' => $user->email, 'phone' => '09123456789']);
    }
    if ($role === 'staff') {
        StaffFactory::new()->create(['email' => $user->email, 'phone' => '09123456789']);
    }
    $this->actingAs($user)->get(route($role.'.profile'))->assertOk()
        ->assertSee('class="profile-account-badge"', false)->assertSee($badge)
        ->assertSee('Personal information')->assertSee('Change photo')
        ->assertSee('action="'.route($role.'.profile.update').'"', false);
})->with([['admin', 'Administrator'], ['staff', 'Staff Account'], ['customer', 'Customer Account']]);
