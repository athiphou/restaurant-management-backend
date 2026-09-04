<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodAddonItem extends Model
{
    use HasFactory;

    protected $table = 'food_addon_items';

    protected $fillable = [
        'addon_id',
        'name',
        'price',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function addon(): BelongsTo
    {
        return $this->belongsTo(FoodAddon::class, 'addon_id');
    }
}