<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
    ];

    public function tmb()
    {
        return $this->hasMany(TMB::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function pbth()
    {
        return $this->hasMany(PBTH::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
