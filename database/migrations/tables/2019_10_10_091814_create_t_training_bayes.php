<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTTrainingBayes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_training_bayes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('blogInfo_Id');   //默认类型为string（36）
            $table->integer('isSuspicious')->default(0); //是否带有侮辱性;
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
        Schema::dropIfExists('t_training_bayes');
    }
}
