<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('staff_id')
                ->nullable()
                ->after('service_id');

            $table->date('order_date')
                ->nullable()
                ->after('quantity');

            $table->date('pickup_date')
                ->nullable()
                ->after('order_date');

            $table->date('delivery_date')
                ->nullable()
                ->after('pickup_date');

            $table->string('payment_status')
                ->default('Unpaid')
                ->after('status');

            $table->decimal('total', 10, 2)
                ->default(0)
                ->after('payment_status');

            $table->text('notes')
                ->nullable()
                ->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn([
                'staff_id',
                'order_date',
                'pickup_date',
                'delivery_date',
                'payment_status',
                'total',
                'notes',
            ]);
        });
    }
};
