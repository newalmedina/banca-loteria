<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionAsignatura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissionNumber = Permission::where('name', 'admin-asignaturas')->count();

        if ($permissionNumber>0) {
            Artisan::call('db:seed', [
                '--class' => ElearningAsignaturaPermissionSeeder::class,
                '--force' => true
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
