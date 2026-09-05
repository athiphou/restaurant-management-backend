<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Restaurant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(RestaurantBranch::class, 'restaurant_id');
    }

    public function tables(): HasManyThrough
    {
        return $this->hasManyThrough(RestaurantTable::class, RestaurantBranch::class, 'restaurant_id', 'branch_id');
    }
}