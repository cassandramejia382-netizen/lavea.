<?php

use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\StaffFactory;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../../vendor/autoload.php';

if (getenv('APP_ENV') !== 'testing' || ! str_contains(getenv('DB_DATABASE') ?: '', 'lavea-logout-browser-')) {
    throw new RuntimeException('Browser fixtures require an isolated test database.');
}

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

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
Artisan::call('migrate', ['--no-interaction' => true]);

foreach (['admin', 'staff', 'customer'] as $role) {
    $user = User::factory()->create([
        'role' => $role,
        'email' => $role.'@logout-browser.test',
        'password' => 'Browser-test-password-123',
    ]);
    if ($role === 'customer') {
        CustomerFactory::new()->create(['email' => $user->email]);
    }
    if ($role === 'staff') {
        StaffFactory::new()->create(['email' => $user->email]);
    }
}
