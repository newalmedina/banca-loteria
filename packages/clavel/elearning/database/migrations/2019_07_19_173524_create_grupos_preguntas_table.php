<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGruposPreguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_preguntas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contenido_id')->unsigned();
            $table->string('titulo', 20);
            $table->string('color', 10);
            $table->timestamps();

            $table->foreign("contenido_id")->references("id")->on("contenidos")->onDelete("cascade");
        });


        Schema::table('preguntas', function (Blueprint $table) {
            $table->integer('grupo_pregunta_id')->unsigned()->default(0);
        });

        Schema::table('contenidos_evaluacion', function (Blueprint $table) {
            $table->boolean("grupos_preguntas")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contenidos_evaluacion', function (Blueprint $table) {
            $table->dropColumn('grupos_preguntas');
        });

        Schema::table('preguntas', function (Blueprint $table) {
            $table->dropColumn('grupo_pregunta_id');
        });

        Schema::dropIfExists('grupos_preguntas');
    }
}
