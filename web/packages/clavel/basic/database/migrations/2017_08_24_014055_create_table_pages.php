<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active')->default(false);
            $table->text('css')->nullable();
            $table->text('javascript')->nullable();
            $table->boolean('permission')->default(0);
            $table->string('permission_name')->nullable();
            $table->integer('created_id')->unsigned();
            $table->integer('modified_id')->unsigned()->nullable();

            $table->timestamps();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->string('url_seo')->nullable();
            $table->text('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_content')->nullable();

            $table->unique(['page_id','locale','url_seo']);
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });

        Schema::create('page_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->string('provider')->nullable();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });

        Schema::create('page_provider_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_provider_id')->unsigned();
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->text('value')->nullable();

            $table->unique(['page_provider_id','locale','name']);
            $table->foreign('page_provider_id')->references('id')->on('page_providers')->onDelete('cascade');
        });

        Schema::create('page_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->unique(['page_id', 'role_id']);
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
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
        Schema::drop('page_role');
        Schema::drop('page_provider_translations');
        Schema::drop('page_providers');
        Schema::drop('page_translations');
        Schema::drop('pages');
    }
}
