<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    //绑定表格
    protected $table = 't_blog_tag';

    //绑定主键
    protected $primaryKey = 'id';
}
