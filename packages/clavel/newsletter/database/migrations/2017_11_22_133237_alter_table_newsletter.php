<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNewsletter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletters', function ($table) {
            $table->integer('template_id')->unsigned()->nullable();
            $table->foreign('template_id')->references('id')->on('newsletter_templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn('template_id');
        });
    }
}
