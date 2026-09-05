<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'phone',
        'address',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class, 'branch_id');
    }
}