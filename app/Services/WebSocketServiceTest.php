<?php


namespace App\Services;
use App\Jobs\ToSql;
use App\Jobs\ToSql2;
use App\Messagelog;
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
//        $redis = Redis::lrange ('Rooms1', 0, -1); //返回第0个至倒数第一个, 相当于返回所有元素
//        var_dump($redis);
//        //测试打开redis存储用户
//        if (!in_array($request->fd,$redis))       // 限制重复用户
//            Redis::rpush('Rooms1', $request->fd);  // 返回列表长度 1
        //$server->push($request->fd, '当前房间：');
    }

    public function onMessage(Server $server, Frame $frame)
    {
        $data = json_decode( $frame->data , true );
        //$server->push($frame->fd, $data['roomId']);
        //// TODO: Implement onMessage() method.
        if($data['Type']=="Join"){

            $this->JoinRoom($frame->fd,$data['roomId']);

            $server->push($frame->fd,json_encode(['msg'=>1,'data'=>'当前房间为：'.$data['roomId']],JSON_UNESCAPED_UNICODE));
            //假实现  用户加入房间后去数据库取数据  默认取最新30条聊天信息
            $zong = $this->GetInfo($data['roomId']);
            foreach ($zong as $k => $v){
                $server->push($frame->fd,json_encode(['msg'=>2,'data'=>$v->Data.$v->created_at],JSON_UNESCAPED_UNICODE));
            }
        }

        elseif($data['Type']=="Tack"){
            //取Redis内的用户 在同时push多个用户
            //Redis::rpush('Rooms1', $frame->fd);  // 返回列表长度 1
            $redis = Redis::lrange ($data['roomId'], 0, -1); //返回第0个至倒数第一个, 相当于返回所有元素
            ToSql2::dispatch($data);   //投送队列
            foreach ($redis as $k =>$v){
                $server->push($v, json_encode(['msg'=>2,'data'=>$data['Data'] .'    '. date('Y-m-d H:i:s')],JSON_UNESCAPED_UNICODE));
            }
        }
        else{
            $this->levelRoom($data['roomId'],$frame->fd);
        }
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        // TODO: Implement onClose() method.
        // 删除相对应redis
        Redis::lrem('Rooms1', 1, $fd) ;
        Log::info('WebSocket 连接关闭');
    }

    public function JoinRoom($Id,$data){
        $redis = Redis::lrange ($data, 0, -1); //返回第0个至倒数第一个, 相当于返回所有元素
        //测试打开redis存储用户
        if (!in_array($Id,$redis))       // 限制重复用户
            Redis::rpush($data, $Id);  // 返回列表长度 1
    }

    public function GetInfo($data){
        $count = Messagelog::all()->count();

        $info = Messagelog::where('RoomId','=',$data)
            ->offset($count>30?0:$count-30)->limit(30)
            ->orderBy('created_at','asc')->get(['Data','created_at']);
        return $info;
    }

    public function levelRoom($Id,$room){
        Redis::lrem($room, 1, $Id) ;
    }
}
