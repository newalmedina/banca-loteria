<?php

use Clavel\Elearning\Models\Provincia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provincias')->delete();

        $array_provincias = array([1, 'Araba/Álava', 'alava', 'País Vasco', 0, 0],
            [2, 'Albacete', 'albacete', 'Castilla-La Mancha', 0, 0],
            [3, 'Alicante/Alacant', 'alicante', 'Valencia', 0, 0],
            [4, 'Almería', 'almeria', 'Andalucía', 0, 0],
            [5, 'Ávila', 'avila', 'Castilla y León', 0, 0],
            [6, 'Badajoz', 'badajoz', 'Extremadura', 0, 0],
            [7, 'Balears, Illes', 'mallorca', 'Balears, Illes', 0, 0],
            [8, 'Barcelona', 'barcelona', 'Catalunya', 0, 0],
            [9, 'Burgos', 'burgos', 'Castilla y León', 0, 0],
            [10, 'Cáceres', 'caceres', 'Extremadura', 0, 0],
            [11, 'Cádiz', 'cadiz', 'Andalucía', 0, 0],
            [12, 'Castellón/Castelló', 'castellon', 'Valencia', 0, 0],
            [13, 'Ciudad Real', 'ciudadreal', 'Castilla-La Mancha', 0, 0],
            [14, 'Córdoba', 'cordoba', 'Andalucía', 0, 0],
            [15, 'Coruña, A', 'coruña', 'Galicia', 0, 0],
            [16, 'Cuenca', 'cuenca', 'Castilla-La Mancha', 0, 0],
            [17, 'Girona', 'girona', 'Catalunya', 0, 0],
            [18, 'Granada', 'granada', 'Andalucía', 0, 0],
            [19, 'Guadalajara', 'guadalajara', 'Castilla-La Mancha', 0, 0],
            [20, 'Gipuzkoa', 'guipuzcua', 'País Vasco', 0, 0],
            [21, 'Huelva', 'huelva', 'Andalucía', 0, 0],
            [22, 'Huesca', 'huesca', 'Aragón', 0, 0],
            [23, 'Jaén', 'jaen', 'Andalucía', 0, 0],
            [24, 'León', 'leon', 'Castilla y León', 0, 0],
            [25, 'Lleida', 'lleida', 'Catalunya', 0, 0],
            [26, 'Rioja, La', 'rioja', 'Rioja, La', 0, 0],
            [27, 'Lugo', 'lugo', 'Galicia', 0, 0],
            [28, 'Madrid', 'madrid', 'Madrid', 0, 0],
            [29, 'Málaga', 'malaga', 'Andalucía', 0, 0],
            [30, 'Murcia', 'murcia', 'Murcia', 0, 0],
            [31, 'Navarra', 'navarra', 'Navarra', 0, 0],
            [32, 'Ourense', 'ourense', 'Galicia', 0, 0],
            [33, 'Asturias', 'asturias', 'Asturias', 0, 0],
            [34, 'Palencia', 'palencia', 'Castilla y León', 0, 0],
            [35, 'Palmas, Las', 'palmas', 'Islas Canarias', 0, 0],
            [36, 'Pontevedra', 'pontevedra', 'Galicia', 0, 0],
            [37, 'Salamanca', 'salamanca', 'Castilla y León', 0, 0],
            [38, 'Santa Cruz de Tenerife', 'tenerife', 'Islas Canarias', 0, 0],
            [39, 'Cantabria', 'cantabria', 'Cantabria', 0, 0],
            [40, 'Segovia', 'segovia', 'Castilla y León', 0, 0],
            [41, 'Sevilla', 'sevilla', 'Andalucía', 0, 0],
            [42, 'Soria', 'soria', 'Castilla y León', 0, 0],
            [43, 'Tarragona', 'tarragona', 'Catalunya', 0, 0],
            [44, 'Teruel', 'teruel', 'Aragón', 0, 0],
            [45, 'Toledo', 'toledo', 'Castilla-La Mancha', 0, 0],
            [46, 'Valencia/València', 'valencia', 'Valencia', 0, 0],
            [47, 'Valladolid', 'valladolid', 'Castilla y León', 0, 0],
            [48, 'Bizkaia', 'vizcaya', 'País Vasco', 0, 0],
            [49, 'Zamora', 'zamora', 'Castilla y León', 0, 0],
            [50, 'Zaragoza', 'zaragoza', 'Aragón', 0, 0],
            [51, 'Ceuta', 'ceuta', 'Ceuta y Melilla', 0, 0],
            [52, 'Melilla', 'melilla', 'Ceuta y Melilla', 0, 0],
            [100, 'Otros Paises', 'otras', 'Otros Países', 1, 1]
        );

        foreach ($array_provincias as $provincia_info) {
            $provincia = new Provincia();
            $provincia->id = $provincia_info[0];
            $provincia->nombre = $provincia_info[1];
            $provincia->aux = $provincia_info[2];
            $provincia->ccaa = $provincia_info[3];
            $provincia->showOrder = $provincia_info[4];
            $provincia->fixed = $provincia_info[5];
            $provincia->save();
        }

        //Provincia::insert($array_provincias);
    }
}
