<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'maintenances';
    protected $fillable = [
        'name',
        'product_id',
        'description',
    ];

    protected $appends = [
        'product_name',
    ];

    public function getProductNameAttribute()
    {
        return $this->product->name;
    }

    public function sales()
    {
        return $this->hasMany(SalesMaintenance::class);
    }

    public function tmb()
    {
        return $this->hasMany(TMB::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
