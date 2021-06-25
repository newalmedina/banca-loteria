<?php


use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionsTree;

class LoginAttemptPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();

        // Agrupador de puntos de menu
        // Para crear cuando ya existen
        $adminStruct = Permission::where('name', 'admin-users-gral')->first();
        $childAdminStruct = PermissionsTree::where("permissions_id", $adminStruct->id)->first();


        // Módulo de Configuracion
        $moduleName = "Control de acceso";
        $moduleSlug = "control-acceso";
        $permissions = [
            [
                'display_name' => $moduleName.'',
                'name' => Str::slug('admin-'.$moduleSlug),
                'description' => $moduleName.' - Módulo'
            ],
            [
                'display_name' => $moduleName.' - listado',
                'name' => Str::slug('admin-'.$moduleSlug.'-list'),
                'description' => $moduleName.' - lista'
            ],
            [
                'display_name' => $moduleName.' - crear',
                'name' => Str::slug('admin-'.$moduleSlug.'-create'),
                'description' => $moduleName.' - crear'
            ],
            [
                'display_name' => $moduleName.' - actualizar',
                'name' => Str::slug('admin-'.$moduleSlug.'-update'),
                'description' => $moduleName.' - actualizar'
            ],
            [
                'display_name' => $moduleName.' - borrar',
                'name' => Str::slug('admin-'.$moduleSlug.'-delete'),
                'description' => $moduleName.' - borrar'
            ],
            [
                'display_name' => $moduleName.' - ver',
                'name' => Str::slug('admin-'.$moduleSlug.'-read'),
                'description' => $moduleName.' - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name","=", Str::slug('admin'))->first();
        if(!empty($this->a_permission_admin)) {
            $roleAdmin->attachPermissions($this->a_permission_admin);
        }
        $roleUser = Role::where("name","=", Str::slug('usuario-front'))->first();
        if(!empty($this->a_permission_front)) {
            $roleUser->attachPermissions($this->a_permission_front);
        }

    }
}
