<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContenidosEvaluacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asignatura_convocatorias', function (Blueprint $table) {
            $table->integer('limite_finalizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('asignatura_convocatorias', function (Blueprint $table) {
            $table->dropColumn('limite_finalizacion');
        });
    }
}
