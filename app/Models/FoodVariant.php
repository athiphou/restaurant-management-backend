<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_id',
        'name',
        'price',
    ];

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'variant_id');
    }
}
