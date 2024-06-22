<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pbth extends Model
{
    use HasFactory;

    protected $table = 'pbth';
    protected $guarded = ['id'];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class, 'prospect_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function acType()
    {
        return $this->belongsTo(AircraftType::class, 'ac_type_id');
    }
}
