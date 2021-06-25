<?php

use Clavel\TimeTracker\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'code' => $faker->numerify('C#######'),
        'description' => $faker->paragraph(5),
        'company' => $faker->company. " ". $faker->companySuffix,
        'contact' => $faker->name. " ".$faker->firstName,
        'address' => $faker->address,
        'country' => $faker->countryCode,
        'currency' => $faker->currencyCode,
        'phone' => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'mobile' => $faker->phoneNumber,
        'email' => $faker->companyEmail,
        'homepage' => $faker->url,
        'timezone' => $faker->timezone,
        'active' => $faker->boolean(),
        'fixed_rate' => $faker->randomFloat(2, 100, 30000),
        'hourly_rate' => $faker->randomFloat(2, 10, 160)
    ];
});
