<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();
        DB::table('permissions_tree')->delete();

        $this->root = PermissionsTree::create(['permissions_id' => null]);
        $this->a_permission_admin = array();
        $this->a_permission_front = array();

        // Permisos generales
        $adminRole = new Permission();
        $adminRole->display_name = 'Administrador';
        $adminRole->name = Str::slug('admin');
        $adminRole->description = 'Acceso a Administración';
        $adminRole->save();
        $this->childAdmin = $this->root->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Web';
        $adminRole->name = Str::slug('frontend');
        $adminRole->description = 'Acceso a  Front End Web';
        $adminRole->save();
        $this->childWeb = $this->root->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'API';
        $adminRole->name = Str::slug('api');
        $adminRole->description = 'Acceso a llamadas a web services y Api';
        $adminRole->save();
        $this->childApi = $this->root->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        // Módulo de dashboard
        $adminRole = new Permission();
        $adminRole->display_name = 'Dashboard';
        $adminRole->name = Str::slug('admin-dashboard');
        $adminRole->description = 'Acceso al Dashboard de administración';
        $adminRole->save();
        $this->childAdmin->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        //------------------------------------------------------------------------------------------------
        // Gestión de usuarios
        $adminRole = new Permission();
        $adminRole->display_name = 'Gestión de usuarios';
        $adminRole->name = Str::slug('admin-users-gral');
        $adminRole->description = 'Gestión de usuarios - Módulo';
        $adminRole->save();
        $childAdminUsersGral = $this->childAdmin->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        // Módulo de usuarios
        $adminRole = new Permission();
        $adminRole->display_name = 'Usuarios';
        $adminRole->name = Str::slug('admin-users');
        $adminRole->description = 'Usuarios - Módulo';
        $adminRole->save();
        $childAdminUsers = $childAdminUsersGral->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Usuarios - listado';
        $adminRole->name = Str::slug('admin-users-list');
        $adminRole->description = 'Usuarios - lista';
        $adminRole->save();
        $childAdminUsers->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Usuarios - crear';
        $adminRole->name = Str::slug('admin-users-create');
        $adminRole->description = 'Usuarios - crear';
        $adminRole->save();
        $childAdminUsers->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Usuarios - actualizar';
        $adminRole->name = Str::slug('admin-users-update');
        $adminRole->description = 'Usuarios - actualizar';
        $adminRole->save();
        $childAdminUsers->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Usuarios - borrar';
        $adminRole->name = Str::slug('admin-users-delete');
        $adminRole->description = 'Usuarios - borrar';
        $adminRole->save();
        $childAdminUsers->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Usuarios - ver';
        $adminRole->name = Str::slug('admin-users-read');
        $adminRole->description = 'Usuarios - ver';
        $adminRole->save();
        $childAdminUsers->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Usuarios - ver anónimo';
        $adminRole->name = Str::slug('admin-users-read-anonime');
        $adminRole->description = 'Usuarios - ver anónimo';
        $adminRole->save();
        $childAdminUsers->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        // Módulo de roles
        $adminRole = new Permission();
        $adminRole->display_name = 'Roles';
        $adminRole->name = Str::slug('admin-roles');
        $adminRole->description = 'Roles - Módulo';
        $adminRole->save();
        $childAdminRoles = $childAdminUsersGral->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Roles - listado';
        $adminRole->name = Str::slug('admin-roles-list');
        $adminRole->description = 'Roles - lista';
        $adminRole->save();
        $childAdminRoles->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Roles - crear';
        $adminRole->name = Str::slug('admin-roles-create');
        $adminRole->description = 'Roles - crear';
        $adminRole->save();
        $childAdminRoles->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Roles - actualizar';
        $adminRole->name = Str::slug('admin-roles-update');
        $adminRole->description = 'Roles - actualizar';
        $adminRole->save();
        $childAdminRoles->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Roles - borrar';
        $adminRole->name = Str::slug('admin-roles-delete');
        $adminRole->description = 'Roles - borrar';
        $adminRole->save();
        $childAdminRoles->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Roles - ver';
        $adminRole->name = Str::slug('admin-roles-read');
        $adminRole->description = 'Roles - ver';
        $adminRole->save();
        $childAdminRoles->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        // ---------------------------------------------------------------------------------------------------------

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
