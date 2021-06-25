<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTransferTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ft_bundles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bundle_key', 50)->unique();
            $table->string('view_auth', 50);
            $table->string('delete_auth', 50);
            $table->bigInteger('fullsize')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });


        Schema::create('ft_bundle_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bundle_id')->unsigned();
            $table->string('original');
            $table->string('filename');
            $table->string('fullpath');
            $table->bigInteger('filesize')->default(0);

            $table->timestamps();

            $table->foreign('bundle_id')->references('id')->on('ft_bundles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ft_bundle_files');
        Schema::dropIfExists('ft_bundles');
    }
}
