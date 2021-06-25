<?php


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrudGeneratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crud_modules')->delete();

        factory('Clavel\CrudGenerator\Models\Module', 100)->create();
    }
}
