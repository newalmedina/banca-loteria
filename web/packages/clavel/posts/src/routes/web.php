<?php

// Module Posts
Route::group(array('namespace' => 'Clavel\Posts\Controllers', 'middleware' => ['web']), function () {
    Route::get("posts/{selected_tag?}", 'FrontPostsController@index')->name('posts');
    Route::post("posts", 'FrontPostsController@index');
    Route::get("posts/post/{slug}", 'FrontPostsController@postsDetail')->name('posts.post');
    Route::post("posts/post", 'FrontPostsController@postComment')->name('posts.post.comment');

    Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::group(array('prefix' => 'posts'), function () {
            Route::post("tags/list", 'AdminPostTagsController@getData');
            Route::get('tags/state/{id}', 'AdminPostTagsController@setChangeState')->where('id', '[0-9]+');
            Route::get('tags/state_home/{id}', 'AdminPostTagsController@setChangeHome')->where('id', '[0-9]+');
            Route::get('tags/state_home/{id}', 'AdminPostTagsController@setChangeHome')->where('id', '[0-9]+');
            Route::resource('tags', 'AdminPostTagsController');
        });

        Route::group(array('prefix' => 'posts'), function () {
            Route::get("comments", 'AdminPostCommentsController@index');
            Route::post("comments/list", 'AdminPostCommentsController@getData');
            Route::delete('comments/{comment}', 'AdminPostCommentsController@destroy');
        });

        Route::group(array('prefix' => 'posts'), function () {
            Route::get("stats", 'AdminPostStatsController@index');
            Route::post("stats/list", 'AdminPostStatsController@getData');
            Route::get("stats/{id}/users", 'AdminPostStatsController@getStatsUsers')->where('id', '[0-9]+');
            Route::post("stats/{id}/users/list", 'AdminPostStatsController@getDataStatsUser');
            Route::get("stats/{id}/users/export", 'AdminPostStatsController@generateExcelUsers')->where('id', '[0-9]+');
            Route::get("stats/{id}/time", 'AdminPostStatsController@getStatsTime')->where('id', '[0-9]+');
        });

        Route::post("posts/list", 'AdminPostsController@getData');
        Route::get('posts/state/{id}', 'AdminPostsController@setChangeState')->where('id', '[0-9]+');
        Route::get('posts/state_home/{id}', 'AdminPostsController@setChangeHome')->where('id', '[0-9]+');
        Route::post('posts/notify/{id}', 'AdminPostsController@notifyPost')->where('id', '[0-9]+');
        Route::resource('posts', 'AdminPostsController');
    });
});
