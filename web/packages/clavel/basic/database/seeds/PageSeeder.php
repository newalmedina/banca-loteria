<?php

use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('page_translations')->delete();
        DB::table('pages')->delete();


        factory('Clavel\Basic\Models\PageTranslation', 50)->create();
    }
}
