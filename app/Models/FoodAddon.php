<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodAddon extends Model
{
    use HasFactory;

    protected $table = 'food_addons';

    protected $fillable = [
        'food_id',
        'name',
        'is_required',
        'max_items',
        'is_available',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'max_items' => 'integer',
        'is_available' => 'boolean',
    ];

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class, 'food_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FoodAddonItem::class, 'addon_id');
    }
}