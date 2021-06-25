<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

class CreateRolesElearning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => ElearningRolesSeeder::class
        ]);

        $permissionNumber = Permission::where('name', 'frontend-elearning')->count();

        if ($permissionNumber>0) {
            Artisan::call('db:seed', [
                '--class' => ElearningFrontPermissionSeeder::class
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
