<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //绑定表格
    protected $table = 't_comment';

    //绑定主键
    protected $primaryKey = 'id';
}
