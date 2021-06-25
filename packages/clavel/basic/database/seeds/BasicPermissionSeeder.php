<?php

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\PermissionsTree;

class BasicPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();

        $this->apiPermission = Permission::where('name', 'api')->first();
        $this->childApi = PermissionsTree::where('permissions_id', $this->apiPermission->id)->first();

        $this->a_permission_admin = array();
        $this->a_permission_front = array();

        // Agrupador de puntos de menu - Estucctura de la web
        $adminStruct = new Permission();
        $adminStruct->display_name = 'Estructura web';
        $adminStruct->name = Str::slug('admin-struct');
        $adminStruct->description = 'Estructura web - Módulo';
        $adminStruct->save();
        $childAdminStruct = $this->childAdmin->children()->create(['permissions_id' => $adminStruct->id]);
        $this->a_permission_admin[] = $adminStruct->id;


        // Módulo de Menús
        $permissions = [
            [
                'display_name' => 'Menú',
                'name' => Str::slug('admin-menu'),
                'description' => 'Menú - Módulo'
            ],
            [
                'display_name' => 'Menú - listado',
                'name' => Str::slug('admin-menu-list'),
                'description' => 'Menú - lista'
            ],
            [
                'display_name' => 'Menú - crear',
                'name' => Str::slug('admin-menu-create'),
                'description' => 'Menú - crear'
            ],
            [
                'display_name' => 'Menú - actualizar',
                'name' => Str::slug('admin-menu-update'),
                'description' => 'Menú - actualizar'
            ],
            [
                'display_name' => 'Menú - borrar',
                'name' => Str::slug('admin-menu-delete'),
                'description' => 'Menú - borrar'
            ],
            [
                'display_name' => 'Menú - ver',
                'name' => Str::slug('admin-menu-read'),
                'description' => 'Menú - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);


        // Module Páginas
        $permissions = [
            [
                'display_name' => 'Páginas',
                'name' => Str::slug('admin-pages'),
                'description' => 'Páginas - Módulo'
            ],
            [
                'display_name' => 'Páginas - listado',
                'name' => Str::slug('admin-pages-list'),
                'description' => 'Páginas - lista'
            ],
            [
                'display_name' => 'Páginas - crear',
                'name' => Str::slug('admin-pages-create'),
                'description' => 'Páginas - crear'
            ],
            [
                'display_name' => 'Páginas - actualizar',
                'name' => Str::slug('admin-pages-update'),
                'description' => 'Páginas - actualizar'
            ],
            [
                'display_name' => 'Páginas - borrar',
                'name' => Str::slug('admin-pages-delete'),
                'description' => 'Páginas - borrar'
            ],
            [
                'display_name' => 'Páginas - ver',
                'name' => Str::slug('admin-pages-read'),
                'description' => 'Páginas - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Modulo de paginas Front end
        $adminPage = new Permission();
        $adminPage->display_name = 'Páginas con permisos';
        $adminPage->name = Str::slug('front-pages');
        $adminPage->description = 'Páginas con permisos';
        $adminPage->save();
        $this->childWeb->children()->create(['permissions_id' => $adminPage->id]);
        $this->a_permission_admin[] = $adminPage->id;
        $this->a_permission_front[] = $adminPage->id;

        // Module Multimedia
        $permissions = [
            [
                'display_name' => 'Media',
                'name' => Str::slug('admin-media'),
                'description' => 'Media - Módulo'
            ],
            [
                'display_name' => 'Media - listado',
                'name' => Str::slug('admin-media-list'),
                'description' => 'Media - lista'
            ],
            [
                'display_name' => 'Media - crear',
                'name' => Str::slug('admin-media-create'),
                'description' => 'Media - crear'
            ],
            [
                'display_name' => 'Media - actualizar',
                'name' => Str::slug('admin-media-update'),
                'description' => 'Media - actualizar'
            ],
            [
                'display_name' => 'Media - borrar',
                'name' => Str::slug('admin-media-delete'),
                'description' => 'Media - borrar'
            ],
            [
                'display_name' => 'Media - ver',
                'name' => Str::slug('admin-media-read'),
                'description' => 'Media - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);


        // Rol de administrador
        $roleAdmin = Role::where("name", "=", Str::slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", Str::slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);



        /*

        //------------------------------------------------------------------------------------------------
        // Esturctura de la web
        $adminRole = new Permission();
        $adminRole->display_name = 'Estructura web';
        $adminRole->name = Str::slug('admin-struct');
        $adminRole->description = 'Estructura web - Módulo';
        $adminRole->save();
        $childAdminStruct = $this->childAdmin->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;


        // Módulo de Menús
        $adminRole = new Permission();
        $adminRole->display_name = 'Menú';
        $adminRole->name = Str::slug('admin-menu');
        $adminRole->description = 'Menú - Módulo';
        $adminRole->save();
        $childAdminMenu = $childAdminStruct->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Menú - listado';
        $adminRole->name = Str::slug('admin-menu-list');
        $adminRole->description = 'Menú - lista';
        $adminRole->save();
        $childAdminMenu->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Menú - crear';
        $adminRole->name = Str::slug('admin-menu-create');
        $adminRole->description = 'Menú - crear';
        $adminRole->save();
        $childAdminMenu->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Menú - actualizar';
        $adminRole->name = Str::slug('admin-menu-update');
        $adminRole->description = 'Menú - actualizar';
        $adminRole->save();
        $childAdminMenu->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Menú - borrar';
        $adminRole->name = Str::slug('admin-menu-delete');
        $adminRole->description = 'Menú - borrar';
        $adminRole->save();
        $childAdminMenu->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Menú - ver';
        $adminRole->name = Str::slug('admin-menu-read');
        $adminRole->description = 'Menú - ver';
        $adminRole->save();
        $childAdminMenu->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        // Módulo de Páginas
        $adminRole = new Permission();
        $adminRole->display_name = 'Páginas';
        $adminRole->name = Str::slug('admin-pages');
        $adminRole->description = 'Páginas - Módulo';
        $adminRole->save();
        $childAdminPages = $childAdminStruct->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Páginas - listado';
        $adminRole->name = Str::slug('admin-pages-list');
        $adminRole->description = 'Páginas - lista';
        $adminRole->save();
        $childAdminPages->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Páginas - crear';
        $adminRole->name = Str::slug('admin-pages-create');
        $adminRole->description = 'Páginas - crear';
        $adminRole->save();
        $childAdminPages->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Páginas - actualizar';
        $adminRole->name = Str::slug('admin-pages-update');
        $adminRole->description = 'Páginas - actualizar';
        $adminRole->save();
        $childAdminPages->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Páginas - borrar';
        $adminRole->name = Str::slug('admin-pages-delete');
        $adminRole->description = 'Páginas - borrar';
        $adminRole->save();
        $childAdminPages->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Páginas - ver';
        $adminRole->name = Str::slug('admin-pages-read');
        $adminRole->description = 'Páginas - ver';
        $adminRole->save();
        $childAdminPages->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        // Multimedia
        $adminRole = new Permission();
        $adminRole->display_name = 'Media';
        $adminRole->name = Str::slug('admin-media');
        $adminRole->description = 'Media - Módulo';
        $adminRole->save();
        $childAdminMedia = $childAdminStruct->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Media - listado';
        $adminRole->name = Str::slug('admin-media-list');
        $adminRole->description = 'Media - lista';
        $adminRole->save();
        $childAdminMedia->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Media - crear';
        $adminRole->name = Str::slug('admin-media-create');
        $adminRole->description = 'Media - crear';
        $adminRole->save();
        $childAdminMedia->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Media - borrar';
        $adminRole->name = Str::slug('admin-media-delete');
        $adminRole->description = 'Media - borrar';
        $adminRole->save();
        $childAdminMedia->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Media - ver';
        $adminRole->name = Str::slug('admin-media-read');
        $adminRole->description = 'Media - ver';
        $adminRole->save();
        $childAdminMedia->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;
         */
    }
}
