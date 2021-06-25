<?php

use Clavel\Posts\Models\Post;
use Clavel\Posts\Models\PostComment;
use Clavel\Posts\Models\PostTag;
use Clavel\Posts\Models\PostTagTranslation;
use Clavel\Posts\Models\PostTranslation;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
            'date_post' => Carbon::now(),
            'date_activation' => Carbon::now()->subDays(30),
            'date_deactivation' => Carbon::now()->addDays($faker->numberBetween(30, 365)),
            'in_home' => $faker->boolean(80),
            'date_deactivation_home' => Carbon::now()->addDays($faker->numberBetween(0, 5)),
            'active' => $faker->boolean(80),
            'has_shared' => $faker->boolean(80),
            'has_comment' => $faker->boolean(80),
            'has_comment_only_user' => $faker->boolean(80),
            'permission' => 0,
            'permission_name' => null,


    ];
});

$factory->define(PostTranslation::class, function (Faker $faker) {
    $title = $faker->sentence(5);
    return [

        'locale' => 'es',
        'title' => $title,
        'body' => $faker->paragraph(5),
        'meta_title' => $faker->name(),
        'meta_content' => $faker->sentence(2),
        'post_id' => factory(Post::class)->create()->id,
    ];
});



$factory->define(PostTag::class, function (Faker $faker) {
    return [

        'active' => $faker->boolean(),
    ];
});

$factory->define(PostTagTranslation::class, function (Faker $faker) {
    return [

        'locale' => 'es',
        'tag' => $faker->word(),
        'post_tag_id' => factory(PostTag::class)->create()->id,
    ];
});

$factory->define(PostComment::class, function (Faker $faker) {
    return [
        'comment' => $faker->paragraph(5),
    ];
});
