<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date_post')->nullable();
            $table->date('date_activation')->nullable();
            $table->date('date_deactivation')->nullable();
            $table->boolean('in_home')->default(0);
            $table->date('date_deactivation_home')->nullable();
            $table->boolean('active')->default(0);
            $table->boolean('has_shared')->default(0);
            $table->boolean('has_comment')->default(0);
            $table->boolean('has_comment_only_user')->default(0);
            $table->boolean('permission')->default(0);
            $table->string('permission_name')->nullable();
            $table->timestamps();
        });

        Schema::create('post_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->string('url_seo')->nullable();
            $table->text('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_content')->nullable();

            $table->unique(['post_id','locale','url_seo']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('post_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->unique(['post_id', 'role_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('post_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->string('path')->nullable();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('post_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->integer('parent_id')->default(0);
            $table->string('user')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('comment')->nullable();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('post_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active')->default(0);
            $table->timestamps();
        });

        Schema::create('post_tag_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_tag_id')->unsigned();
            $table->string('locale')->index();
            $table->string('tag')->nullable();

            $table->unique(['post_tag_id','locale','tag']);
            $table->foreign('post_tag_id')->references('id')->on('post_tags')->onDelete('cascade');
        });

        Schema::create('post_post_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->integer('post_tag_id')->unsigned();

            $table->unique(['post_id', 'post_tag_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('post_tag_id')->references('id')->on('post_tags')->onDelete('cascade');
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
        Schema::drop('post_post_tag');
        Schema::drop('post_tag_translations');
        Schema::drop('post_tags');
        Schema::drop('post_comments');
        Schema::drop('post_translations');
        Schema::drop('post_role');
        Schema::drop('post_images');
        Schema::drop('posts');
    }
}
