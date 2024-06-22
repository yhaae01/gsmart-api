<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learning extends Model
{
    use HasFactory;
    protected $table = 'learnings';
    protected $fillable = [
        'name',
    ];

    public function tmb()
    {
        return $this->hasMany(TMB::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }
}
