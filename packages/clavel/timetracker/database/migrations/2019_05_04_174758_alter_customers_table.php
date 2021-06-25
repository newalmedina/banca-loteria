<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function ($table) {
            $table->string('tax_id', 30)->nullable()->default(null);
            $table->string('color', 10)->nullable()->default(null);
            $table->text('description')->nullable()->default(null)->change();
            $table->text('address')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('tax_id');
            $table->dropColumn('color');
        });
    }
}
