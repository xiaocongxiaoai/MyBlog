<?php


namespace App\Events;


use App\Listeners\TestEventListener;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class TestEvent extends Event
{

    protected $listeners = [
        // 监听器列表
        TestEventListener::class,
        // TestListener2::class,
    ];

    protected $data;

    /**
     * TestEvent constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }



}
