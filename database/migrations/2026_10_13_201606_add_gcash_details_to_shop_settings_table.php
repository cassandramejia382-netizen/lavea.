<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shop_settings', function (Blueprint $table): void {
            $table->string('gcash_number', 20)->nullable();
            $table->string('gcash_account_name')->nullable();
            $table->string('gcash_qr_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_settings', function (Blueprint $table): void {
            $table->dropColumn(['gcash_number', 'gcash_account_name', 'gcash_qr_path']);
        });
    }
};
