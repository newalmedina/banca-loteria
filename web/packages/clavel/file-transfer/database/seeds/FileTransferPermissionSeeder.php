<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class FileTransferPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();



        // Agrupador de puntos de menu - File Transfer
        $adminStruct = Permission::where('name', str_slug('admin'))->first();
        $childAdminStruct = PermissionsTree::where('permissions_id', $adminStruct->id)->first();

        // Módulo de modulos crud
        $permissions = [
            [
                'display_name' => 'Módulos crud',
                'name' => str_slug('admin-file-transfer'),
                'description' => 'Módulos crud - Módulo'
            ],
            [
                'display_name' => 'Módulos crud - listado',
                'name' => str_slug('admin-file-transfer-list'),
                'description' => 'Módulos crud - lista'
            ],
            [
                'display_name' => 'Módulos crud - crear',
                'name' => str_slug('admin-file-transfer-create'),
                'description' => 'Módulos crud - crear'
            ],
            [
                'display_name' => 'Módulos crud - actualizar',
                'name' => str_slug('admin-file-transfer-update'),
                'description' => 'Módulos crud - actualizar'
            ],
            [
                'display_name' => 'Módulos crud - borrar',
                'name' => str_slug('admin-file-transfer-delete'),
                'description' => 'Módulos crud - borrar'
            ],
            [
                'display_name' => 'Módulos crud - ver',
                'name' => str_slug('admin-file-transfer-read'),
                'description' => 'Módulos crud - ver'
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
