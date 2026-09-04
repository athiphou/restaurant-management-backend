<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'inventory_id',
        'supplier_id',
        'transaction_type',
        'quantity',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
