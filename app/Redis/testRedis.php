<?php


namespace App\Redis;
use Illuminate\Support\Facades\Redis;

class testRedis
{

    /**
     * testRedis constructor.
     */
    public function __construct()
    {
        Redis::get('name');
    }
}

$v = new testRedis();
