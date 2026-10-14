<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'quantity' => (float) $this->quantity,
            'total' => (float) $this->total,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'order_date' => $this->order_date?->toDateString(),
            'pickup_date' => $this->pickup_date?->toDateString(),
            'delivery_date' => $this->delivery_date?->toDateString(),
            'fulfillment_method' => $this->fulfillmentMethod(),
        ];
    }
}
