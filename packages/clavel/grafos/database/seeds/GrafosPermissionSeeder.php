<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class GrafosPermissionSeeder extends BaseSeeder
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
        $adminStruct->display_name = 'Gráfos';
        $adminStruct->name = Str::slug('admin-grafos');
        $adminStruct->description = 'Gráfos - Módulo';
        $adminStruct->save();
        $childAdminStruct = $this->childAdmin->children()->create(['permissions_id' => $adminStruct->id]);
        $this->a_permission_admin[] = $adminStruct->id;

        // Módulo de modulos crud
        $permissions = [
            [
                'display_name' => 'Gráfos',
                'name' => Str::slug('admin-grafos'),
                'description' => 'Gráfos - Módulo'
            ],
            [
                'display_name' => 'Gráfos - listado',
                'name' => Str::slug('admin-grafos-list'),
                'description' => 'Gráfos - lista'
            ],
            [
                'display_name' => 'Gráfos - crear',
                'name' => Str::slug('admin-grafos-create'),
                'description' => 'Gráfos - crear'
            ],
            [
                'display_name' => 'Gráfos - actualizar',
                'name' => Str::slug('admin-grafos-update'),
                'description' => 'Gráfos - actualizar'
            ],
            [
                'display_name' => 'Gráfos - borrar',
                'name' => Str::slug('admin-grafos-delete'),
                'description' => 'Gráfos - borrar'
            ],
            [
                'display_name' => 'Gráfos - ver',
                'name' => Str::slug('admin-grafos-read'),
                'description' => 'Gráfos - ver'
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
