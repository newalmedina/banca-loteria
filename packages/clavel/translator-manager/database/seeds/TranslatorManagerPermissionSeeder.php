<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class TranslatorManagerPermissionSeeder extends BaseSeeder
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
        $adminStruct = Permission::where('name', str_slug('admin-struct'))->first();
        if (empty($adminStruct)) {
            // Si no esta admin estruct es porque no esta el paquete basic
            $adminStruct = Permission::where('name', str_slug('admin'))->first();
        }
        $childAdminStruct = PermissionsTree::where('permissions_id', $adminStruct->id)->first();

        // Módulo de Translator
        $permissions = [
            [
                'display_name' => 'Traducciones',
                'name' => str_slug('admin-translator'),
                'description' => 'Traducciones - Módulo'
            ],
            [
                'display_name' => 'Traducciones - listado',
                'name' => str_slug('admin-translator-list'),
                'description' => 'Traducciones - lista'
            ],
            [
                'display_name' => 'Traducciones - crear',
                'name' => str_slug('admin-translator-create'),
                'description' => 'Traducciones - crear'
            ],
            [
                'display_name' => 'Traducciones - actualizar',
                'name' => str_slug('admin-translator-update'),
                'description' => 'Traducciones - actualizar'
            ],
            [
                'display_name' => 'Traducciones - borrar',
                'name' => str_slug('admin-translator-delete'),
                'description' => 'Traducciones - borrar'
            ],
            [
                'display_name' => 'Traducciones - ver',
                'name' => str_slug('admin-translator-read'),
                'description' => 'Traducciones - ver'
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
