<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class ForoPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();


        // Módulo de Foro
        $permissions = [
            [
                'display_name' => 'Foro',
                'name' => str_slug('frontend-foro'),
                'description' => 'Foro - Módulo'
            ],
            [
                'display_name' => 'Foro - Ver mensajes',
                'name' => str_slug('frontend-foro-list'),
                'description' => 'Foro - Ver mensajes'
            ],
            [
                'display_name' => 'Foro - crear',
                'name' => str_slug('frontend-foro-create'),
                'description' => 'Foro - crear'
            ],
            [
                'display_name' => 'Foro - actualizar (mensaje propio)',
                'name' => str_slug('frontend-foro-update'),
                'description' => 'Foro - actualizar'
            ],
            [
                'display_name' => 'Foro - borrar (todos)',
                'name' => str_slug('frontend-foro-delete'),
                'description' => 'Foro - borrar (todos)'
            ],
            [
                'display_name' => 'Foro - ver',
                'name' => str_slug('frontend-foro-read'),
                'description' => 'Foro - ver'
            ],
            [
                'display_name' => 'Foro - responder',
                'name' => str_slug('frontend-foro-reply'),
                'description' => 'Foro - responder'
            ],
            [
                'display_name' => 'Foro - borrar (mensaje propio)',
                'name' => str_slug('frontend-foro-delete-self'),
                'description' => 'Foro - borrar (mensaje propio)'
            ]
        ];

        $foroChild = null;
        foreach ($permissions as $key=>$permission) {
            $newPermission = Permission::firstOrCreate($permission);
            $newPermission->save();
            if ($key==0) {
                $foroChild = $this->childWeb->children()->create(['permissions_id' => $newPermission->id]);
            } else {
                $foroChild->children()->create(['permissions_id' => $newPermission->id]);
            }

            $this->a_permission_admin[] = $newPermission->id;
            $this->a_permission_front[] = $newPermission->id;
        }


        // Rol de administrador
        $roleAdmin = Role::where("name", "=", str_slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", str_slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
