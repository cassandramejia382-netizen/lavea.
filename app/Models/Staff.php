<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    public const LAUNDRY_JOB_POSITIONS = ['Laundry Attendant', 'Dry Cleaning Attendant', 'Ironing Attendant'];

    public const JOB_POSITIONS = [...self::LAUNDRY_JOB_POSITIONS, 'Delivery Rider'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'status',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'staff_id');
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_rider_id');
    }
}
