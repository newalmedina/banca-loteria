<?php

use Clavel\Elearning\Models\Especialidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_especialidad')->delete();

        $array_especialidad = array(
            [1, 'Médicos'],
            [2, 'Hematólogos'],
            [3, 'Analista Biólogo'],
            [4, 'Analista Biotecnólogo'],
            [5, 'Analista Biomédico'],
            [6, 'Analista Farmacéutico'],
            [7, 'Analista Químico'],
            [8, 'Otra'],
        );

        foreach ($array_especialidad as $especialidad_info) {
            $especialidad = new Especialidad();
            $especialidad->id = $especialidad_info[0];
            $especialidad->nombre = $especialidad_info[1];
            $especialidad->save();
        }
    }
}
