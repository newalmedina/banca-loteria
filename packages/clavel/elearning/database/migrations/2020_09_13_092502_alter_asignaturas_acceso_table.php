<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAsignaturasAccesoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asignaturas', function (Blueprint $table) {
            $table->boolean('requiere_codigo')->default(false);
        });

        Schema::create('codigo_asignatura_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('codigo_id')->unsigned();
            $table->integer('asignatura_id')->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->string('codigo');

            $table->unique(['codigo_id','asignatura_id','user_id']);
            $table->foreign('codigo_id')
                ->references('id')
                ->on('codigos')
                ->onDelete('cascade');
            $table->foreign('asignatura_id')
                ->references('id')
                ->on('asignaturas')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
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
        Schema::table('codigo_asignatura_user', function (Blueprint $table) {
            $table->dropForeign(['asignatura_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::drop('codigo_asignatura_user');

        Schema::table('asignaturas', function (Blueprint $table) {
            $table->dropColumn('requiere_codigo');
        });
    }
}
