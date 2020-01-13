<?php
namespace Swoole;

// 监听本地 9503 端口，等待客户端请求
$server = new Server("0.0.0.0", 9503);
// 建立连接时输出
$server->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});
// 接收消息时返回内容
$server->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});
// 连接关闭时输出
$server->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
// 启动 TCP 服务器
$server->start();
