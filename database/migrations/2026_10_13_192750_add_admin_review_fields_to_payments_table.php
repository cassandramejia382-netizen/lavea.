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
        Schema::table('payments', function (Blueprint $table): void {
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('submission_key', 64)->nullable()->unique();
            $table->string('status')->default('Pending')->change();
        });
        DB::table('payments')->where('status', 'Completed')->update(['status' => 'Confirmed']);
        DB::table('payments')->where('status', 'Failed')->update(['status' => 'Rejected']);
        DB::table('orders')->select('id', 'total')->orderBy('id')->chunkById(200, function (Collection $orders): void {
            $amounts = DB::table('payments')->where('status', 'Confirmed')
                ->whereIn('order_id', $orders->pluck('id'))->selectRaw('order_id, SUM(amount) AS paid_amount')
                ->groupBy('order_id')->pluck('paid_amount', 'order_id');
            foreach ($orders as $order) {
                $paidCents = (int) round(($amounts[$order->id] ?? 0) * 100);
                $totalCents = (int) round($order->total * 100);
                DB::table('orders')->where('id', $order->id)->update([
                    'payment_status' => $paidCents <= 0 ? 'Unpaid' : ($paidCents >= $totalCents ? 'Paid' : 'Partial'),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payments')->where('status', 'Confirmed')->update(['status' => 'Completed']);
        DB::table('payments')->where('status', 'Rejected')->update(['status' => 'Failed']);
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropUnique(['submission_key']);
            $table->dropColumn(['rejection_reason', 'reviewed_at', 'submission_key']);
            $table->string('status')->default('Completed')->change();
        });
    }
};
