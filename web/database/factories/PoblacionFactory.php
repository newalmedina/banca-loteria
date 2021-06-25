<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\Poblacions\Models\Poblacion;
use Faker\Generator as Faker;

$factory->define(Poblacion::class, function (Faker $faker) {

    return [
        'active' => $faker->numberBetween(0,1),
'name' => $faker->word(),
'description' => $faker->sentence(),
'code' => $faker->word(),
'pais' => $faker->numberBetween(1,999)
    ];
});










