<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('users')->where('role', 'staff')->orderBy('id')->chunkById(100, function (Collection $users): void {
                foreach ($users as $user) {
                    $worker = DB::table('staff')->where('email', $user->email)->first();
                    DB::table('users')->where('id', $user->id)->update([
                        'role' => 'manager',
                        'phone' => $user->phone ?? $worker?->phone,
                    ]);
                }
            });
            DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
        });
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('customer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('Login role migration is forward-only; restore a backup to recover the original role assignments.');
    }
};
