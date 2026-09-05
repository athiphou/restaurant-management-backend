<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries';

    protected $fillable = [
        'order_id',
        'delivery_driver_id',
        'customer_address_id',
        'status',
        'delivery_fee',
        'notes',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function deliveryDriver(): BelongsTo
    {
        return $this->belongsTo(DeliveryDriver::class, 'delivery_driver_id');
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }
}
