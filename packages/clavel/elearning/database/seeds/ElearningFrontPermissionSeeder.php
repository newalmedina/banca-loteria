<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ElearningFrontPermissionSeeder extends BaseSeeder
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
        $webStruct = new Permission();
        $webStruct->display_name = 'Elearning';
        $webStruct->name = str_slug('frontend-elearning');
        $webStruct->description = 'Elearning - Módulo';
        $webStruct->save();
        $childWebStruct = $this->childWeb->children()->create(['permissions_id' => $webStruct->id]);
        $this->a_permission_admin[] = $webStruct->id;

        // Módulo de Front
        $permissions = [
            [
                'display_name' => 'Asignaturas',
                'name' => str_slug('frontend-asignaturas'),
                'description' => 'Asignaturas - Módulo'
            ],
            [
                'display_name' => 'Asignaturas - acceder fuera de convocatoria',
                'name' => str_slug('frontend-asignaturas-convocatoria-premium'),
                'description' => 'Asignaturas - acceder fuera de convocatoria',
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childWebStruct, $this->a_permission_front);

        // Rol de admin
        $roleUser = Role::where("name", "=", str_slug('admin'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
