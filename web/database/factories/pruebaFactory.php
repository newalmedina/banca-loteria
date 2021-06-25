<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\pruebas\Models\prueba;
use Faker\Generator as Faker;

$factory->define(prueba::class, function (Faker $faker) {

    return [
        'active' => $faker->numberBetween(0,1),
'name' => $faker->word(),
'description' => $faker->sentence()
    ];
});










