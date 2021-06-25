<?php

use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterCampaign;
use App\Modules\Newsletter\Models\NewsletterMailingList;
use App\Modules\Newsletter\Models\NewsletterSubscription;
use Faker\Generator as Faker;

$factory->define(NewsletterMailingList::class, function (Faker $faker) {
    $name = $faker->bs;
    return [
        'name' => $name,
        'slug' => str_slug($name),
        'requires_opt_in' => $faker->boolean(75),
    ];
});


$factory->define(NewsletterCampaign::class, function (Faker $faker) {
    $sent_at = $faker->randomElement([
        $faker->dateTimeBetween('-10 days', 'now')->getTimestamp(),
        null
    ]);

    $scheduled_for = $faker->randomElement([
        $faker->dateTimeBetween('now', '+10 days')->getTimestamp(),
        null
    ]);
    return [
        'name' => $faker->bs,
        'newsletter_id' => null,
        'newsletter_campaign_state_id' => !empty($sent_at)?2:0,
        'sent_at' => $sent_at,
        'is_scheduled' => $scheduled_for!=null,
        'scheduled_for' => $scheduled_for,
    ];
});


$factory->define(NewsletterSubscription::class, function (Faker $faker) {
    $deleted_at = $faker->randomElement([
        $faker->dateTimeBetween('-10 days', 'now')->getTimestamp(),
        null
    ]);
    return [
        'list_id' => $faker->numberBetween(1, 5),
        'user_id' => 1,
        'opted_in' => $faker->boolean(75),
        'opted_in_at' => $faker->dateTimeBetween('-30 days', 'now')->getTimestamp()
    ];
});
