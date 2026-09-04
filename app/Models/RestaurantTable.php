<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;
    protected $fillable =[
        'branch_id',
        'table_number',
        'capacity',
        'status',
    ];

    protected $casts =[
        'capacity' => 'integer',
    ];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }
}

