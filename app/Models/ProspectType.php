<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectType extends Model
{
    use HasFactory;

    protected $table = 'prospect_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }
}
