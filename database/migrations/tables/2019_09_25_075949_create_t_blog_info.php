<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTBlogInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_blog_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('blogOnlyId');   //默认类型为string（36）
            $table->string('blogTitle',255);
            $table->text('blogContent');
            $table->string('blogTypeId',36);   //用uuid做表连接 与t_blogType->blogTypeOnlyId 关联
            $table->string('blogUserTypeId',36);   //同上 与表t_ublogType做关联
            $table->string('blogTag',255);
            $table->string('user_id',36);
            $table->integer('readNum')->default(0);  //阅读人数默认值为0
            $table->integer('likeNum')->default(0);  //点赞数默认为0
            $table->integer('isPublic')->default(1);  //是否公开  默认值为1 表示公开
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
        Schema::dropIfExists('t_blog_info');
    }
}
