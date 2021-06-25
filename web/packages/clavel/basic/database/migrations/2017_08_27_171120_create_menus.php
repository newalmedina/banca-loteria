<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->tinyInteger('primary')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->unsigned();

            $table->integer('item_type_id')->unsigned();
            $table->integer('page_id')->unsigned()->nullable();
            $table->string('target', 10)->nullable();
            $table->string('module_name')->nullable();
            $table->string('uri')->nullable();
            $table->tinyInteger('status')->default(0);

            /* Nested Sets */
            $table->integer('parent_id')->nullable();
            $table->integer('lft')->nullable();
            $table->integer('rgt')->nullable();

            $table->boolean('permission')->default(0);
            $table->string('permission_name')->nullable();

            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');

            $table->foreign('page_id')
                ->references('id')->on('pages')
                ->onDelete('cascade');
        });

        Schema::create('menu_item_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_item_id')->unsigned();
            $table->string('locale')->index();

            $table->string('title');
            $table->string('url')->nullable();
            $table->string('generate_url')->nullable();
            $table->unique(['menu_item_id', 'locale']);
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('menu_item_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('ico')->nullable();
            $table->timestamps();
        });

        Schema::table('menu_items', function ($table) {
            $table->foreign('item_type_id')->references('id')->on('menu_item_types')->onDelete('cascade');
        });


        Schema::create('menu_items_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_item_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->unique(['menu_item_id', 'role_id']);
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
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
        Schema::drop('menu_items_role');

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign('menu_items_item_type_id_foreign');
        });

        Schema::drop('menu_item_types');

        Schema::table('menu_item_translations', function (Blueprint $table) {
            $table->dropForeign('menu_item_translations_menu_item_id_foreign');
        });

        Schema::drop('menu_item_translations');

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign('menu_items_menu_id_foreign');
            $table->dropForeign('menu_items_page_id_foreign');
        });
        Schema::drop('menu_items');
        Schema::drop('menus');
    }
}
