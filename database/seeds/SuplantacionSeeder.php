<?php


use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;


class SuplantacionSeeder extends BaseSeeder
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
        $adminStruct = Permission::where('name', Str::slug('admin-users'))->first();
        if(empty($adminStruct)) {
            return;
        }
        $childAdminStruct = PermissionsTree::where('permissions_id', $adminStruct->id)->first();

        // Módulo de usuarios

        $permissions = [
            [
                'display_name' => 'Usuarios - suplantar identidad',
                'name' => Str::slug('admin-users-suplantar'),
                'description' => 'Usuarios - suplantar identidad'
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
