<?php


use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionsTree;

class BounceTypesPermissionSeeder extends BaseSeeder
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
        $adminStruct = Permission::where('name', str_slug('admin'))->first();
        $childAdminStruct = PermissionsTree::where('permissions_id', $adminStruct->id)->first();


        // Si los permisos los hemos creados volvemos
        $permExists = Permission::where('name', str_slug('admin-bouncetypes'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de bouncetypes
        $permissions = [
            [
                'display_name' => 'Tipos de rebotes',
                'name' => str_slug('admin-bouncetypes'),
                'description' => 'Tipos de rebotes - Módulo'
            ],
            [
                'display_name' => 'Tipos de rebotes - listado',
                'name' => str_slug('admin-bouncetypes-list'),
                'description' => 'Tipos de rebotes - lista'
            ],
            [
                'display_name' => 'Tipos de rebotes - crear',
                'name' => str_slug('admin-bouncetypes-create'),
                'description' => 'Tipos de rebotes - crear'
            ],
            [
                'display_name' => 'Tipos de rebotes - actualizar',
                'name' => str_slug('admin-bouncetypes-update'),
                'description' => 'Tipos de rebotes - actualizar'
            ],
            [
                'display_name' => 'Tipos de rebotes - borrar',
                'name' => str_slug('admin-bouncetypes-delete'),
                'description' => 'Tipos de rebotes - borrar'
            ],
            [
                'display_name' => 'Tipos de rebotes - ver',
                'name' => str_slug('admin-bouncetypes-read'),
                'description' => 'Tipos de rebotes - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $this->childAdmin, $this->a_permission_admin);


        // Rol de administrador
        $roleAdmin = Role::where("name", "=", str_slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", str_slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
