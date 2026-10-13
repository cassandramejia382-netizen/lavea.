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
        Schema::table('services', function (Blueprint $table): void {
            $table->string('required_job_position', 100)->nullable();
        });
        foreach (DB::table('services')->get(['id', 'service_name']) as $service) {
            $name = strtolower(preg_replace('/[^a-zA-Z]/', '', $service->service_name));
            $position = match ($name) {
                'washfold', 'washandfold', 'bedsheetcleaning', 'bedsheetscleaning', 'comfortercleaning', 'comforterscleaning', 'curtaincleaning', 'curtainscleaning', 'expresslaundry', 'stainremoval' => 'Laundry Attendant',
                'drycleaning' => 'Dry Cleaning Attendant',
                'ironing' => 'Ironing Attendant',
                default => null,
            };
            if ($position !== null) {
                DB::table('services')->where('id', $service->id)->update(['required_job_position' => $position]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('required_job_position');
        });
    }
};
