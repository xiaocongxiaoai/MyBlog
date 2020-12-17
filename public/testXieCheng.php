<?php
$server = new \Swoole\Http\Server('0.0.0.0', 9501);
$server->on('Request', function ($request, $response) {

    $channel = new \Swoole\Coroutine\Channel(3);    //定义管道 大小为3(类似于队列)

    go(function () use ($channel) {
        var_dump(time());

        $mysql = new Swoole\Coroutine\MySQL();
        $mysql->connect([
            'host' => '127.0.0.1',
            'user' => 'root',
            'password' => 'root',
            'database' => 'laravel58',
        ]);
        $result = $mysql->query('select sleep(3)');

        $channel->push($result);                //结果push到管道中
    });

    go(function () use ($channel) {
        var_dump(time());

        $redis1 = new Swoole\Coroutine\Redis();
        $redis1->connect('127.0.0.1', 6379);
        $result = $redis1->set('hello', 'world');

        $channel->push($result);
    });

    go(function () use ($channel) {
        var_dump(time());

        $redis2 = new Swoole\Coroutine\Redis();
        $redis2->connect('127.0.0.1', 6379);

        $result = $redis2->get('hello');
        $channel->push($result);
    });

    $results = [];
    for ($i = 0; $i < 3; $i++) {
        $results[] = $channel->pop();    //pop 元素移出管道
        var_dump($channel);
    }

    $response->end(json_encode([
        'data' => $results,
        'time' => time()
    ]));
});

$server->start();
