<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesLevel extends Model
{
    use HasFactory;
    protected $fillable = [
        'sales_id',
        'level_id',
        'status',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    
}
