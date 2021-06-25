<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('visits')->default(0);
            $table->timestamps();

            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        Schema::create('post_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->date('fecha')->nullable();
            $table->unsignedInteger('visits')->default(0);
            $table->timestamps();

            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_tracks', function (Blueprint $table) {
            $table->dropForeign('post_tracks_post_id_foreign');
            $table->dropForeign('post_tracks_user_id_foreign');
        });
        Schema::dropIfExists('post_tracks');

        Schema::table('post_stats', function (Blueprint $table) {
            $table->dropForeign('post_stats_post_id_foreign');
        });
        Schema::dropIfExists('post_stats');
    }
}
