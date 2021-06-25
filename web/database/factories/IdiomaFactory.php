<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Idioma;
use Faker\Generator as Faker;

$factory->define(Idioma::class, function (Faker $faker) {

    return [
        'active' => $faker->numberBetween(0,1),
'code' => config('app.locale'),
'locale' => config('app.locale'),
'name' => $faker->word(),
'default' => $faker->numberBetween(0,1)
    ];
});










