<?php


namespace App\Services;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use App\Services\SocketIO\Packet;
use Illuminate\Support\Facades\Redis;

class WebSocketServiceTest implements WebSocketHandlerInterface
{

    public function __construct()
    {
    }

    public function onOpen(Server $server, Request $request)
    {
        // TODO: Implement onOpen() method.
        Log::info('WebSocket 连接建立');
        //Redis::ltrim('Rooms1',0,0);
        $redis = Redis::lrange ('Rooms1', 0, -1); //返回第0个至倒数第一个, 相当于返回所有元素
        var_dump($redis);
        //测试打开redis存储用户
        if (!in_array($request->fd,$redis))       // 限制重复用户
            Redis::rpush('Rooms1', $request->fd);  // 返回列表长度 1
        //$server->push($request->fd, $request->fd);
    }

    public function onMessage(Server $server, Frame $frame)
    {
        // TODO: Implement onMessage() method.
        //取Redis内的用户 在同时push多个用户
        //Redis::rpush('Rooms1', $frame->fd);  // 返回列表长度 1
        $redis = Redis::lrange ('Rooms1', 0, -1); //返回第0个至倒数第一个, 相当于返回所有元素
        foreach ($redis as $k =>$v){
            $server->push($v, $frame->data .'    '. date('Y-m-d H:i:s'));
        }

    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        // TODO: Implement onClose() method.
        // 删除相对应redis
        Redis::lrem('Rooms1', 1, $fd) ;
        Log::info('WebSocket 连接关闭');
    }
}
