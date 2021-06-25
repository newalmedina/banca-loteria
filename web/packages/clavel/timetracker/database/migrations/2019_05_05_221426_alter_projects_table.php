<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Estado de projectos
        Schema::create('project_states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('color', 10);
            $table->string('slug', 10)->index();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });

        Schema::create('project_state_translations', function ($table) {
            $table->increments('id');
            $table->integer('project_state_id')->unsigned();
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['project_state_id','locale']);
            $table->foreign('project_state_id')->references('id')->on('project_states')->onDelete('cascade');
        });

        $states = array(
            array(
                'color' => '#FA4D46',
                'slug' => 'oferta'
            ),
            array(
                'color' => '#F9C80E',
                'slug' => 'pte'
            ),
            array(
                'color' => '#59C3C3',
                'slug' => 'en-curso'
            ),
            array(
                'color' => '#662E9B',
                'slug' => 'finalizado'
            ),
            array(
                'color' => '#E87EA1',
                'slug' => 'anulado'
            )
        );

        $states_translation = array(
            array(
                'name' => 'Ofertado',
                'description' => ''
            ),
            array(
                'name' => 'Pte. Inicio',
                'description' => ''
            ),
            array(
                'name' => 'En curso',
                'description' => ''
            ),
            array(
                'name' => 'Finalizado',
                'description' => ''
            ),
            array(
                'name' => 'Anulado',
                'description' => ''
            )
        );


        for ($i=0; $i<sizeof($states_translation); $i++) {
            $state = array(
                'active' => true,
                'color' =>  $states[$i]['color'],
                'slug' =>  $states[$i]['slug'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );

            $state_id = DB::table('project_states')->insertGetId($state);

            $state_translation = array(
                'project_state_id' => $state_id,
                'locale' => 'es',
                'name' => $states_translation[$i]['name'],
                'description' => $states_translation[$i]['description']
            );
            DB::table('project_state_translations')->insert($state_translation);
        }


        Schema::table('projects', function ($table) {
            $table->unsignedInteger('customer_final_id')->nullable()->default(null);
            $table->double('vat')->nullable()->default(null);
            $table->double('total')->nullable()->default(null);
            $table->string('slug_state', 10)->default('en-curso')->index();
            $table->text('bill_info')->nullable()->default(null);
            $table->boolean('invoiced')->default(false);

            $table->foreign('customer_final_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('slug_state')
                ->references('slug')
                ->on('project_states')
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
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['customer_final_id']);
            $table->dropForeign(['slug_state']);

            $table->dropColumn('customer_final_id');
            $table->dropColumn('vat');
            $table->dropColumn('total');
            $table->dropColumn('slug_state');
            $table->dropColumn('bill_info');
            $table->dropColumn('invoiced');
        });

        Schema::dropIfExists('project_state_translations');
        Schema::dropIfExists('project_states');
    }
}
