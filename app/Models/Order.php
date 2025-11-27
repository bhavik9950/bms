<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_date',
        'advance_paid',
        'total_amount',
        'pending_amount',
        'remarks',
    ];

    protected $casts = [
        'order_date' => 'date',
        'advance_paid' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItems::class);
    }
}
