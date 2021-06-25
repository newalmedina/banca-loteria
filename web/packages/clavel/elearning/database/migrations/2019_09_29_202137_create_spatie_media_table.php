<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpatieMediaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('spatie_media', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('model');
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->unsignedInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->nullableTimestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('spatie_media', function (Blueprint $table) {
            $table->dropForeign('spatie_media_user_id_foreign');
        });

        Schema::dropIfExists('spatie_media');
    }
}
