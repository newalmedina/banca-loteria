<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active')->default(0);
            $table->string('nombre')->nullable();
            $table->string('slug')->nullable();
            $table->string('background_content')->nullable();
            $table->string('background_page')->nullable();
            $table->string('font_color')->nullable();
            $table->longText('header')->nullable();
            $table->longText('footer')->nullable();
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
        Schema::drop('newsletter_templates');
    }
}
