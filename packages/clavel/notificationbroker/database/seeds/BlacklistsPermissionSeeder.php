<?php


use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionsTree;

class BlacklistsPermissionSeeder extends BaseSeeder
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
        $permExists = Permission::where('name', str_slug('admin-blacklists'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de blacklists
        $permissions = [
            [
                'display_name' => 'Blacklists',
                'name' => str_slug('admin-blacklists'),
                'description' => 'Blacklists - Módulo'
            ],
            [
                'display_name' => 'Blacklists - listado',
                'name' => str_slug('admin-blacklists-list'),
                'description' => 'Blacklists - lista'
            ],
            [
                'display_name' => 'Blacklists - crear',
                'name' => str_slug('admin-blacklists-create'),
                'description' => 'Blacklists - crear'
            ],
            [
                'display_name' => 'Blacklists - actualizar',
                'name' => str_slug('admin-blacklists-update'),
                'description' => 'Blacklists - actualizar'
            ],
            [
                'display_name' => 'Blacklists - borrar',
                'name' => str_slug('admin-blacklists-delete'),
                'description' => 'Blacklists - borrar'
            ],
            [
                'display_name' => 'Blacklists - ver',
                'name' => str_slug('admin-blacklists-read'),
                'description' => 'Blacklists - ver'
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
