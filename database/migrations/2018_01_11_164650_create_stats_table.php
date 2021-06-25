<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stat_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ipuser')->nullable();
            $table->date('dateaccess')->nullable();
            $table->string('cityname')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('countryname')->nullable();
            $table->string('browser')->nullable();
            $table->boolean('is_mobile')->default(0);
            $table->boolean('is_login')->default(0);
            $table->timestamps();

            $table->unique(['dateaccess', 'ipuser', 'cityname', 'countryname']);
        });

        Schema::create('stat_user_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ipuser')->nullable();
            $table->date('dateaccess')->nullable();
            $table->string('route')->nullable();
            $table->string('titulo')->nullable();
            $table->timestamps();

            $table->unique(['route', 'dateaccess', 'ipuser']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stat_users');
        Schema::drop('stat_user_routes');
    }
}
