<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Img extends Model
{
    //
    //绑定表格
    protected $table = 't_img';

    //绑定主键
    protected $primaryKey = 'id';
}
