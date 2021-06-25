<?php

use Clavel\Basic\Models\Page;
use Clavel\Basic\Models\PageTranslation;
use Faker\Generator as Faker;

$factory->define(Page::class, function (Faker $faker) {
    return [
        
            'active' => $faker->boolean(),
            'css' => null,
            'javascript' => null,
            'permission' => 0,
            'permission_name' => null,
            'created_id' => 1,
            'modified_id' => 1,

    ];
});



$factory->define(PageTranslation::class, function (Faker $faker) {
    return [

        'locale' => 'es',
        'title' => $faker->sentence(5),
        'body' => $faker->paragraph(5),
        'meta_title' => $faker->name(),
        'meta_content' => $faker->sentence(2),
        'page_id' => factory(Page::class)->create()->id,
    ];
});
