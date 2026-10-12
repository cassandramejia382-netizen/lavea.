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
        if (! Schema::hasColumn('orders', 'quantity')) {
            throw new RuntimeException('The orders table must have a quantity column before applying decimal weights.');
        }

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('quantity', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('Decimal weights cannot safely be converted back to whole kilograms.');
    }
};
