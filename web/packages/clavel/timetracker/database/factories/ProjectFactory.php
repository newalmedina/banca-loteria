<?php

use Clavel\TimeTracker\Models\Customer;
use Clavel\TimeTracker\Models\Project;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'order_number' => $faker->numerify('P#######'),
        'description' => $faker->paragraph(5),
        'customer_id' => Customer::all()->random()->id,
        'active' => $faker->boolean(),
        'budget' => $faker->randomFloat(2, 1000, 30000),
        'fixed_rate' => $faker->randomFloat(2, 100, 30000),
        'hourly_rate' => $faker->randomFloat(2, 10, 160)
    ];
});
