<?php


use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionsTree;

class TimeTrackerPermissionSeeder extends BaseSeeder
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
        $adminStruct = new Permission();
        $adminStruct->display_name = 'Time Tracker';
        $adminStruct->name = str_slug('admin-timetracker');
        $adminStruct->description = 'Time Tracker - Módulo';
        $adminStruct->save();
        $childAdminStruct = $this->childAdmin->children()->create(['permissions_id' => $adminStruct->id]);
        $this->a_permission_admin[] = $adminStruct->id;


        /*
        // Para crear cuando ya existen
        $adminStruct = Permission::where('name', 'admin-timetracker')->first();
        $childAdminStruct = PermissionsTree::where("permissions_id", $adminStruct->id)->first();
        */



        // Módulo de Clientes
        $moduleName = "Clientes";
        $moduleSlug = "customers";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);


        // Módulo de Proyectos
        $moduleName = "Proyectos";
        $moduleSlug = "projects";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Módulo de Actividades
        $moduleName = "Actividades";
        $moduleSlug = "activities";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);


        // Módulo de Hojas de tiempo
        $moduleName = "Hojas de tiempo";
        $moduleSlug = "timesheet";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Módulo de Mis Tiempos
        $moduleName = "Mis tiempos";
        $moduleSlug = "mytimes";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        // Módulo de Dash Board
        $moduleName = "Time Dashboard";
        $moduleSlug = "timetracker-dashboard";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);




        // Módulo de Configuracion
        $moduleName = "Time Configuración";
        $moduleSlug = "timetracker-config";
        $permissions = $this->getBasicPermissions($moduleName, $moduleSlug);

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);


        // Rol de administrador
        $roleAdmin = Role::where("name", "=", str_slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", str_slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
