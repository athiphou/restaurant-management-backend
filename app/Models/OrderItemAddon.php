<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemAddon extends Model
{
    use HasFactory;

    protected $table = 'order_item_addons';

    protected $fillable = [
        'order_item_id',
        'addon_item_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function addonItem(): BelongsTo
    {
        return $this->belongsTo(FoodAddonItem::class, 'addon_item_id');
    }
}
