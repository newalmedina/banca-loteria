<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionAlumno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissionNumber = Permission::where('name', 'admin-elearning')->count();

        if ($permissionNumber>0) {
            Artisan::call('db:seed', [
                '--class' => ElearningAlumnoPermissionSeeder::class
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
