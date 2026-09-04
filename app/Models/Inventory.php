<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'ingredient_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id');
    }
}
