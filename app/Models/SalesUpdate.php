<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'detail',
        'reason',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
