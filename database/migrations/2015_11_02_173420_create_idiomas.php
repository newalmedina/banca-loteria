<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdiomas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idiomas', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('code');
            $table->boolean('active')->default(false);
            $table->boolean('default')->default(false);
            $table->timestamps();
        });

        Schema::create('idioma_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('idioma_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();

            $table->unique(['idioma_id','locale']);
            $table->foreign('idioma_id')->references('id')->on('idiomas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('idiomas_translations');
        Schema::drop('idiomas');
    }
}
