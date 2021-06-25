<?php

use Clavel\TimeTracker\Models\Activity;
use Clavel\TimeTracker\Models\Project;
use Faker\Generator as Faker;

$factory->define(Activity::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => $faker->paragraph(5),
        'active' => $faker->boolean(),
        'fixed_rate' => $faker->randomFloat(2, 100, 30000),
        'hourly_rate' => $faker->randomFloat(2, 10, 160)
    ];
});
