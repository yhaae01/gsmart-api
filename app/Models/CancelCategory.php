<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelCategory extends Model
{
    protected $table = 'cancel_categories';
    protected $fillable = ['name'];

    public function salesRejects()
    {
        return $this->hasMany(SalesReject::class);
    }
}
