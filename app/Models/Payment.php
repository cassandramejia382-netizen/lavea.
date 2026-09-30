<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = [
        'payment_date' => 'date',
    ];

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'status',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
