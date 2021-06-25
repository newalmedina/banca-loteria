<?php


use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\PermissionsTree;
use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    protected $root = null;
    protected $adminPermission = null;
    protected $childAdmin = null;
    protected $webPermission = null;
    protected $childWeb = null;
    protected $apiPermission = null;
    protected $childApi = null;
    protected $a_permission_admin = null;
    protected $a_permission_front = null;

    protected function init() {
        $this->root = PermissionsTree::where('permissions_id', null)->first();
        $this->adminPermission = Permission::where('name', 'admin')->first();
        $this->childAdmin = PermissionsTree::where('permissions_id', $this->adminPermission->id)->first();

        $this->webPermission = Permission::where('name', 'frontend')->first();
        $this->childWeb = PermissionsTree::where('permissions_id', $this->webPermission->id)->first();


        $this->apiPermission = Permission::where('name', 'api')->first();
        $this->childApi = PermissionsTree::where('permissions_id', $this->apiPermission->id)->first();

        $this->a_permission_admin = array();
        $this->a_permission_front = array();
    }

    protected function insertPermissions($permissions, $root, &$a_permission) {
        $MenuChild = null;
        foreach ($permissions as $key=>$permission) {
            $permission = Permission::firstOrCreate($permission);
            $permission->save();
            if ($key==0) {
                $MenuChild = $root->children()->create(['permissions_id' => $permission->id]);
            } else {
                if(!empty($MenuChild)) {
                    $MenuChild->children()->create(['permissions_id' => $permission->id]);
                }
            }

            $a_permission[] = $permission->id;
        }
        return  $MenuChild;
    }

    protected function getBasicPermissions($moduleName, $moduleSlug) {
        $permissions = [
            [
                'display_name' => $moduleName.'',
                'name' => Str::slug('admin-'.$moduleSlug),
                'description' => $moduleName.' - Módulo'
            ],
            [
                'display_name' => $moduleName.' - listado',
                'name' => Str::slug('admin-'.$moduleSlug.'-list'),
                'description' => $moduleName.' - lista'
            ],
            [
                'display_name' => $moduleName.' - crear',
                'name' => Str::slug('admin-'.$moduleSlug.'-create'),
                'description' => $moduleName.' - crear'
            ],
            [
                'display_name' => $moduleName.' - actualizar',
                'name' => Str::slug('admin-'.$moduleSlug.'-update'),
                'description' => $moduleName.' - actualizar'
            ],
            [
                'display_name' => $moduleName.' - borrar',
                'name' => Str::slug('admin-'.$moduleSlug.'-delete'),
                'description' => $moduleName.' - borrar'
            ],
            [
                'display_name' => $moduleName.' - ver',
                'name' => Str::slug('admin-'.$moduleSlug.'-read'),
                'description' => $moduleName.' - ver'
            ]
        ];

        return $permissions;
    }
}
