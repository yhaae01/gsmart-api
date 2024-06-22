<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'description',
    ];

    public function salesLevels()
    {
        return $this->hasMany(SalesLevel::class);
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }
}
