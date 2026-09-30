<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_name',
        'price',
        'description',
        'image',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
