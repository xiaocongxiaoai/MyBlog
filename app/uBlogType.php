<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class uBlogType extends Model
{
    //绑定表格
    protected $table = 't_ublog_type';

    //绑定主键
    protected $primaryKey = 'id';
}
