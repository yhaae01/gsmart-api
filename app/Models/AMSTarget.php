<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AMSTarget extends Model
{
    use HasFactory;

    protected $table = 'ams_targets';
    protected $guarded = ['id'];

    public function ams()
    {
        return $this->belongsTo(AMS::class, 'ams_id');
    }
}
