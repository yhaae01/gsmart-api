<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AM extends Model
{
    use HasFactory;

    protected $table = 'am';

    protected $fillable = [
        'initial',
        'user_id',
        'area_id',
    ];

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->where('initial', 'LIKE', "%{$search}%")
                ->orWhereRelation('user', 'name', 'LIKE', "%{$search}%");
        });
    }

    public function scopeSort($query, $order, $by)
    {
        $query->when(($order && $by), function ($query) use ($order, $by) {
            if ($order  == 'user') {
                $query->withAggregate('user', 'name')
                    ->orderBy('user_name', $by);
            } elseif ($order  == 'area') {
                $query->withAggregate('area', 'name')
                    ->orderBy('name', $by);
            } else {
                $query->orderBy('initial', $by);
            }
        }) ;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function ams()
    {
        return $this->hasMany(AMS::class, 'am_id');
    }
}
