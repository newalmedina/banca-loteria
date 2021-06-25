<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoModuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_modulos')->delete();
        DB::table('tipo_modulo_translations')->delete();

        $tipo = array(
            'active'=>1
        );

        $idtipo = DB::table('tipo_modulos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_modulo_id' => $idtipo,
            'nombre' => 'Caso clínco',
            'locale' => 'es',
        );

        DB::table('tipo_modulo_translations')->insert($tipo_translation);

        $tipo = array(
            'active'=>1
        );

        $idtipo = DB::table('tipo_modulos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_modulo_id' => $idtipo,
            'nombre' => 'Evaluación',
            'locale' => 'es',
        );

        DB::table('tipo_modulo_translations')->insert($tipo_translation);

        $tipo = array(
            'active'=>1
        );

        $idtipo = DB::table('tipo_modulos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_modulo_id' => $idtipo,
            'nombre' => 'Contenido teórico',
            'locale' => 'es',
        );

        DB::table('tipo_modulo_translations')->insert($tipo_translation);

        $tipo = array(
            'active'=>1
        );

        $idtipo = DB::table('tipo_modulos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_modulo_id' => $idtipo,
            'nombre' => 'Encuesta',
            'locale' => 'es',
        );

        DB::table('tipo_modulo_translations')->insert($tipo_translation);
    }
}
