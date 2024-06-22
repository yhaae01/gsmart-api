<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XpreamPlanningGates extends Model
{
    use HasFactory;

    protected $connection = 'db_xpream';
    protected $table = 'XPREAM_planning_gates';
}
