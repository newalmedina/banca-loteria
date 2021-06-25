<?php

return [

    'facebook' => [
        "ico" => 'fa-facebook-square',
        "meta" => [
            'label' => 'Facebook data',
            'active' => env("PROVIDER_FACEBOOK", 0),
            'property' => 'property',
            'inputs' => [
                'og:title' => [
                    'type' => 'text',
                    'label' => 'title_facebook',
                    'isimage' => 0
                ],
                'og:description' => [
                    'type' => 'textarea',
                    'label' => 'description_facebook',
                    'isimage' => 0
                ],
                'og:image' => [
                    'type' => 'text',
                    'label' => 'image_facebook',
                    'isimage' => 1
                ]
            ]
        ]
    ],
    'twitter' => [
        "ico" => 'fa-twitter-square',
        "meta" => [
            'label' => 'Twitter data',
            'active' => env("PROVIDER_TWITTER", 0),
            'property' => 'name',
            'inputs' => [
                'twitter:title' => [
                    'type' => 'text',
                    'label' => 'twitter_facebook',
                    'isimage' => 0
                ],
                'twitter:description' => [
                    'type' => 'textarea',
                    'label' => 'description_twitter',
                    'isimage' => 0
                ],
                'twitter:image:src' => [
                    'type' => 'text',
                    'label' => 'image_twitter',
                    'isimage' => 1
                ]
            ]
        ]
    ],
    'googleplus' => [
        "ico" => 'fa-google-plus-square',
        "meta" => [
            'label' => 'Google + data',
            'active' => env("PROVIDER_GOOGLE_PLUS", 0),
            'property' => 'itemprop',
            'inputs' => [
                'name' => [
                    'type' => 'text',
                    'label' => 'gplus_title',
                    'isimage' => 0
                ],
                'description' => [
                    'type' => 'textarea',
                    'label' => 'description_gplus',
                    'isimage' => 0
                ],
                'image' => [
                    'type' => 'text',
                    'label' => 'gplus_image',
                    'isimage' => 1
                ]
            ]
        ]
    ]

];
