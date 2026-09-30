<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $casts = [
        'order_date' => 'date',
        'pickup_date' => 'date',
        'delivery_date' => 'date',
    ];

    protected $fillable = [
        'customer_id',
        'service_id',
        'staff_id',
        'quantity',
        'order_date',
        'pickup_date',
        'delivery_date',
        'status',
        'payment_status',
        'total',
        'notes',
    ];

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

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
