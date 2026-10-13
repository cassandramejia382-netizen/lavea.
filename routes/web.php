<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('home'))->name('home');
/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.submit');
    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('register.submit');
    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');
    Route::post('/forgot-password/code', [AuthController::class, 'sendResetCode'])->middleware('throttle:6,1')->name('password.code.send');
    Route::get('/forgot-password/code', [AuthController::class, 'showResetCode'])->name('password.code');
    Route::get('/forgot-password/new-password', [AuthController::class, 'showNewPassword'])->name('password.code.new');
    Route::post('/forgot-password/code/verify', [AuthController::class, 'verifyResetCode'])->middleware('throttle:6,1')->name('password.code.verify');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:6,1')
        ->name('password.email');
    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:6,1')
        ->name('password.update');
});
/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth', 'no-cache'])
    ->name('logout');
Route::get('/logout/confirm', fn () => view('auth.confirm-logout'))
    ->middleware(['auth', 'no-cache'])
    ->name('logout.confirm');
/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/
Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead'])
    ->middleware(['auth', 'no-cache'])
    ->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
    ->middleware(['auth', 'no-cache'])
    ->name('notifications.read-all');
Route::get('/payments/{payment}/proof', [PaymentController::class, 'proof'])
    ->middleware(['auth', 'no-cache'])->name('payments.proof');

Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])
    ->whereNumber('payment')->middleware(['auth', 'no-cache'])->name('payments.receipt');

Route::get('/staff/{path?}', function (?string $path = null): RedirectResponse {
    $destination = url('manager/'.($path ?? 'dashboard'));
    $query = request()->getQueryString();

    return redirect()->to($destination.($query ? '?'.$query : ''));
})->where('path', '.*')->middleware(['auth', 'no-cache', 'role:manager']);
/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'no-cache', 'role:customer'])->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    |
    | LAVEA uses a 6-digit verification code instead of
    | Laravel's default email verification link.
    |
    */
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])
        ->name('verification.notice');
    Route::post('/email/verify', [AuthController::class, 'verifyCode'])
        ->middleware('throttle:6,1')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    /*
        |--------------------------------------------------------------------------
        | Customer Dashboard
        |--------------------------------------------------------------------------
        */
    Route::middleware(['verified', 'no-cache'])
        ->prefix('customer')
        ->name('customer.')
        ->group(function (): void {
            Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
                ->name('dashboard');
            /*
            |--------------------------------------------------------------------------
            | Orders
            |--------------------------------------------------------------------------
            */
            Route::get('/orders', [CustomerDashboardController::class, 'orders'])
                ->name('orders.index');
            Route::get('/orders/{order}', [CustomerDashboardController::class, 'showOrder'])
                ->whereNumber('order')
                ->name('orders.show');
            Route::get('/orders/{order}/edit', [CustomerDashboardController::class, 'editOrder'])->whereNumber('order')->name('orders.edit');
            Route::put('/orders/{order}', [CustomerDashboardController::class, 'updateOrder'])->whereNumber('order')->name('orders.update');
            Route::patch('/orders/{order}/cancel', [CustomerDashboardController::class, 'cancelOrder'])->whereNumber('order')->name('orders.cancel');
            /*
            |--------------------------------------------------------------------------
            | Service Ordering
            |--------------------------------------------------------------------------
            */
            Route::get('/services', [CustomerDashboardController::class, 'services'])
                ->name('services.index');
            Route::get('/services/{service}', [CustomerDashboardController::class, 'showService'])
                ->name('services.show');
            Route::post('/services/{service}/order', [CustomerDashboardController::class, 'storeServiceOrder'])
                ->name('services.order');
            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */
            Route::get('/payments/orders/{order}', [CustomerDashboardController::class, 'createOrderPayment'])
                ->whereNumber('order')
                ->name('payments.checkout');
            Route::post('/payments/orders/{order}', [CustomerDashboardController::class, 'storeOrderPayment'])
                ->whereNumber('order')
                ->name('payments.checkout.store');
            Route::get('/payments', [CustomerDashboardController::class, 'payments'])
                ->name('payments.index');
            Route::get('/payments/{payment}', [CustomerDashboardController::class, 'showPayment'])
                ->whereNumber('payment')
                ->name('payments.show');
            /*
            |--------------------------------------------------------------------------
            | Customer Profile
            |--------------------------------------------------------------------------
            */
            Route::get('/profile', [CustomerDashboardController::class, 'profile'])
                ->name('profile');
            Route::patch('/profile', [CustomerDashboardController::class, 'updateProfile'])
                ->name('profile.update');
            Route::delete('/profile', [CustomerDashboardController::class, 'deleteAccount'])
                ->middleware('throttle:6,1')->name('profile.destroy');
            /*
            |--------------------------------------------------------------------------
            | Customer Settings
            |--------------------------------------------------------------------------
            */
            Route::get('/settings', [SettingsController::class, 'index'])
                ->name('settings');
            Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])
                ->name('settings.profile');
            Route::post('/settings/password', [SettingsController::class, 'updatePassword'])
                ->name('settings.password');
            Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])
                ->name('settings.appearance');
            Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])
                ->name('settings.notifications');
        });
    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    | These redirect old /user URLs to the customer dashboard.
    |
    */
    Route::middleware(['verified', 'no-cache'])
        ->prefix('user')
        ->name('user.')
        ->group(function (): void {
            Route::get('/dashboard', fn () => redirect()->route('customer.dashboard')
            )->name('dashboard');
            Route::get('/services', fn () => redirect()->route('customer.services.index')
            )->name('services');
            Route::get('/orders', fn () => redirect()->route('customer.orders.index')
            )->name('orders');
            Route::get('/payments', fn () => redirect()->route('customer.payments.index')
            )->name('payments');
            Route::get('/profile', fn () => redirect()->route('customer.profile')
            )->name('profile');
        });
});
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'no-cache', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/profile', [SettingsController::class, 'adminProfile'])
            ->name('profile');
        Route::post('/profile', [SettingsController::class, 'updateAdminAccountProfile'])
            ->name('profile.update');
        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */
        Route::resource('customers', CustomerController::class)
            ->only(['index', 'show'])
            ->names('customers');
        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */
        Route::resource('managers', ManagerController::class)->except(['show', 'destroy']);
        Route::resource('staff', StaffController::class)
            ->names('staff');
        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */
        Route::patch('services/{service}/availability', [ServiceController::class, 'updateAvailability'])->name('services.availability');
        Route::resource('services', ServiceController::class)
            ->names('services');
        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */
        Route::resource('orders', OrderController::class)
            ->only(['index', 'show'])
            ->names('orders');
        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */
        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show'])
            ->names('payments');
        Route::patch('/payments/{payment}/review', [PaymentController::class, 'reviewCustomerPayment'])
            ->name('payments.review');
        /*
        |--------------------------------------------------------------------------
        | Schedules
        |--------------------------------------------------------------------------
        */
        Route::resource('schedules', ScheduleController::class)
            ->except(['destroy'])
            ->names('schedules');
        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('settings');
        Route::post('/settings/shop-information', [SettingsController::class, 'updateShopInformation'])
            ->name('settings.shop');
        Route::post('/settings/gcash', [SettingsController::class, 'updateGCash'])
            ->name('settings.gcash');
        Route::post('/settings/admin-profile', [SettingsController::class, 'updateAdminProfile'])
            ->name('settings.profile');
        Route::post('/settings/password', [SettingsController::class, 'updatePassword'])
            ->name('settings.password');
        Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])
            ->name('settings.appearance');
        Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])
            ->name('settings.notifications');
    });
/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'no-cache', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function (): void {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])
            ->name('dashboard');
        /*
        |--------------------------------------------------------------------------
        | Manager Profile
        |--------------------------------------------------------------------------
        */
        Route::get('/profile', [SettingsController::class, 'managerProfile'])
            ->name('profile');
        Route::post('/profile', [SettingsController::class, 'updateManagerProfile'])
            ->name('profile.update');
        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */
        Route::resource('customers', CustomerController::class)
            ->names('customers');
        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */
        Route::get('/services', [ServiceController::class, 'managerIndex'])
            ->name('services.index');
        Route::get('/services/{service}', [ServiceController::class, 'managerShow'])
            ->name('services.show');
        Route::get('/services/{service}/staff-options', [ServiceController::class, 'staffOptions'])->name('services.staff-options');
        Route::patch('/services/{service}/assignment-position', [ServiceController::class, 'updateAssignmentPosition'])->name('services.assignment-position');
        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */
        Route::resource('orders', OrderController::class)
            ->names('orders');
        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */
        Route::resource('payments', PaymentController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->names('payments');
        /*
        |--------------------------------------------------------------------------
        | Schedules
        |--------------------------------------------------------------------------
        */
        Route::resource('staff', StaffController::class)->names('staff');
        Route::resource('schedules', ScheduleController::class)->names('schedules');
        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('settings');
        Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])
            ->name('settings.profile');
        Route::post('/settings/password', [SettingsController::class, 'updatePassword'])
            ->name('settings.password');
        Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])
            ->name('settings.appearance');
        Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])
            ->name('settings.notifications');
    });
