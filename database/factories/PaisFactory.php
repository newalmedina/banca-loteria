<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\Pais\Models\Pais;
use Faker\Generator as Faker;

$factory->define(Pais::class, function (Faker $faker) {

    return [
        'active' => $faker->numberBetween(0,1),
'name' => $faker->word(),
'description' => $faker->sentence(),
'code' => $faker->word()
    ];
});










