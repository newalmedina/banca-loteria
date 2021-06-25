<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class ElearningAlumnoPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();



        // Agrupador de puntos de menu - Estucctura de la web
        $adminStruct = Permission::where('name', str_slug('admin-elearning'))->first();
        $childAdminStruct = PermissionsTree::where('permissions_id', $adminStruct->id)->first();


        // Módulo de Alumnos
        $permissions = [
            [
                'display_name' => 'Alumnos',
                'name' => str_slug('admin-alumnos'),
                'description' => 'Alumnos - Módulo'
            ],
            [
                'display_name' => 'Alumnos - listado',
                'name' => str_slug('admin-alumnos-list'),
                'description' => 'Alumnos - lista'
            ],
            [
                'display_name' => 'Alumnos - crear',
                'name' => str_slug('admin-alumnos-create'),
                'description' => 'Alumnos - crear'
            ],
            [
                'display_name' => 'Alumnos - actualizar',
                'name' => str_slug('admin-alumnos-update'),
                'description' => 'Alumnos - actualizar'
            ],
            [
                'display_name' => 'Alumnos - borrar',
                'name' => str_slug('admin-alumnos-delete'),
                'description' => 'Alumnos - borrar'
            ],
            [
                'display_name' => 'Alumnos - ver',
                'name' => str_slug('admin-alumnos-read'),
                'description' => 'Alumnos - ver'
            ],
            [
                'display_name' => 'Alumnos - ver todos los alumnos',
                'name' => str_slug('admin-alumnos-all'),
                'description' => 'Alumnos -  ver todos los alumnos'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name", "=", str_slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", str_slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
