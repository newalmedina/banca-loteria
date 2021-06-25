<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class NotificationsTemplatesSeeder extends BaseSeeder
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
        $moduleName = "Gestión de plantillas del Broker";
        $moduleSlug = "plantillas";
        $permissions = [
            [
                'display_name' => $moduleName.'',
                'name' => str_slug('admin-'.$moduleSlug),
                'description' => $moduleName.' - Módulo'
            ],
            [
                'display_name' => $moduleName.' - listado',
                'name' => str_slug('admin-'.$moduleSlug.'-list'),
                'description' => $moduleName.' - lista'
            ],
            [
                'display_name' => $moduleName.' - crear',
                'name' => str_slug('admin-'.$moduleSlug.'-create'),
                'description' => $moduleName.' - crear'
            ],
            [
                'display_name' => $moduleName.' - actualizar',
                'name' => str_slug('admin-'.$moduleSlug.'-update'),
                'description' => $moduleName.' - actualizar'
            ],
            [
                'display_name' => $moduleName.' - borrar',
                'name' => str_slug('admin-'.$moduleSlug.'-delete'),
                'description' => $moduleName.' - borrar'
            ],
            [
                'display_name' => $moduleName.' - ver',
                'name' => str_slug('admin-'.$moduleSlug.'-read'),
                'description' => $moduleName.' - ver'
            ],
            [
                'display_name' => $moduleName.' - generar',
                'name' => str_slug('admin-'.$moduleSlug.'-generar'),
                'description' => $moduleName.' - generar'
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
