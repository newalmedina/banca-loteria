<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('gender', array('male', 'female'))->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('user_lang', 6)->default('en');
            $table->boolean('confirmed')->default(false);
            $table->timestamps();

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

        Schema::table('user_profiles', function(Blueprint $table)
        {
            $table->dropForeign('user_profiles_user_id_foreign');
        });

        Schema::drop('user_profiles');
    }
}
