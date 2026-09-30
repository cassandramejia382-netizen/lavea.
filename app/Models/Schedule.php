<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'schedule_date',
        'schedule_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
