<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AMSCustomer extends Model
{
    use HasFactory;
    protected $table = 'ams_customers';
    protected $fillable = [
        'customer_id',
        'area_id',
        'ams_id',
    ];

    protected $appends = [
        'label',
    ];

    protected $hidden = [
        'ams',
    ];

    public function getLabelAttribute()
    {
        $area = $this->area->name;
        $ams = $this->ams->initial;

        return "{$area} - {$ams}";
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function ams()
    {
        return $this->belongsTo(AMS::class, 'ams_id');
    }

    public function prospects()
    {
        return $this->hasMany(Prospect::class, 'ams_customer_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
