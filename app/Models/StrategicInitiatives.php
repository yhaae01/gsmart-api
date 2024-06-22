<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategicInitiatives extends Model
{
    use HasFactory;

    protected $table = 'strategic_initiatives';

    protected $fillable = [
        'name',
        'description',
    ];

    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }
}
