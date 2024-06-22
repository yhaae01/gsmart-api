<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReject extends Model
{
    use HasFactory;
    protected $fillable = [
        'sales_id',
        'category_id',
        'reason',
        'competitor',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function category()
    {
        return $this->belongsTo(CancelCategory::class, 'category_id');
    }
}
