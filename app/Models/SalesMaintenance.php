<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesMaintenance extends Model
{
    use HasFactory;

    protected $table = 'sales_maintenances';

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }
}
