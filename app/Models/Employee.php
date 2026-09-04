<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'position_id',
        'hire_date',
        'salary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function position()
    {
        return $this->belongsTo(StaffPosition::class, 'position_id');
    }
}