<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudGeneratorTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crud_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('model');
            $table->string('model_plural')->nullable();
            $table->string('table_name')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('has_soft_deletes')->default(true);
            $table->boolean('has_api_crud')->default(true);
            $table->boolean('has_api_crud_secure')->default(false);
            $table->boolean('has_create_form')->default(true);
            $table->boolean('has_edit_form')->default(true);
            $table->boolean('has_show_form')->default(true);
            $table->boolean('has_delete_form')->default(true);
            $table->boolean('has_exports')->default(true);
            $table->integer('entries_page')->unsigned()->default(10);
            $table->string('order_by_field', 30)->nullable();
            $table->string('order_direction', 4)->default('ASC');
            $table->boolean('has_fake_data')->default(false);
            $table->timestamps();
        });


        Schema::create('crud_field_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug', 50)->unique();
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
        Schema::create('crud_module_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_list')->unsigned();
            $table->integer('order_create')->unsigned();
            $table->integer('crud_module_id')->unsigned();
            $table->string('field_type_slug', 50);
            $table->string('column_name')->nullable();
            $table->string('column_title')->nullable();
            $table->string('column_tooltip')->nullable();
            $table->boolean('in_list')->default(true);
            $table->boolean('in_create')->default(true);
            $table->boolean('in_edit')->default(true);
            $table->boolean('in_show')->default(true);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_multilang')->default(false);
            $table->boolean('can_modify')->default(true);
            $table->text('data')->nullable();

            $table->unsignedInteger('min_length')->nullable();
            $table->unsignedInteger('max_length')->nullable();
            $table->string('default_value')->nullable();

            $table->string('use_editor', 10)->nullable();


            $table->foreign('crud_module_id')->references('id')->on('crud_modules')->onDelete('cascade');
            $table->foreign('field_type_slug')->references('slug')->on('crud_field_types')->onDelete('cascade');
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
        Schema::dropIfExists('crud_module_fields');
        Schema::dropIfExists('crud_field_types');
        Schema::dropIfExists('crud_modules');
    }
}
