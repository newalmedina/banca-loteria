<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class PostStatsPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();

        // Obtenemos la raiz de
        $postPermission = Permission::where('name', 'admin-posts-group')->first();
        $postChildWeb = PermissionsTree::where('permissions_id', $postPermission->id)->first();


        // Module Posts - Stats
        $permissions = [
            [
                'display_name' => 'Noticias - estadísticas',
                'name' => str_slug('admin-posts-stats'),
                'description' => 'Noticias - estadísticas - Módulo'
            ],
            [
                'display_name' => 'Noticias - estadísticas - listado',
                'name' => str_slug('admin-posts-stats-list'),
                'description' => 'Noticias - estadísticas - lista'
            ],
            [
                'display_name' => 'Noticias - estadísticas - crear',
                'name' => str_slug('admin-posts-stats-create'),
                'description' => 'Noticias - estadísticas - crear'
            ],
            [
                'display_name' => 'Noticias - estadísticas - actualizar',
                'name' => str_slug('admin-posts-stats-update'),
                'description' => 'Noticias - estadísticas - actualizar'
            ],
            [
                'display_name' => 'Noticias - estadísticas - borrar',
                'name' => str_slug('admin-posts-stats-delete'),
                'description' => 'Noticias - estadísticas - borrar'
            ],
            [
                'display_name' => 'Noticias - estadísticas - ver',
                'name' => str_slug('admin-posts-stats-read'),
                'description' => 'Páginas - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $postChildWeb, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name", "=", str_slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", str_slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
