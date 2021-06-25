<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTemplateBorders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletter_templates', function ($table) {
            $table->boolean('border')->after('title_font_color')->default(true);
            $table->string('border_color')->after('border')->default('#C0C0C0');
            $table->boolean('border_shadow')->after('border_color')->default(true);
            $table->boolean('resaltar_border')->after('border_shadow')->default(true);
            $table->string('resaltar_background_color')->after('resaltar_border')->default('#FFFFFF');
            $table->string('resaltar_border_color')->after('resaltar_background_color')->default('#c7c7c7');
            $table->boolean('resaltar_sombra')->after('resaltar_border_color')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletter_templates', function (Blueprint $table) {
            $table->dropColumn('border');
            $table->dropColumn('border_color');
            $table->dropColumn('border_shadow');
            $table->dropColumn('resaltar_border');
            $table->dropColumn('resaltar_background_color');
            $table->dropColumn('resaltar_sombra');
            $table->dropColumn('resaltar_border_color');
        });
    }
}
