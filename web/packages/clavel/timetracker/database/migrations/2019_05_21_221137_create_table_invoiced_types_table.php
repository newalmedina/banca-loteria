<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInvoicedTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Estado de projectos
        Schema::create('invoiced_states', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('color', 10);
            $table->string('slug', 20)->index();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });

        Schema::create('invoiced_state_translations', function ($table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('invoiced_state_id');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['invoiced_state_id','locale']);
            $table->foreign('invoiced_state_id')->references('id')->on('invoiced_states')->onDelete('cascade');
        });

        $states = array(
            array(
                'id' => 0,
                'color' => '#FA4D46',
                'slug' => 'no-pagado'
            ),
            array(
                'id' => 1,
                'color' => '#F9C80E',
                'slug' => 'pagado'
            ),
            array(
                'id' => 2,
                'color' => '#59C3C3',
                'slug' => 'parcialmente-pagado'
            )
        );

        $states_translation = array(
            array(
                'id' => 0,
                'name' => 'Pte. pago',
                'description' => 'Pendiente de pago'
            ),
            array(
                'id' => 1,
                'name' => 'Pagado',
                'description' => 'Pagado'
            ),
            array(
                'id' => 2,
                'name' => 'Parcialmente pagado',
                'description' => 'Parcialmente pagado'
            )
        );

        for ($i=0; $i<sizeof($states_translation); $i++) {
            $state = array(
                'id' => $states[$i]['id'],
                'active' => true,
                'color' =>  $states[$i]['color'],
                'slug' =>  $states[$i]['slug'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );

            DB::table('invoiced_states')->insert($state);

            $state_translation = array(
                'id' => $states_translation[$i]['id'],
                'invoiced_state_id' => $states_translation[$i]['id'],
                'locale' => 'es',
                'name' => $states_translation[$i]['name'],
                'description' => $states_translation[$i]['description']
            );
            DB::table('invoiced_state_translations')->insert($state_translation);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoiced_state_translations');
        Schema::dropIfExists('invoiced_states');
    }
}
