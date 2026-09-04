<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'base_salary',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }
}