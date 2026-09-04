<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantBranch extends Model
{
    use HasFactory;
    protected $fillable=[
        'restaurant_id',
        'name',
        'phone',
        'address',
    ];
    public function restaurant(): BelongTo{
        return $this->belongTo(Restaurant::class);
    }
    public function tables(): HasMany{
        return $this->hasMany(RestaurantTable::class, 'branch_id');
    }
}
