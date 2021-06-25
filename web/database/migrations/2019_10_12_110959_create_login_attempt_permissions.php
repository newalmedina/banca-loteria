<?php

use App\Models\PermissionsTree;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

class CreateLoginAttemptPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissionNumber = PermissionsTree::count();

        if($permissionNumber>0) {
            Artisan::call('db:seed', [
                '--class' => LoginAttemptPermissionSeeder::class,
                '--force'   => true
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
