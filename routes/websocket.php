<?php
use Swoole\Http\Request;
use App\Services\WebSocket;
use App\Services\Facades\Websocket as WebsocketProxy;
use Swoole\WebSocket\Frame;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Count;
/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/

WebsocketProxy::on('connect', function (WebSocket $websocket, Request $request) {
    // 发送欢迎信息
    $websocket->setSender($request->fd);
    $websocket->emit('connect', '欢迎访问聊天室');
});

WebsocketProxy::on('disconnect', function (WebSocket $websocket, $data) {
    // called while socket on disconnect
    //$websocket->emit('disconnect', '亚谁了类！');
    roomout($websocket, $data);
});

WebsocketProxy::on('login', function (WebSocket $websocket, $data) {

    if (!empty($data['token']) && ($user = \App\User::where('api_token', $data['token'])->first())) {
        $websocket->loginUsing($user);
        // 获取未读消息

        $rooms = [];

        foreach (\App\Count::$ROOMLIST as $roomid) {
            // 循环所有房间
            $result = \App\Count::where('user_id', $user->userOnlyId)->where('room_id', $roomid)->first();
            $roomid = 'room' . $roomid;
            if ($result) {
                $rooms[$roomid] = $result->count;
            } else {
                $rooms[$roomid] = 0;
            }
        }
        $websocket->toUser($user)->emit('connect', $rooms);
    } else {
        $websocket->emit('message', '登录后才能进入聊天室');
    }
});

WebsocketProxy::on('message',function (WebSocket $websocket, $data){

    $websocket->emit('message', $data);
    $websocket->emit('message', '亚谁了类！');
});

WebsocketProxy::on('room', function (WebSocket $websocket, $data) {
    if (!empty($data['token']) && ($user = \App\User::where('api_token', $data['token'])->first())) {
        // 从请求数据中获取房间ID
        if (empty($data['roomid'])) {
            return;
        }

        $roomId = $data['roomid'];
        // 重置用户与fd关联


        Redis::command('hset', ['socket_id', $user->userOnlyId, $websocket->getSender()]);
        // 将该房间下用户未读消息清零
        $count = Count::where('user_id', $user->userOnlyId)->where('room_id', $roomId)->first();
        $count->count = 0;
        $count->save();

        // 将用户加入指定房间
        $websocket->join($roomId);
        // 打印日志
        Log::info($user->name . '进入房间：' . $roomId);
        $websocket->to($roomId)->emit('room', $user->name . '进入房间：' . $roomId);
        // 更新在线用户信息
        $roomUsersKey = 'online_users_' . $roomId;
        $onelineUsers = Cache::get($roomUsersKey);
        //$user->src = $user->avatar;
        if ($onelineUsers) {
            $onelineUsers[$user->userOnlyId] = $user;
            Cache::forever($roomUsersKey, $onelineUsers);
        } else {
            $onelineUsers = [
                $user->userOnlyId => $user
            ];
            Cache::forever($roomUsersKey, $onelineUsers);
        }
        // 广播消息给房间内所有用户

        $websocket->to($roomId)->emit('room', $onelineUsers);

    } else {
        $websocket->emit('message', '登录后才能进入聊天室');
    }
});

WebsocketProxy::on('roomout', function (WebSocket $websocket, $data) {
    roomout($websocket, $data);
});

function roomout(WebSocket $websocket, $data) {

    if (!empty($data['api_token']) && ($user = \App\User::where('api_token', $data['api_token'])->first())) {

        if (empty($data['roomid'])) {
            return;
        }

        $roomId = $data['roomid'];



        $websocket->leave([$roomId]);
        // 更新在线用户信息
        $roomUsersKey = 'online_users_' . $roomId;
        $onelineUsers = Cache::get($roomUsersKey);
        if (!empty($onelineUsers[$user->userOnlyId])) {
            unset($onelineUsers[$user->userOnlyId]);
            Cache::forever($roomUsersKey, $onelineUsers);
        }

        //dd($websocket->to($roomId));
        $websocket->to($roomId)->emit('roomout',$user->name . '退出房间: ' . $roomId);
        $websocket->to($roomId)->emit('roomout', $onelineUsers);
        $websocket->emit('roomout', '你已经退出房间！');
        //$websocket->to($room)->emit('roomout', $onelineUsers);
        Log::info($user->name . '退出房间: ' . $roomId);

    } else {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
}
