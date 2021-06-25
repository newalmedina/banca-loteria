<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->datetime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->datetime("last_online_at")->nullable();
        });

        Schema::create('online_sessions', function(Blueprint $table)
        {
            $table->string('id')->unique();
            $table->text('payload')->nullable();
            $table->datetime('last_online_at')->useCurrent();
            $table->integer('user_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_sessions');

        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('last_login_at');
            $table->dropColumn('last_login_ip');
            $table->dropColumn('last_online_at');
        });
    }
}
