<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hangar extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function salesReschedules()
    {
        return $this->hasMany(SalesReschedule::class);
    }

    public function lines()
    {
        return $this->hasMany(Line::class);
    }
}
