<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForoEntradas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foro_entradas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asignatura_id')->unsigned()->default(0);
            $table->integer('modulo_id')->unsigned()->default(0);
            $table->integer('contenido_id')->unsigned()->default(0);
            $table->unsignedBigInteger('user_id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('titulo')->nullable();
            $table->boolean('visible')->default(true);
            $table->text('mensaje')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')->on('foro_entradas')
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
        Schema::table('foro_entradas', function (Blueprint $table) {
            $table->dropForeign('foro_entradas_user_id_foreign');
            $table->dropForeign('foro_entradas_parent_id_foreign');
        });

        Schema::drop('foro_entradas');
    }
}
