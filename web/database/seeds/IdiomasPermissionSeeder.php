<?php


use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\PermissionsTree;

class IdiomasPermissionSeeder extends BaseSeeder
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
        $adminStruct = Permission::where('name', Str::slug('admin'))->first();
        $childAdminStruct = PermissionsTree::where('permissions_id', $adminStruct->id)->first();


        // Si los permisos los hemos creados volvemos
        $permExists = Permission::where('name', Str::slug('admin-idiomas'))->first();
        if(!empty($permExists)) {
            return;
        }

        // Módulo de idiomas
        $permissions = [
            [
                'display_name' => 'Idiomas',
                'name' => Str::slug('admin-idiomas'),
                'description' => 'Idiomas - Módulo'
            ],
            [
                'display_name' => 'Idiomas - listado',
                'name' => Str::slug('admin-idiomas-list'),
                'description' => 'Idiomas - lista'
            ],
            [
                'display_name' => 'Idiomas - crear',
                'name' => Str::slug('admin-idiomas-create'),
                'description' => 'Idiomas - crear'
            ],
            [
                'display_name' => 'Idiomas - actualizar',
                'name' => Str::slug('admin-idiomas-update'),
                'description' => 'Idiomas - actualizar'
            ],
            [
                'display_name' => 'Idiomas - borrar',
                'name' => Str::slug('admin-idiomas-delete'),
                'description' => 'Idiomas - borrar'
            ],
            [
                'display_name' => 'Idiomas - ver',
                'name' => Str::slug('admin-idiomas-read'),
                'description' => 'Idiomas - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name","=", Str::slug('admin'))->first();
        if(!empty($this->a_permission_admin)) {
            $roleAdmin->attachPermissions($this->a_permission_admin);
        }
        $roleUser = Role::where("name","=", Str::slug('usuario-front'))->first();
        if(!empty($this->a_permission_front)) {
            $roleUser->attachPermissions($this->a_permission_front);
        }
    }
}
