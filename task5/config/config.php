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
        'Content-Type:' =>  'Content-Type: text/html; charset=utf-8'
    ],
    'protocolVersion' => 'HTTP/1.1',
    'userFoundHtml' => '<h1 style="color:green">FOUND</h1>',
    'userNotFoundHtml' => '<h1 style="color:red">NOT FOUND</h1>',
    'fileNameUserStorage' => './passwords.txt',
    'requiredHeaderName' => 'Host',
    'resolveHostValues' => [
        'student.shpp.me' => '/student',
        'another.shpp.me' => '/another'
    ],
    'testRequest4' => <<<REQ
POST /api/checkLoginAndPassword HTTP/1.1
Accept: */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/4.0
Content-Length: 35


login=student&password=12345
REQ
    ,
    'testRequest5' => <<<REQ
POST /api/checkLoginAndPassword/password.txt HTTP/1.1
Host: student.shpp.me
Accept: */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/4.0
Content-Length: 35


login=student&password=12345
REQ
    ,
    'testRequest5Another' => <<<REQ
POST /another/hey/file.txt HTTP/1.1
Host: another.shpp.me
Accept: */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/4.0
Content-Length: 35


login=student&password=12345
REQ
    ,
];