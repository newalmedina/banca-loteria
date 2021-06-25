<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class CrudGeneratorPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();


        // Agrupador de puntos de menu - Crud Generator
        $adminStruct = new Permission();
        $adminStruct->display_name = 'Crud generator';
        $adminStruct->name = Str::slug('admin-crud-generator');
        $adminStruct->description = 'Crud generator - Módulo';
        $adminStruct->save();
        $childAdminStruct = $this->childAdmin->children()->create(['permissions_id' => $adminStruct->id]);
        $this->a_permission_admin[] = $adminStruct->id;

        // Módulo de modulos crud
        $permissions = [
            [
                'display_name' => 'Módulos crud',
                'name' => Str::slug('admin-modulos-crud'),
                'description' => 'Módulos crud - Módulo'
            ],
            [
                'display_name' => 'Módulos crud - listado',
                'name' => Str::slug('admin-modulos-crud-list'),
                'description' => 'Módulos crud - lista'
            ],
            [
                'display_name' => 'Módulos crud - crear',
                'name' => Str::slug('admin-modulos-crud-create'),
                'description' => 'Módulos crud - crear'
            ],
            [
                'display_name' => 'Módulos crud - actualizar',
                'name' => Str::slug('admin-modulos-crud-update'),
                'description' => 'Módulos crud - actualizar'
            ],
            [
                'display_name' => 'Módulos crud - borrar',
                'name' => Str::slug('admin-modulos-crud-delete'),
                'description' => 'Módulos crud - borrar'
            ],
            [
                'display_name' => 'Módulos crud - ver',
                'name' => Str::slug('admin-modulos-crud-read'),
                'description' => 'Módulos crud - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name", "=", Str::slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", Str::slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
