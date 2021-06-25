<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsletterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->default('');
            $table->string('subject', 255)->default('');
            $table->boolean('generated')->default(false);
            $table->timestamps();
        });

        Schema::create('newsletter_rows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('newsletter_id')->unsigned();
            $table->integer('cols')->unsigned();
            $table->integer('position')->unsigned();
            $table->timestamps();

            $table->foreign('newsletter_id')->references('id')->on('newsletters')->onDelete('cascade');
        });

        Schema::create('newsletter_row_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('newsletter_row_id')->unsigned();
            $table->integer('post_id')->unsigned()->nullable();
            $table->enum('type', array('post', 'text'))->nullable();
            $table->integer('position')->unsigned();
            $table->char('image_position')->default('t');
            $table->string('title_color')->default('');
            $table->string('text_color')->default('');
            $table->string('image_border')->default('');
            $table->integer('text_length')->nullable();
            $table->boolean('complete_post')->default(false);
            $table->timestamps();

            $table->foreign('newsletter_row_id')->references('id')->on('newsletter_rows')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('newsletter_row_fields_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('newsletter_row_field_id')->unsigned();
            $table->string('locale')->index();
            $table->text('body')->nullable();

            $table->foreign('newsletter_row_field_id', 'news_row_id_trans_foreign')->references('id')->on('newsletter_row_fields')->onDelete('cascade');
        });

        Schema::create('newsletter_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('slug')->unique()->index();
            $table->boolean('requires_opt_in')->default(true);
            $table->timestamps();
        });

        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('list_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->boolean('opted_in')->default(false);
            $table->timestamp('opted_in_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('list_id')
                ->references('id')->on('newsletter_lists')
                ->onDelete('cascade');
        });

        Schema::create('newsletter_campaign_states', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name', 100)->default('');
            $table->string('class', 100)->default('');
            $table->integer('code')->unsigned();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });


        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('list_id')->index()->nullable();
            $table->unsignedInteger('newsletter_id')->index()->nullable();
            $table->string('name');
            $table->boolean('is_scheduled')->default(false);
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('sent_count')->nullable();
            $table->unsignedInteger('newsletter_campaign_state_id')->index()->nullable();

            $table->timestamps();

            $table->foreign('list_id')
                ->references('id')->on('newsletter_lists')
                ->onDelete('cascade');

            $table->foreign('newsletter_id')
                ->references('id')->on('newsletters')
                ->onDelete('cascade');


            $table->foreign('newsletter_campaign_state_id')
                ->references('id')
                ->on('newsletter_campaign_states')
                ->onDelete('cascade');
        });

        Schema::create('newsletter_campaign_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id')->index()->nullable();
            $table->unsignedInteger('user_id')->index();
            $table->boolean('is_sent')->default(false);
            $table->string('sent_result')->default('');
            $table->timestamps();

            $table->foreign('campaign_id')
                ->references('id')->on('newsletter_campaigns')
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
        Schema::drop('newsletter_campaign_recipients');
        Schema::drop('newsletter_campaigns');
        Schema::drop('newsletter_campaign_states');
        Schema::drop('newsletter_subscriptions');
        Schema::drop('newsletter_lists');
        Schema::drop('newsletter_row_fields_translations');
        Schema::drop('newsletter_row_fields');
        Schema::drop('newsletter_rows');
        Schema::drop('newsletters');
    }
}
