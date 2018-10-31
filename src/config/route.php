<?php

global $route_config;
$route_config = [
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
];
