<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'requirement',
        'value',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function salesRequirements()
    {
        return $this->hasMany(SalesRequirement::class);
    }
}
