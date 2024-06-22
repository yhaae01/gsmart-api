<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportDashboard extends Model
{
    protected $table = 'v_export_excel';
    protected $hidden = ['Sales_ID'];
}
