<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'Pending',
                'Processing',
                'Completed',
                'Cancelled',
                'Ready',
                'Claimed',
            ])->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->where('status', 'Completed')->update(['status' => 'Claimed']);
        DB::table('orders')->where('status', 'Cancelled')->update(['status' => 'Pending']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'Pending',
                'Processing',
                'Ready',
                'Claimed',
            ])->default('Pending')->change();
        });
    }
};
