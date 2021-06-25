<?php

// Module Posts
Route::group(
    [
        'module' => 'Newsletter',
        'middleware' => ['web'],
        'namespace' => 'App\Modules\Newsletter\Controllers'
    ],
    function () {
        Route::get('/newsletter/listado', 'FrontNewsletterController@listado');
        Route::get('/newsletter/preview/{id}', 'FrontNewsletterController@previewNewsletter')->where("id", "[0-9]+");
        Route::get('/newsletter', 'FrontNewsletterController@index');

        Route::group(['middleware' => 'auth'], function () {
            Route::get('/newsletter/unsubscribe/{id}', "FrontNewsletterController@unsubscribe")
                ->where("id", '[0-9]+');
            Route::post('/newsletter/subscribe', "FrontNewsletterController@subscribe");

            Route::resource('newsletter', 'FrontNewsletterController');
        });


        Route::group(array('prefix' => 'admin'), function () {

            // Newsletter
            Route::get(
                'newsletter/form/plantilla_contenidos/{row_id}/{position}',
                "AdminNewsletterController@hfContenidos"
            );
            Route::get('newsletter/form/{template_id}/{row_id}/{position}/{id?}', "AdminNewsletterController@formData");
            Route::post(
                "newsletter/savefield",
                ['as' => 'newsletter.savefield', "uses" => "AdminNewsletterController@savefield"]
            );
            Route::post("newsletter/delete_post", "AdminNewsletterController@deletePost");

            Route::get("newsletter/preview/{id}/{locale?}", 'AdminNewsletterController@previewNewsletter');

            Route::post("newsletter/set_row", 'AdminNewsletterController@setRow');
            Route::post("newsletter/custom_template", 'AdminNewsletterController@customTemplate');
            Route::post("newsletter/deshacer", 'AdminNewsletterController@deshacer');
            Route::post("newsletter/reorder", 'AdminNewsletterController@reorder');
            Route::post("newsletter/delete_row", 'AdminNewsletterController@deleteRow');

            Route::post("newsletter/list", 'AdminNewsletterController@getData');
            Route::resource('newsletter', 'AdminNewsletterController');

            // Newsletter lists
            Route::post("newsletter-lists/list", 'AdminNewsletterListController@getData');
            Route::resource('newsletter-lists', 'AdminNewsletterListController');

            // Newsletter subscribers
            Route::get("newsletter-subscribers/generateExcel", 'AdminNewsletterSubscriberController@generateExcel');
            Route::post("newsletter-subscribers/list", 'AdminNewsletterSubscriberController@getData');
            Route::resource('newsletter-subscribers', 'AdminNewsletterSubscriberController');

            // Newsletter campaings
            Route::post("newsletter-campaigns/{id}/send", 'AdminNewsletterCampaignController@send');
            Route::post("newsletter-campaigns/{id}/prepare", 'AdminNewsletterCampaignController@prepare');
            Route::post("newsletter-campaigns/upload", 'AdminNewsletterCampaignController@uploadFiles');
            Route::get("newsletter-campaigns/{id}/duplicate", 'AdminNewsletterCampaignController@duplicate');
            Route::post("newsletter-campaigns/list", 'AdminNewsletterCampaignController@getData');
            Route::post("newsletter-campaigns/sent_list/{id}", 'AdminNewsletterCampaignController@getSentList');
            Route::resource('newsletter-campaigns', 'AdminNewsletterCampaignController');


            // Module Plantillas
            Route::get('templates/state/{id}', 'AdminTemplatesController@setChangeState')->where('id', '[0-9]+');
            Route::post("templates/list", 'AdminTemplatesController@getData');
            Route::resource("templates", 'AdminTemplatesController');

            Route::group(array('prefix' => 'templates'), function () {
                Route::get('{id}/design', 'AdminTemplatesDesignController@index')->where('id', '[0-9]+');
                Route::get('{id}/preview', 'AdminTemplatesDesignController@preview')->where('id', '[0-9]+');
                Route::post("{id}/design", 'AdminTemplatesDesignController@save');
                Route::post("{id}/duplicate", 'AdminTemplatesController@duplicate');
            });

            //Layout Designer

            Route::get(
                'admin/newsletter/edit/header/{id}}',
                ['as' => 'header.edit', 'uses' => 'AdminNewsletterController@editHeader']
            );
            Route::patch(
                'admin/newsletter/update/header/',
                ['as' => 'header.update', 'uses' => 'AdminNewsletterController@updateHeader']
            );
        });
    }
);
