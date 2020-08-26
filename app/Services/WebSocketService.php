<?php


namespace App\Services;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use App\Services\SocketIO\Packet;
class WebSocketService implements WebSocketHandlerInterface
{
    //创建一个实现了WebSocketHandlerInterface接口的类
    public function __construct()
    {

        $this->websocket = app('swoole.websocket');
        $this->parser = app('swoole.parser');
    }

    //建立时触发
    public function onOpen(Server $server, Request $request)
    {
        // 在触发 WebSocket 连接建立事件之前，Laravel 应用初始化的生命周期已经结束，你可以在这里获取 Laravel 请求和会话数据
        // 调用 push 方法向客户端推送数据，fd 是客户端连接标识字段
        // TODO: Implement onOpen() method.
//        Log::info('WebSocket 连接建立');
////        echo "server: handshake success with fd{$request->fd}\n";
////        $server->push($request->fd, '欢迎来到图灵机器人！');
///
        // 如果未建立连接，先建立连接
        if (!request()->input('sid')) {
            // 初始化连接信息 socket.io-client
            $payload = json_encode([
                'sid' => base64_encode(uniqid()),
                'upgrades' => [],
                'pingInterval' => config('laravels.swoole.heartbeat_idle_time') * 1000,
                'pingTimeout' => config('laravels.swoole.heartbeat_check_interval') * 1000,
            ]);
            $initPayload = Packet::OPEN . $payload;
            $connectPayload = Packet::MESSAGE . Packet::CONNECT;
            $server->push($request->fd, $initPayload);
            $server->push($request->fd, $connectPayload);
        }

        echo 'WebSocket 连接建立:'. $request->fd .";\n";
        Log::info('WebSocket 连接建立:' . $request->fd);
        if ($this->websocket->eventExists('connect')) {
            $this->websocket->call('connect', $request);
        }
        //$this->websocket->call('room',$request);

    }

    //收到信息时触发
    public function onMessage(Server $server, Frame $frame)
    {
        // TODO: Implement onMessage() method.
        // 调用 push 方法向客户端推送数据
//        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//        $server->push($frame->fd, "{$frame->data}  by xcc" . date('Y-m-d H:i:s'));
//        $key = config('services.turingapi.key');
//        $url = config('services.turingapi.url');
////        $reqType = 0;
////        $perception = json_encode(['inputText'=>json_encode(['text'=>$frame->data],JSON_UNESCAPED_UNICODE)],JSON_UNESCAPED_UNICODE);
////        $userInfo = json_encode(['apiKey'=>$key,'userId'=>'demo'],JSON_UNESCAPED_UNICODE);
//        $client = new \GuzzleHttp\Client();
//
//        $response = $client->request('POST', $url, [
//            'json' =>[
//                "reqType"=>0,
//                "perception"=> [
//                    "inputText"=> [
//                        "text"=> "{$frame->data}"
//                    ]
//                ],
//                "userInfo"=>[
//                    "apiKey"=> $key,
//                    "userId"=> "demo"
//                ]
//            ]
//        ]);
//        $returninfo = json_decode($response->getBody());
//
//      // dd($returninfo->results[0]->values->text);
//
//        $server->push($frame->fd, $returninfo->results[0]->values->text."    by 小葱小爱" . date('Y-m-d H:i:s'));

        Log::info("从 {$frame->fd} 接收到的数据: {$frame->data}");
        if ($this->parser->execute($server, $frame)) {
            return;
        }
        $payload = $this->parser->decode($frame);
        ['event' => $event, 'data' => $data] = $payload;
        //dd($event);
        $this->websocket->reset(true)->setSender($frame->fd);
        if ($this->websocket->eventExists($event)) {
            $this->websocket->call($event, $data);
        } else {
            // 兜底处理，一般不会执行到这里
            return;
        }
    }
    // 关闭连接时触发
    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('WebSocket 连接关闭:' . $fd);
        echo 'WebSocket 连接关闭:' . $fd;
        $this->websocket->setSender($fd);
        if ($this->websocket->eventExists('disconnect')) {
            $this->websocket->call('disconnect', '连接关闭');
        }
    }
}
