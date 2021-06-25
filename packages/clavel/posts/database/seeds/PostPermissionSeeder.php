<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class PostPermissionSeeder extends BaseSeeder
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
        $adminStruct->display_name = 'Noticias Grupo';
        $adminStruct->name = Str::slug('admin-posts-group');
        $adminStruct->description = 'Noticias - Módulo';
        $adminStruct->save();
        $childAdminStruct = $this->childAdmin->children()->create(['permissions_id' => $adminStruct->id]);
        $this->a_permission_admin[] = $adminStruct->id;

        //Módulo de noticias
        $permissions = [
            [
                'display_name' => 'Noticias',
                'name' => Str::slug('admin-posts'),
                'description' => 'Noticias - Módulo'
            ],
            [
                'display_name' => 'Noticias - listado',
                'name' => Str::slug('admin-posts-list'),
                'description' => 'Noticias - lista'
            ],
            [
                'display_name' => 'Noticias - crear',
                'name' => Str::slug('admin-posts-create'),
                'description' => 'Noticias - crear'
            ],
            [
                'display_name' => 'Noticias - actualizar',
                'name' => Str::slug('admin-posts-update'),
                'description' => 'Noticias - actualizar'
            ],
            [
                'display_name' => 'Noticias - borrar',
                'name' => Str::slug('admin-posts-delete'),
                'description' => 'Noticias - borrar'
            ],
            [
                'display_name' => 'Noticias - ver',
                'name' => Str::slug('admin-posts-read'),
                'description' => 'Noticias - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Módulo de noticias comentarios
        $permissions = [
            [
                'display_name' => 'Noticias - Comentarios',
                'name' => Str::slug('admin-posts-comments'),
                'description' => 'Noticias - Comentarios - Módulo'
            ],
            [
                'display_name' => 'Noticias - Comentarios - listado',
                'name' => Str::slug('admin-posts-comments-list'),
                'description' => 'Noticias - Comentarios - lista'
            ],
            [
                'display_name' => 'Noticias - Comentarios - crear',
                'name' => Str::slug('admin-posts-comments-create'),
                'description' => 'Noticias - Comentarios - crear'
            ],
            [
                'display_name' => 'Noticias - Comentarios - actualizar',
                'name' => Str::slug('admin-posts-comments-update'),
                'description' => 'Noticias - Comentarios - actualizar'
            ],
            [
                'display_name' => 'Noticias - Comentarios - borrar',
                'name' => Str::slug('admin-posts-comments-delete'),
                'description' => 'Noticias - Comentarios - borrar'
            ],
            [
                'display_name' => 'Noticias - Comentarios - ver',
                'name' => Str::slug('admin-posts-comments-read'),
                'description' => 'Noticias - Comentarios - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Módulo de noticias tags
        $permissions = [
            [
                'display_name' => 'Noticias - Tags',
                'name' => Str::slug('admin-posts-tags'),
                'description' => 'Noticias - Tags - Módulo'
            ],
            [
                'display_name' => 'Noticias - Tags - listado',
                'name' => Str::slug('admin-posts-tags-list'),
                'description' => 'Noticias - Tags - lista'
            ],
            [
                'display_name' => 'Noticias - Tags - crear',
                'name' => Str::slug('admin-posts-tags-create'),
                'description' => 'Noticias - Tags - crear'
            ],
            [
                'display_name' => 'Noticias - Tags - actualizar',
                'name' => Str::slug('admin-posts-tags-update'),
                'description' => 'Noticias - Tags - actualizar'
            ],
            [
                'display_name' => 'Noticias - Tags - borrar',
                'name' => Str::slug('admin-posts-tags-delete'),
                'description' => 'Noticias - Tags - borrar'
            ],
            [
                'display_name' => 'Noticias - Tags - ver',
                'name' => Str::slug('admin-posts-tags-read'),
                'description' => 'Noticias - Tags - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name", "=", Str::slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", Str::slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);



        // Agrupador de puntos de menu - Crud Generator
        $frontStruct = new Permission();
        $frontStruct->display_name = 'Noticias con permisos';
        $frontStruct->name = Str::slug('front-posts');
        $frontStruct->description = 'Noticias con permisos';
        $frontStruct->save();

        $childAdminStruct = $this->childWeb->children()->create(['permissions_id' => $frontStruct->id]);
        $this->a_permission_front[] = $frontStruct->id;

        $roleUser = Role::where("name", "=", Str::slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
