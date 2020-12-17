<?php


namespace App\Listeners;

use App\BlogTag;
use App\Events\TestEvent;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Hhxsv5\LaravelS\Swoole\Task\Listener;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class TestEventListener extends Listener
{

    protected $requst;
    /**
     * TestEventListener constructor.
     */
    public function handle(){

    }

//    public function handle(Event $event)
//    {
//        // TODO: Implement handle() method.
//        Log::info(__CLASS__ . ': 开始处理', [$event->getData()]);
//        sleep(3);// 模拟耗时代码的执行
//        Log::info(__CLASS__ . ': 处理完毕');
//
////        Log::info(__CLASS__ . ': 开始处理', [$event->getData()]);
//            //$this->requst = $event->getData();
////            $info = new BlogTag();
////          $info->content = '测试插入'.time();
////          sleep(3);
////          $info->tagOnlyId = Uuid::uuid1();
////          $info->save();
//
//
////        Log::info(__CLASS__ . ': 处理完毕');
//    }


}
