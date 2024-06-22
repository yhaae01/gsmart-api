<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'requirement_id',
        'detail',
    ];
    
    public function latestFile()
    {
        return $this->hasOne(File::class)->latest('updated_at');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class, 'requirement_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
