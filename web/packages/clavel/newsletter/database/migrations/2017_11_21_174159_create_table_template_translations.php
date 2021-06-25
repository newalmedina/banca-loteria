<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTemplateTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletters', function ($table) {
            $table->longText('custom_header')->after('generated')->nullable();
            $table->longText('custom_footer')->after('custom_header')->nullable();
        });

        Schema::create('newsletter_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('newsletter_id')->unsigned();
            $table->string('locale')->index();
            $table->string('subject')->nullable();

            $table->foreign('newsletter_id', 'news_id_trans_foreign')->references('id')->on('newsletters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletters', function ($table) {
            $table->dropColumn('custom_header');
            $table->dropColumn('custom_footer');
        });
        Schema::drop('newsletter_translations');
    }
}
