<?php
return [
  respond => [
    '404' => <<<CODE_404
HTTP/1.1 404 Not Found
{${getdate()}}
Date: Sun, 18 Oct 2012 10:36:20 GMT -- тут текущее время
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