<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTImg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_img', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('ImgOnlyId');
            $table->string('ImgUrl');
            $table->uuid('user_id');
            $table->integer('ImgType');   // 0头像  1博客封面  2用户背景
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
        Schema::dropIfExists('t_img');
    }
}
