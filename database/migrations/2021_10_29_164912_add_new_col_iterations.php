<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColIterations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('searches', function (Blueprint $table) {
            $table->integer('iterations_count')->after('search_engine')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('searches', function (Blueprint $table) {
            $table->dropColumn('iterations_count');
        });
    }
}
