<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $connection = 'db_hrm';
    protected $table = 'TBL_SOE_HEAD';
}
