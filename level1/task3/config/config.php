<?php

return [
  'responseCode' => [
    '404' => <<<CODE_404
HTTP/1.1 404 Not Found
Date: {$fn(time())}
Server: Apache/2.2.14 (Win32)
Content-Length: 9
Connection: Closed
Content-Type: text/html; charset=utf-8

not found
CODE_404
    ,
      '200' => ""

  ]
];