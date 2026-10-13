<?php

namespace App;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class DeliveryStaffAssignment
{
    public function assign(?int $currentRiderId = null): int
    {
        Staff::where('status', 'Active')->where('role', 'Delivery Rider')->lockForUpdate()->get();
        $riders = Staff::where('status', 'Active')->where('role', 'Delivery Rider')
            ->withCount(['deliveryOrders as unfinished_deliveries_count' => fn (Builder $query) => $query
                ->where('fulfillment_method', 'Delivery')->whereNotIn('status', ['Completed', 'Cancelled'])])
            ->orderBy('unfinished_deliveries_count')->orderBy('id')->get();
        $rider = $riders->firstWhere('id', $currentRiderId) ?? $riders->first();
        if ($rider === null) {
            throw ValidationException::withMessages(['fulfillment_method' => 'No active Delivery Rider is available. Choose Pickup or contact the shop.']);
        }

        return $rider->id;
    }
}
