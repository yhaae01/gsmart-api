<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSwift extends Model
{
    use HasFactory;

    protected $table = 'customer_swift';
    protected $fillable = ['code', 'name', 'customer_group_id'];

    protected $appends = [
        'label',
    ];

    public function getLabelAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search, function($query) use ($search) {
            $query->where('code', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhereRelation('customer_group', 'code', 'LIKE', "%%$search")
                ->orWhereRelation('customer_group', 'name', 'LIKE', "%%$search");
        });
    }

    public function customerGroup()
    {
        return $this->belongsTo(Customer::class, 'customer_group_id');
    }
}
