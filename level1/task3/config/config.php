<?php

return [
  'responseStatusByCode' => [
        '200' => 'ok',
        '400' => 'Bad Request',
        '404' => 'not found',
  ],
  'bodyByCode' => [
        '200' => '',
        '400' => 'not found',
        '404' => 'not found',
    ],
    'header' => [
        'Server:' => 'Server: Apache/2.2.14 (Win32)',
        'Content-Length:' => function(String $body):String{return "Content-Length: ".strval(strlen($body));},
        'Connection:' => 'Connection: Closed',
        'Content-Type: ' =>  'Content-Type: text/html; charset=utf-8'

    ]
];