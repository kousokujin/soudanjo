<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartEndTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quests_table', function (Blueprint $table) {
            //
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quests_table', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            //
        });
    }
}
