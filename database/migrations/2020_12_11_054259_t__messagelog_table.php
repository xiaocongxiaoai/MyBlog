<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TMessagelogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('t_messagelog', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('MessageOnlyId');
            $table->string('ImgUrl');
            $table->string('RoomId');
            $table->string('Data');
            $table->uuid('User');
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
        //
        Schema::dropIfExists('t_img');
    }
}
