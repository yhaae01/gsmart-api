<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;
    protected $table = 'modules';
    protected $fillable = [
        'module_name',
    ];

    public function module()
    {
        return $this->belongsToMany(Permission::class, 'module_permissions', 'module_id', 'permission_id');
    }
}
