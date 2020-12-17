<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messagelog extends Model
{
    //
    //
    //绑定表格
    protected $table = 't_messagelog';

    //绑定主键
    protected $primaryKey = 'id';
}
