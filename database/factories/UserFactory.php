<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\UserProfile;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    static $password;

    return [
        'username' => $faker->userName(),
        'email' => $faker->unique()->safeEmail,
        'confirmed' => $faker->numberBetween(0,1),
        'email_verified_at' => now(),
        'active' => $faker->numberBetween(0,1),
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => Str::random(10)
    ];
});

$factory->define(UserProfile::class, function (Faker $faker) use ($factory) {

    return [
        'user_id' => factory(User::class)->create()->id,
        'first_name' => $faker->firstName(),
        'last_name' => $faker->lastName(),
        'gender' => $faker->randomElement(['male', 'female']),
        'phone' => $faker->phoneNumber(),
        'mobile' => $faker->phoneNumber(),
        'user_lang' => 'es',
    ];
});
