<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogType extends Model
{
    //绑定表格
    protected $table = 't_blog_type';

    //绑定主键
    protected $primaryKey = 'id';
}
