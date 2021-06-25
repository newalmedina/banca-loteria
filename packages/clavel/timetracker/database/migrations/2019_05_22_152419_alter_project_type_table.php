<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Estado de projectos
        Schema::create('project_types', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('color', 10);
            $table->string('slug', 20)->index();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });

        Schema::create('project_type_translations', function ($table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('project_type_id');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['project_type_id','locale']);
            $table->foreign('project_type_id')->references('id')->on('project_types')->onDelete('cascade');
        });

        $types = array(
            array(
                'id' => 0,
                'color' => '#73D67F',
                'slug' => 'proyecto'
            ),
            array(
                'id' => 1,
                'color' => '#FFD23F',
                'slug' => 'saas'
            ),
            array(
                'id' => 2,
                'color' => '#1F487E',
                'slug' => 'bono'
            ),
            array(
                'id' => 3,
                'color' => '#FF7733',
                'slug' => 'body-shopping'
            )
        );

        $types_translation = array(
            array(
                'id' => 0,
                'name' => 'Proyecto',
                'description' => 'Proyecto'
            ),
            array(
                'id' => 1,
                'name' => 'Sass',
                'description' => 'Sass'
            ),
            array(
                'id' => 2,
                'name' => 'Bono horas',
                'description' => 'Bono horas'
            ),
            array(
                'id' => 3,
                'name' => 'Body shopping',
                'description' => 'Desarrollo por horas mensuales'
            )
        );

        for ($i=0; $i<sizeof($types_translation); $i++) {
            $state = array(
                'id' => $types[$i]['id'],
                'active' => true,
                'color' =>  $types[$i]['color'],
                'slug' =>  $types[$i]['slug'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );

            DB::table('project_types')->insert($state);

            $state_translation = array(
                'id' => $types_translation[$i]['id'],
                'project_type_id' => $types_translation[$i]['id'],
                'locale' => 'es',
                'name' => $types_translation[$i]['name'],
                'description' => $types_translation[$i]['description']
            );
            DB::table('project_type_translations')->insert($state_translation);
        }

        Schema::table('projects', function ($table) {
            $table->unsignedInteger('project_type_id')->default(0);
            $table->dateTime('expire_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->string('invoice_number')->nullable();

            $table->foreign('project_type_id')
                ->references('id')
                ->on('project_types')
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
            $table->dropForeign(['project_type_id']);

            $table->dropColumn('project_type_id');
            $table->dropColumn('expire_at');
            $table->dropColumn('closed_at');
            $table->dropColumn('invoice_number');
        });

        Schema::dropIfExists('project_type_translations');
        Schema::dropIfExists('project_types');
    }
}
