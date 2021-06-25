<?php
/**
 * Created by PhpStorm.
 * User: jjcalvo
 * Date: 7/10/18
 * Time: 20:44
 */

/*



        // ---------------------------------------------------------------------------------------------------------
        // Module Settings
        /*$permissions = [
            [
                'display_name' => 'Settings',
                'name' => str_slug('admin-settings'),
                'description' => 'Settings Module'
            ],
            [
                'display_name' => 'Settings - update',
                'name' => str_slug('admin-settings-update'),
                'description' => 'Settings - update'
            ],

        ];

        foreach ($permissions as $permission) {
            $adminRole = Permission::firstOrCreate($permission);
            $adminRole->save();
            $this->childAdmin->children()->create(['permissions_id' => $adminRole->id]);
            $this->a_permission_admin[] = $adminRole->id;

        }
 */
