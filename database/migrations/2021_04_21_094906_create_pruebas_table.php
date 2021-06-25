<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatepruebasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pruebas')) {
            Schema::create('pruebas', function (Blueprint $table) {
                $table->increments('id');

                $table->boolean('active')->default(0);
$table->string('name');
$table->text('description')->nullable();


                $table->timestamps();

                
            });
        }

        

        


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::dropIfExists('pruebas');
    }
}
