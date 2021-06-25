<?php


use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionsTree;

class BouncedEmailsPermissionSeeder extends BaseSeeder
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
        $permExists = Permission::where('name', str_slug('admin-bouncedemails'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de bouncedemails
        $permissions = [
            [
                'display_name' => 'Emails rebotados',
                'name' => str_slug('admin-bouncedemails'),
                'description' => 'Emails rebotados - Módulo'
            ],
            [
                'display_name' => 'Emails rebotados - listado',
                'name' => str_slug('admin-bouncedemails-list'),
                'description' => 'Emails rebotados - lista'
            ],
            [
                'display_name' => 'Emails rebotados - crear',
                'name' => str_slug('admin-bouncedemails-create'),
                'description' => 'Emails rebotados - crear'
            ],
            [
                'display_name' => 'Emails rebotados - actualizar',
                'name' => str_slug('admin-bouncedemails-update'),
                'description' => 'Emails rebotados - actualizar'
            ],
            [
                'display_name' => 'Emails rebotados - borrar',
                'name' => str_slug('admin-bouncedemails-delete'),
                'description' => 'Emails rebotados - borrar'
            ],
            [
                'display_name' => 'Emails rebotados - ver',
                'name' => str_slug('admin-bouncedemails-read'),
                'description' => 'Emails rebotados - ver'
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
