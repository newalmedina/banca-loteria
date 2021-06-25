<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdiomasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('idiomas')->delete();
        DB::table('idioma_translations')->delete();


        $idiomas = array(
            "es",
            "en",
            "ca"
        );

        $idiomas_lang = array (
            array(
                "es" =>  'Castellano',
                "en" => 'Spanish',
                "ca" => 'Castellà'
            ),
            array(
                "es" => 'Inglés',
                "en" => 'English',
                "ca" => 'Anglès'
            ),
            array(
                "es" => 'Catalán',
                "en" => 'Catalan',
                "ca" => 'Català'
            )

        );



        for($i=0; $i<sizeof($idiomas); $i++)
        {
            $idioma = array(
                'active' => true,
                'code' => $idiomas[$i],
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );

            $idioma_id = DB::table('idiomas')->insertGetId( $idioma );

            foreach($idiomas as $lang)
            {
                $idioma_translations = array(
                    'idioma_id' => $idioma_id,
                    'locale' => $lang,
                    'name' => $idiomas_lang[$i][$lang]
                );
                DB::table('idioma_translations')->insert( $idioma_translations );
            }
        }
    }
}
