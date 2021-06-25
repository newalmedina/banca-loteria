<?php

use Clavel\Elearning\Models\TipoPregunta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposPreguntas extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_preguntas')->delete();

        $pregunta = new TipoPregunta();
        $pregunta->id = 1;
        $pregunta->nombre = "Respuesta única";
        $pregunta->activa = true;
        $pregunta->slug = "unica";
        $pregunta->save();

        $pregunta = new TipoPregunta();
        $pregunta->id = 2;
        $pregunta->nombre = "Varias respuestas";
        $pregunta->activa = true;
        $pregunta->slug = "multiple";
        $pregunta->save();

        $pregunta = new TipoPregunta();
        $pregunta->id = 3;
        $pregunta->nombre = "Tipo texto";
        $pregunta->activa = false;
        $pregunta->slug = "texto";
        $pregunta->save();

        $pregunta = new TipoPregunta();
        $pregunta->id = 4;
        $pregunta->nombre = "Selector número";
        $pregunta->activa = false;
        $pregunta->slug = "numero";
        $pregunta->save();
    }
}
