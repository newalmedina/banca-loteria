<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('informer')->nullable();
            $table->text('description')->nullable();
            $table->integer('issuetype_id')->unsigned()->default(0);
            $table->integer('priority')->unsigned()->default(0);
            $table->string('tags')->nullable();

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
        Schema::dropIfExists('issue');
    }
}
