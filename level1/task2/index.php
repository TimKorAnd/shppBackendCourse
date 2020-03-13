<?php

// не обращайте на эту функцию внимания
// она нужна для того чтобы правильно считать входные данные
function readHttpLikeInput() {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toread = 0;
    while( $line = fgets( $f ) ) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/',$line,$m))
            $toread=$m[1]*1;
        if ($line == "\r\n")
            break;
    }
    if ($toread > 0)
        $store .= fread($f, $toread);
    return $store;
}

$contents = readHttpLikeInput();

function getHeadersFromTcpStringHttpRequest($headers){
        return array_map(function ($header){
            return explode(": ",$header);
        }, $headers);
}

function parseTcpStringAsHttpRequest($string) {
    //get array of each string of http request
    $httpRequestTcpStringsArray = explode("\n", $string);

    //get method, URI, HTTP version
    $httpRequestFirstStringParamsArray = explode(" ",$httpRequestTcpStringsArray[0]);

    //get headers array
    $headers = getHeadersFromTcpStringHttpRequest(array_slice($httpRequestTcpStringsArray, 1, -2));

    $body = end($httpRequestTcpStringsArray);

    return array(
        "method" => $httpRequestFirstStringParamsArray[0],
        "uri" => $httpRequestFirstStringParamsArray[1],
        "headers" => $headers,
        "body" => $body,
    );
}

$http = parseTcpStringAsHttpRequest($contents);
echo(json_encode($http, JSON_PRETTY_PRINT));
//echo("hello...");
