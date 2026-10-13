<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const STATUSES = ['Pending', 'Processing', 'Completed', 'Cancelled'];

    protected $casts = [
        'order_date' => 'date',
        'pickup_date' => 'date',
        'delivery_date' => 'date',
    ];

    protected $fillable = [
        'customer_id',
        'service_id',
        'staff_id',
        'delivery_rider_id',
        'quantity',
        'order_date',
        'pickup_date',
        'delivery_date',
        'fulfillment_method',
        'delivery_address',
        'status',
        'payment_status',
        'total',
        'notes',
    ];

    public function fulfillmentMethod(): string
    {
        return $this->fulfillment_method ?? ($this->delivery_date && ! $this->pickup_date ? 'Delivery' : 'Pickup');
    }

    public function canCustomerModify(): bool
    {
        return $this->status === 'Pending'
            && $this->payment_status === 'Unpaid'
            && ! $this->payments->contains(fn (Payment $payment): bool => in_array($payment->status, ['Pending', 'Confirmed'], true));
    }

    public function refreshPaymentStatus(): void
    {
        $paidCents = (int) round($this->payments()->where('status', 'Confirmed')->sum('amount') * 100);
        $totalCents = (int) round($this->total * 100);
        $this->update(['payment_status' => $paidCents <= 0 ? 'Unpaid' : ($paidCents >= $totalCents ? 'Paid' : 'Partial')]);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function deliveryRider(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'delivery_rider_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
