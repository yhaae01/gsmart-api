<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    use HasFactory;

    protected $table = 'lines';
    protected $guarded = ['id'];

    public function scopeByHangar($query, $hangar)
    {
        $query->when($hangar, function ($query) use ($hangar) {
            $query->where('hangar_id', $hangar);
        });
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class, 'hangar_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function salesReschedules()
    {
        return $this->hasMany(SalesReschedule::class);
    }
}
