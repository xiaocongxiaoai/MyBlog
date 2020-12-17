<?php
// 表明服务器启动后监听本地 9051 端口

$server = new \Swoole\Http\Server('0.0.0.0', 9501);

// 服务器启动时返回响应
//$server->on("start", function ($server) {
//    //echo "Swoole http server is started at http://0.0.0.0:9501\n";
//    //shell_exec('netstat -apn | grep 9501');
//});

// 向服务器发送请求时返回响应
// 可以获取请求参数，也可以设置响应头和响应内容
//$server->on("request", function ($request, $response) {
//    $response->header("Content-Type", "text/plain");
//    $response->end("Hello World\n");
//});

$server->on('Request', function ($request, $response) {

    var_dump(time());

    $mysql = new Swoole\Coroutine\MySQL();
    $mysql->connect([
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '123456',
        'database' => 'MyBlog',
    ]);
    $mysql->setDefer();
    $mysql->query('select *From t_user');    //查询query

    var_dump(time());

    $redis1 = new Swoole\Coroutine\Redis();
    $redis1->connect('127.0.0.1', 6379);
    $redis1->setDefer(true);
    $redis1->set('hello', 'world');

    var_dump(time());

    $redis2 = new Swoole\Coroutine\Redis();
    $redis2->connect('127.0.0.1', 6379);
    $redis2->setDefer(true);
    $redis2->get('hello');


    $redis1->recv();              //接收redis1的运行结果  否則無法執行語句
    $result1 = $mysql->recv();    //接收运行结果
    $result2 = $redis2->recv();   //接收运行结果

    var_dump($result1, $result2, time());

    $response->end('Request Finish: ' . time());
});

// 启动 HTTP 服务器
$server->start();


