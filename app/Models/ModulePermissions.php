<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModulePermissions extends Model
{
    use HasFactory;
    protected $table = 'module_permissions';
    protected $fillable = [
        'module_id',
        'permission_id',
    ];
}
