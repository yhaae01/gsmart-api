<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_requirement_id',
        'user_id',
        'status',
        'sequence',
    ];

    public function salesRequirement()
    {
        return $this->belongsTo(Sales::class, 'sales_requirement_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
