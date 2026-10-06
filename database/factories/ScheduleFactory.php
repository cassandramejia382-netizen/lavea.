<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => OrderFactory::new(),
            'type' => 'Pickup',
            'schedule_date' => '2026-10-07',
            'schedule_time' => '09:00:00',
            'status' => 'Scheduled',
            'notes' => null,
        ];
    }
}
