<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNewsFontResumen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_translations', function ($table) {
            $table->text('resumen')->after('body')->nullable();
            $table->string('fuente')->after('resumen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_translations', function (Blueprint $table) {
            $table->dropColumn('fuente');
            $table->dropColumn('resumen');
        });
    }
}
