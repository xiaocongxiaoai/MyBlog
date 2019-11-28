<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('userOnlyId');
            $table->string('name',255);
            $table->string('email',255)->unique()->nullable();
            $table->string('password',255);
            $table->string('summary',255)->nullable();
            $table->string('phoneNum',255)->nullable();
            $table->integer('isPublic')->default(0);
            $table->string('api_token',255);
            $table->integer('role')->default(0);
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
        Schema::dropIfExists('t_user');
    }
}
