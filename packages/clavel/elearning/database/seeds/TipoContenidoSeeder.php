<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoContenidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_contenidos')->delete();
        DB::table('tipo_contenidos_translations')->delete();

        $tipo = array(
            "id" => 1,
            'idintable'=>'id',
            'vista'=>'elearning::contenidos.admin_partials.admin_tema',
            'vista_front'=>'tema',
            'slug'=>str_slug('tema')
        );

        $idtipo = DB::table('tipo_contenidos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_contenido_id' => $idtipo,
            'nombre' => 'Tema',
            'icono' => 'fa-bookmark-o',
            'locale' => 'es',
        );

        DB::table('tipo_contenidos_translations')->insert($tipo_translation);

        $tipo = array(
            "id" => 2,
            'idintable'=>'id',
            'vista'=>'elearning::contenidos.admin_partials.admin_pagina',
            'vista_front'=>'pagina',
            'slug'=>str_slug('pagina')
        );

        $idtipo = DB::table('tipo_contenidos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_contenido_id' => $idtipo,
            'nombre' => 'Página',
            'icono' => 'fa-edit',
            'locale' => 'es',
        );

        DB::table('tipo_contenidos_translations')->insert($tipo_translation);

        $tipo = array(
            "id" => 3,
            'idintable'=>'id',
            'vista'=>'elearning::contenidos.admin_partials.admin_eval',
            'vista_front'=>'eval',
            'slug'=>str_slug('eval')
        );

        $idtipo = DB::table('tipo_contenidos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_contenido_id' => $idtipo,
            'nombre' => 'Evaluación',
            'icono' => 'fa-question',
            'locale' => 'es',
        );

        DB::table('tipo_contenidos_translations')->insert($tipo_translation);


        $tipo = array(
            "id" => 4,
            'idintable'=>'id',
            'vista'=>'elearning::contenidos.admin_partials.admin_galeria',
            'vista_front'=>'galeria',
            'slug'=>str_slug('galeria')
        );

        $idtipo = DB::table('tipo_contenidos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_contenido_id' => $idtipo,
            'nombre' => 'Galería',
            'icono' => 'fa-picture-o',
            'locale' => 'es',
        );

        DB::table('tipo_contenidos_translations')->insert($tipo_translation);

        $tipo = array(
            "id" => 5,
            'idintable'=>'id',
            'vista'=>'elearning::contenidos.admin_partials.admin_video',
            'vista_front'=>'video',
            'slug'=>str_slug('video')
        );

        $idtipo = DB::table('tipo_contenidos')->insertGetId($tipo);

        $tipo_translation = array(
            'tipo_contenido_id' => $idtipo,
            'nombre' => 'Video',
            'icono' => 'fa-file-video-o',
            'locale' => 'es',
        );

        DB::table('tipo_contenidos_translations')->insert($tipo_translation);
    }
}
