<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ElearningRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $adminRole = new Role;
        $adminRole->display_name = 'Supervisor';
        $adminRole->name = str_slug('supervisor');
        $adminRole->description = 'Supervisor';
        $adminRole->fixed = true;
        $adminRole->active = true;
        $adminRole->save();

        $userRole = new Role;
        $userRole->display_name = 'Profesor';
        $userRole->name = str_slug('profesor');
        $userRole->description = 'Profesor';
        $userRole->fixed = true;
        $userRole->active = true;
        $userRole->save();
    }
}
