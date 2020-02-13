<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Members extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('members',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('userid')->nullable();
            $table->ipAddress('ip');
            $table->integer('quest_id');
            $table->enum('main_class',['Hu','Fi','Ra','Gu','Fo','Te','Br','Bo','Su','Hr','Ph','Et','none']);
            $table->enum('sub_class',['Hu','Fi','Ra','Gu','Fo','Te','Br','Bo','Su','Hr','Ph','Et','none']);
            $table->text('comment');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
