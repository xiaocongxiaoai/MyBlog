<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    //绑定表格
    protected $table = 't_user_action';

    //绑定主键
    protected $primaryKey = 'id';
}
