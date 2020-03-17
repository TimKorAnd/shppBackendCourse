<?php

return [
  'responseStatusMessageByCode' => [
        '200' => 'OK',
        '400' => 'Bad Request',
        '404' => 'Not Found',
        '500' => 'Internal Server Error',
  ],
  'bodyByCode' => [
        '200' => '',
        '400' => 'not found',
        '404' => 'not found',
        '500' => 'not found',
    ],
    'headers' => [
      'Date:' => function(){
          return 'Date: '.date('r e',time());
      },
        'Server:' => 'Server: Apache/2.2.14 (Win32)',
        'Content-Length:' => function(String $body):String{return 'Content-Length: '.strval(strlen($body));},
        'Connection:' => 'Connection: Closed',
        'Content-Type: ' =>  'Content-Type: text/html; charset=utf-8'
    ],
    'protocolVersion' => 'HTTP/1.1',
    'userFoundHtml' => '<h1 style="color:green">FOUND</h1>',
    'userNotFoundHtml' => '<h1 style="color:red">NOT FOUND</h1>',
    'fileNameUserStorage' => './passwords.txt'
];