<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaingPivotLista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletter_campaigns', function ($table) {
            $table->dropForeign('newsletter_campaigns_list_id_foreign');
            $table->dropColumn('list_id');
        });

        Schema::create('newsletter_campaign_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id')->unsigned();
            $table->integer('list_id')->unsigned();

            $table->unique(['campaign_id', 'list_id']);
            $table->foreign('campaign_id')->references('id')->on('newsletter_campaigns')->onDelete('cascade');
            $table->foreign('list_id')->references('id')->on('newsletter_lists')->onDelete('cascade');
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
        Schema::drop('newsletter_campaign_list');
    }
}
