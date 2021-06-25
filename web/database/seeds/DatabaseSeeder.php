<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeders::class);
        $this->call(IdiomasSeeder::class);
        $this->call(RolesSeeders::class);
        $this->call(PermissionSeeder::class);
        $this->call(LoginAttemptPermissionSeeder::class);
        $this->call(SuplantacionSeeder::class);

        $this->call(IdiomasPermissionSeeder::class);


    }
}
