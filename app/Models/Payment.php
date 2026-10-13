<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUSES = ['Pending', 'Confirmed', 'Rejected'];

    protected $attributes = ['status' => 'Pending'];

    protected $casts = [
        'payment_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'status',
        'notes',
        'proof_image_path',
        'processed_by',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'submission_key',
    ];

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
