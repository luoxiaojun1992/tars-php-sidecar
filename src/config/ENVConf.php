<?php

namespace HttpServer\config;

class ENVConf
{
    private static $config = [
        'route' => [
            'tars_mysql8:50001' => [
                '/' => [
                    'host' => 'http://www.baidu.com',
                    'timeout' => 5,
                ],
                '*' => [
                    'host' => 'http://www.baidu.com',
                    'timeout' => 5,
                ],
            ],
        ]
    ];

    public static function get($configName)
    {
        return isset(self::$config[$configName]) ? self::$config[$configName] : null;
    }
}
