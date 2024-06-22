<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    use HasFactory;

    protected $table = 'countries';
    protected $fillable = [
        'name',
        'region_id',
    ];

    protected $appends = [
        'label'
    ];

    public function getLabelAttribute()
    {
        return "{$this->name} - {$this->region->name}";
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhereRelation('region', 'name', 'LIKE', "%{$search}%");
        });
    }

    public function scopeSort($query, $order, $by)
    {
        $query->when(($order && $by), function ($query) use ($order, $by) {
            if ($order  == 'region') {
                $query->withAggregate('region', 'name')
                    ->orderBy('region_name', $by);
            } else {
                $query->orderBy('name', $by);
            }
        }) ;
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
