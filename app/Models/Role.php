<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public function getArrayPermissions()
    {
        $permissionRole = $this->permissions()->get();

        $a_arrayPermisos = array();

        foreach ($permissionRole as $key => $value) {
            $a_arrayPermisos[$value->id] = $value->id;
        }

        return $a_arrayPermisos;
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
