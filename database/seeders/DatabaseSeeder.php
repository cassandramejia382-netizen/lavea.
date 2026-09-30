<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = config('app.initial_admin.email');
        $password = config('app.initial_admin.password');

        if (! is_string($email) || $email === '' || ! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_EMAIL and an ADMIN_PASSWORD of at least 12 characters before seeding the admin account.');
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => config('app.initial_admin.name'),
                'password' => $password,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        if ($admin->role !== 'admin') {
            throw new RuntimeException('ADMIN_EMAIL already belongs to a non-admin account. Choose a separate email for the initial admin.');
        }
    }
}
