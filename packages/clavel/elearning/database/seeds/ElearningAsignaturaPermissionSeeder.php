<?php


use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionsTree;

class ElearningAsignaturaPermissionSeeder extends BaseSeeder
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
        $adminStruct = Permission::where('name', str_slug('admin-asignaturas'))->first();
        $childAdminStruct = PermissionsTree::where('permissions_id', $adminStruct->id)->first();


        // Módulo de Alumnos
        $permissions = [
            [
                'display_name' => 'Asignaturas - ver todas las asignaturas',
                'name' => str_slug('admin-asignaturas-all'),
                'description' => 'Asignaturas -  ver todos las asignaturas'
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
