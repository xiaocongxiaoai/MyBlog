<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogInfo extends Model
{
    //绑定表格
    protected $table = 't_blog_info';

    //绑定主键
    protected $primaryKey = 'id';
}
