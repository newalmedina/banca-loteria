<?php


use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;
use Illuminate\Support\Str;

class PaisPermissionSeeder extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-pais'))->first();
        if(!empty($permExists)) {
            return;
        }

        // Módulo de pais
        $permissions = [
            [
                'display_name' => 'País',
                'name' => Str::slug('admin-pais'),
                'description' => 'País - Módulo'
            ],
            [
                'display_name' => 'País - listado',
                'name' => Str::slug('admin-pais-list'),
                'description' => 'País - lista'
            ],
            [
                'display_name' => 'País - crear',
                'name' => Str::slug('admin-pais-create'),
                'description' => 'País - crear'
            ],
            [
                'display_name' => 'País - actualizar',
                'name' => Str::slug('admin-pais-update'),
                'description' => 'País - actualizar'
            ],
            [
                'display_name' => 'País - borrar',
                'name' => Str::slug('admin-pais-delete'),
                'description' => 'País - borrar'
            ],
            [
                'display_name' => 'País - ver',
                'name' => Str::slug('admin-pais-read'),
                'description' => 'País - ver'
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
