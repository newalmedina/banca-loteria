<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTablesTimeTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Clientes
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->text('description');
            $table->string('company')->nullable();
            $table->string('contact')->nullable();
            $table->text('address')->nullable();
            $table->string('country', 2);
            $table->string('currency', 3);
            $table->string('phone', 100)->nullable();
            $table->string('fax', 100)->nullable();
            $table->string('mobile', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('homepage')->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('active')->default(0);
            $table->double('fixed_rate')->nullable();
            $table->double('hourly_rate')->nullable();
            $table->timestamps();
        });


        // Proyectos
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('name');
            $table->string('order_number')->nullable();
            $table->string('customer_number')->nullable();
            $table->string('budget_number')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(0);
            $table->double('budget')->nullable();
            $table->double('fixed_rate')->nullable();
            $table->double('hourly_rate')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });

        // Actividades
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(0);
            $table->double('fixed_rate')->nullable();
            $table->double('hourly_rate')->nullable();
            $table->timestamps();
        });

        // Mis tiempos
        Schema::create('timesheet', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->integer('project_id')->unsigned()->index();
            $table->integer('activity_id')->unsigned()->index();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('duration')->nullable();
            $table->string('timezone')->nullable();
            $table->text('description');
            $table->double('rate')->nullable();
            $table->double('fixed_rate')->nullable();
            $table->double('hourly_rate')->nullable();
            $table->boolean('exported')->default(0);
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheet');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('customers');
    }
}
