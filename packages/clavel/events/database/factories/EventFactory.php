<?php

use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventTag;
use App\Modules\Events\Models\EventTagTranslation;
use App\Modules\Events\Models\EventTranslation;
use App\Models\UserProfile;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    $start_date = Carbon::now()->subDays($faker->numberBetween(0, 30))->addDays($faker->numberBetween(0, 60));
    $end_date =  Carbon::parse($start_date)->addDays($faker->numberBetween(0, 15));
    return [
        'date_start' => $start_date,
        'date_end' => $end_date,
        'in_home' => $faker->boolean(80),
        'active' => $faker->boolean(80),
        'has_shared' => $faker->boolean(80),
        'permission' => 0,
        'permission_name' => null,
        'lat' => $faker->latitude,
        'long' => $faker->longitude,
        'user_id' => factory(UserProfile::class)->create()->user->id,

    ];
});

$factory->define(EventTranslation::class, function (Faker $faker) {
    $title = $faker->sentence(5);

    return [

        'locale' => 'es',
        'title' => $title,
        'body' => $faker->paragraph(5),
        'url_seo' =>  str_slug($title),
        'localization' => $faker->address. "-".$faker->city. "-". $faker->country,
        'link' => $faker->url,
        'event_id' => factory(Event::class)->create()->id,
    ];
});



$factory->define(EventTag::class, function (Faker $faker) {
    return [

        'active' => $faker->boolean(),
    ];
});

$factory->define(EventTagTranslation::class, function (Faker $faker) {
    return [

        'locale' => 'es',
        'tag' => $faker->word(),
        'event_tag_id' => factory(EventTag::class)->create()->id,
    ];
});
