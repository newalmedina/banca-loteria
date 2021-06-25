<?php


use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionsTree;

class NotificationsBrokerGroupSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();



        // Módulo de Notificaciones
        $moduleName = "Log de peticiones";
        $moduleSlug = "notifications-broker-group";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $this->childAdmin, $this->a_permission_admin);


        // Rol de administrador
        $roleAdmin = Role::where("name", "=", str_slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", str_slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
