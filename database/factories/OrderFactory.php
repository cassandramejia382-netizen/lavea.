<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => CustomerFactory::new(),
            'service_id' => ServiceFactory::new(),
            'quantity' => 2,
            'order_date' => '2026-10-01',
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'total' => 150,
        ];
    }
}
