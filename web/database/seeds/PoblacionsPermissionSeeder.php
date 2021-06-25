<?php


use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;
use Illuminate\Support\Str;

class PoblacionsPermissionSeeder extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-poblacions'))->first();
        if(!empty($permExists)) {
            return;
        }

        // Módulo de poblacions
        $permissions = [
            [
                'display_name' => 'Poblaciones',
                'name' => Str::slug('admin-poblacions'),
                'description' => 'Poblaciones - Módulo'
            ],
            [
                'display_name' => 'Poblaciones - listado',
                'name' => Str::slug('admin-poblacions-list'),
                'description' => 'Poblaciones - lista'
            ],
            [
                'display_name' => 'Poblaciones - crear',
                'name' => Str::slug('admin-poblacions-create'),
                'description' => 'Poblaciones - crear'
            ],
            [
                'display_name' => 'Poblaciones - actualizar',
                'name' => Str::slug('admin-poblacions-update'),
                'description' => 'Poblaciones - actualizar'
            ],
            [
                'display_name' => 'Poblaciones - borrar',
                'name' => Str::slug('admin-poblacions-delete'),
                'description' => 'Poblaciones - borrar'
            ],
            [
                'display_name' => 'Poblaciones - ver',
                'name' => Str::slug('admin-poblacions-read'),
                'description' => 'Poblaciones - ver'
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
