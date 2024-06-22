<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    
    protected $fillable = [
        'name',
        'scope',
    ];

    public function amsCustomer()
    {
        return $this->hasMany(AMSCustomer::class);
    }

    
    public function am()
    {
        return $this->hasOne(AM::class);
    }
}
