<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_comment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('commentOnlyId');
            $table->string('userId',36);   //评论者
            $table->string('content',1000);//评论内容
            $table->string('blogId',36);   //评论的文章
            $table->integer('likeNum')->default(0);           //评论点赞人数
            $table->integer('noLikeNum')->default(0);         //
            $table->integer('IsHide')->default(0);            //是否隐藏 默认0 表示不隐藏
            $table->integer('IsLiked')->default(0);           //这条评论是否被当前用户点赞
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
        Schema::dropIfExists('t_conmmernt');
    }
}
