<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    protected $fillable = [
        'description'
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords(str_replace('_', ' ', $value)),
        );
    }

    public function permissions()
    {
        return $this->hasMany(RoleHasPermission::class);
    }
}
