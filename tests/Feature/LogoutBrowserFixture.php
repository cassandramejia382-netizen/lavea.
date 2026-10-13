<?php

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\User;
use Database\Factories\CustomerFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ScheduleFactory;
use Database\Factories\ServiceFactory;
use Database\Factories\StaffFactory;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/../../vendor/autoload.php';

if (getenv('APP_ENV') !== 'testing' || ! str_contains(getenv('DB_DATABASE') ?: '', 'lavea-logout-browser-')) {
    throw new RuntimeException('Browser fixtures require an isolated test database.');
}

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (getenv('LAVEA_BROWSER_CLEANUP') === '1') {
    $qrPath = DB::table('shop_settings')->find(1)?->gcash_qr_path;
    if ($qrPath && preg_match('/\Agcash-qr\/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/i', $qrPath)) {
        Storage::disk('public')->delete($qrPath);
    }
    foreach (Payment::whereNotNull('proof_image_path')->pluck('proof_image_path') as $proofPath) {
        if (preg_match('/\Apayment-proofs\/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/i', $proofPath)) {
            Storage::disk('local')->delete($proofPath);
        }
    }
    exit;
}

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

foreach (['admin', 'manager', 'customer'] as $role) {
    $user = User::factory()->create([
        'role' => $role,
        'email' => $role.'@logout-browser.test',
        'password' => 'Browser-test-password-123',
    ]);
    if ($role === 'customer') {
        CustomerFactory::new()->create(['email' => $user->email]);
    }
    if ($role === 'manager') {
        StaffFactory::new()->create(['email' => $user->email, 'name' => 'Browser Laundry Worker', 'role' => 'Laundry Attendant']);
    }
}

if (getenv('LAVEA_BROWSER_VERIFICATION') === '1') {
    User::factory()->unverified()->create([
        'role' => 'customer',
        'email' => 'verification-customer@logout-browser.test',
        'password' => 'Browser-test-password-123',
        'verification_code' => '123456',
        'verification_code_expires_at' => now()->addMinutes(10),
    ]);
}

if (getenv('LAVEA_BROWSER_FULFILLMENT') === '1') {
    StaffFactory::new()->create(['name' => 'Browser Delivery Rider', 'role' => 'Delivery Rider']);
}

$browserCustomer = Customer::where('email', 'customer@logout-browser.test')->firstOrFail();
$browserStaff = Staff::where('email', 'manager@logout-browser.test')->firstOrFail();
$browserService = ServiceFactory::new()->create(['service_name' => 'Laundry Service With A Longer Name', 'required_job_position' => 'Laundry Attendant', 'description' => str_repeat('Careful laundry cleaning. ', 8)]);
StaffFactory::new()->create(['name' => 'Browser Idle Laundry Worker', 'role' => 'Laundry Attendant']);
StaffFactory::new()->create(['name' => 'Browser Dry Worker', 'role' => 'Dry Cleaning Attendant']);
StaffFactory::new()->create(['name' => 'Browser Ironing Worker', 'role' => 'Ironing Attendant']);
ServiceFactory::new()->create(['service_name' => 'Dry Cleaning', 'required_job_position' => 'Dry Cleaning Attendant']);
ServiceFactory::new()->create(['service_name' => 'Ironing', 'required_job_position' => 'Ironing Attendant']);
ServiceFactory::new()->create(['service_name' => 'Unmapped Service', 'required_job_position' => null]);
$browserOrder = OrderFactory::new()->for($browserCustomer)->for($browserService)->create(['staff_id' => $browserStaff->id, 'pickup_date' => today()->addDays(3), 'total' => 300]);
PaymentFactory::new()->for($browserOrder)->create(['status' => 'Confirmed', 'amount' => 100, 'processed_by' => User::where('email', 'manager@logout-browser.test')->firstOrFail()->id]);
$browserProofPath = 'payment-proofs/browser-'.bin2hex(random_bytes(16)).'.jpg';
Storage::disk('local')->put($browserProofPath, file_get_contents(public_path('images/laundry-home.jpg')));
PaymentFactory::new()->for($browserOrder)->create(['status' => 'Pending', 'amount' => 25, 'payment_method' => 'GCash', 'reference_number' => 'BROWSER-TRANSFER', 'proof_image_path' => $browserProofPath]);
PaymentFactory::new()->for($browserOrder)->create(['status' => 'Rejected', 'amount' => 25, 'rejection_reason' => 'Browser rejected reason', 'proof_image_path' => $browserProofPath]);
PaymentFactory::new()->for($browserOrder)->create(['status' => 'Pending', 'amount' => 10, 'reference_number' => 'BROWSER-CASH']);
ScheduleFactory::new()->for($browserOrder)->create();
