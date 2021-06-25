<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTtConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tt_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string("budget_prefix", 5)->default("");
            $table->unsignedInteger("budget_counter")->default(0);
            $table->unsignedInteger("budget_digits")->default(3);
            $table->string("order_prefix", 5)->default("");
            $table->unsignedInteger("order_counter")->default(0);
            $table->unsignedInteger("order_digits")->default(3);
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
        Schema::dropIfExists('tt_config');
    }
}
