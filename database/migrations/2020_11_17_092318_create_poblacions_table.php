<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoblacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('poblacions')) {
            Schema::create('poblacions', function (Blueprint $table) {
                $table->increments('id');

                $table->boolean('active')->default(0);
$table->string('name');
$table->text('description')->nullable();
$table->string('code');
$table->unsignedInteger('pais_id')->nullable();
$table->foreign('pais_id','pais_id_fk_966600')->references('id')->on('pais');


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
        
        Schema::dropIfExists('poblacions');
    }
}
