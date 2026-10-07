<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffDashboardController;
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
    ->middleware('auth')
    ->name('logout');

Route::get('/logout/confirm', fn () => view('auth.confirm-logout'))
    ->middleware('auth')
    ->name('logout.confirm');


/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/

Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead'])
    ->middleware('auth')
    ->name('notifications.read');


/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:customer'])->group(function (): void {

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

    Route::middleware('verified')
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

    Route::middleware('verified')
        ->prefix('user')
        ->name('user.')
        ->group(function (): void {

            Route::get('/dashboard', fn () =>
                redirect()->route('customer.dashboard')
            )->name('dashboard');

            Route::get('/services', fn () =>
                redirect()->route('customer.services.index')
            )->name('services');

            Route::get('/orders', fn () =>
                redirect()->route('customer.orders.index')
            )->name('orders');

            Route::get('/payments', fn () =>
                redirect()->route('customer.payments.index')
            )->name('payments');

            Route::get('/profile', fn () =>
                redirect()->route('customer.profile')
            )->name('profile');
        });
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
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

        Route::post('/staff/{staff}/invitation', [StaffController::class, 'sendInvitation'])
            ->name('staff.invitation');

        Route::resource('staff', StaffController::class)
            ->names('staff');


        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

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

        Route::get('/reports', fn () =>
            view('admin.reports.index')
        )->name('reports');


        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('settings');

        Route::post('/settings/shop-information', [SettingsController::class, 'updateShopInformation'])
            ->name('settings.shop');

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
| Staff Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function (): void {

        Route::get('/dashboard', [StaffDashboardController::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Staff Profile
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [SettingsController::class, 'staffProfile'])
            ->name('profile');

        Route::post('/profile', [SettingsController::class, 'updateStaffProfile'])
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

        Route::get('/services', [ServiceController::class, 'staffIndex'])
            ->name('services.index');

        Route::get('/services/{service}', [ServiceController::class, 'staffShow'])
            ->name('services.show');


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
            ->only(['index', 'create', 'store', 'show'])
            ->names('payments');

        Route::patch('/payments/{payment}/review', [PaymentController::class, 'reviewCustomerPayment'])
            ->name('payments.review');


        /*
        |--------------------------------------------------------------------------
        | Schedules
        |--------------------------------------------------------------------------
        */

        Route::get('/schedules', [ScheduleController::class, 'staffIndex'])
            ->name('schedules.index');


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