<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserProfilesAdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_especialidad', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('nif')->nullable()->after("last_name");
            $table->integer('provincia_id')->unsigned()->nullable()->after("nif");
            $table->integer('municipio_id')->unsigned()->nullable()->after("provincia_id");
            $table->string('centro')->nullable()->after("municipio_id");
            $table->integer('especialidad_id')->nullable()->after("centro");
            $table->string('especialidad_otra')->nullable()->after("especialidad_id");
            $table->boolean('consentimiento')->default(false)->after("confirmed");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_especialidad');

        Schema::table("user_profiles", function (Blueprint $table) {
            $table->dropColumn('nif');
            $table->dropColumn('provincia_id');
            $table->dropColumn('municipio_id');
            $table->dropColumn('centro');
            $table->dropColumn('especialidad_id');
            $table->dropColumn('especialidad_otra');
            $table->dropColumn('consentimiento');
        });
    }
}
