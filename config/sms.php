<?php
return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,
    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
        // 默认可用的发送网关
        'gateways' => [
//            //云片
//            'yunpian',
            //腾讯云
            'Ten'
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
//        'yunpian' => [
//            'api_key' => '云片短信平台账户api_key',
//        ],
        'Ten' => [
            'sdk_app_id' => env('TEN_SDK_KEY'),
            'app_key' => env('TEN_API_KEY'),
            'sign_name' => ''  //(此处可设置为空，默认签名)
            ],
     ],
];
