<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->boolean('in_home')->default(0);
            $table->boolean('has_shared')->default(0);
            $table->boolean('active')->default(0);
            $table->boolean('permission')->default(0);
            $table->string('permission_name')->nullable();
            $table->float('lat', 10, 6)->nullable();
            $table->float('long', 10, 6)->nullable();
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('event_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->string('url_seo')->nullable();
            $table->text('body')->nullable();
            $table->string('localization')->nullable();
            $table->string('link')->nullable();

            $table->unique(['event_id','locale','url_seo']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('event_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->unique(['event_id', 'role_id']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('event_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->string('path')->nullable();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('event_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active')->default(0);
            $table->string("color", 7)->nullable();
            $table->timestamps();
        });

        Schema::create('event_tag_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_tag_id')->unsigned();
            $table->string('locale')->index();
            $table->string('tag')->nullable();

            $table->unique(['event_tag_id','locale','tag']);
            $table->foreign('event_tag_id')->references('id')->on('event_tags')->onDelete('cascade');
        });

        Schema::create('event_event_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('event_tag_id')->unsigned();

            $table->unique(['event_id', 'event_tag_id']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('event_tag_id')->references('id')->on('event_tags')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('event_favorites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->unique(['event_id', 'user_id']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_favorites');
        Schema::drop('event_event_tag');
        Schema::drop('event_tag_translations');
        Schema::drop('event_tags');
        Schema::drop('event_images');
        Schema::drop('event_role');
        Schema::drop('event_translations');
        Schema::drop('events');
    }
}
