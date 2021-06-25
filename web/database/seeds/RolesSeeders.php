<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('roles')->delete();

        $adminRole = new Role;
        $adminRole->display_name = 'Administrador';
        $adminRole->name = Str::slug('admin');
        $adminRole->description = 'Administradores';
        $adminRole->fixed = true;
        $adminRole->active = true;
        $adminRole->save();

        $userRole = new Role;
        $userRole->display_name = 'Usuario front';
        $userRole->name = Str::slug('usuario-front');
        $userRole->description = 'Usuario de front-End';
        $userRole->fixed = true;
        $userRole->active = true;
        $userRole->save();

        $apiRole = new Role;
        $apiRole->display_name = 'Usuario Api';
        $apiRole->name = Str::slug('usuario-api');
        $apiRole->description = 'Usuario de Api';
        $apiRole->fixed = true;
        $apiRole->active = true;
        $apiRole->save();

        // Asignamos a cada usuario un role de manera aleatoria
        $users = User::get();
        $i = 0;
        foreach ($users as $user) {
            switch($i) {
                case 0:
                    $user->attachRole($adminRole->id);
                    break;
                default:
                    $user->attachRole($userRole->id);
                    break;
            }
            $i=($i+1)%3;

        }




    }
}
