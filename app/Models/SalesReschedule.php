<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReschedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'start_date',
        'end_date',
        'tat',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class, 'hangar_id');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_id');
    }
}
