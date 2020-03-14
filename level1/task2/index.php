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
/**
 * @param array $httpRequestTcpLinesArray
 * @param bool $isBodyPresent if true than explode headers wo 2 last lines/elements
 * @return array of headers array [k => v]
 */
function getHeadersFromHTTPRequest(array $httpRequestTcpLinesArray, bool $isBodyPresent): array {
    $bodyLinesNum = $isBodyPresent ? 2 : 0;
    $lastHeaderIndex = count($httpRequestTcpLinesArray) - $bodyLinesNum - 1;
    $headers = array_slice($httpRequestTcpLinesArray, 1, $lastHeaderIndex);
        return array_map(function ($header){
            return explode(": ",$header);
        }, $headers);
}

/**
 * checking for headers
 * @param array $httpRequestTcpLinesArray array of each line of HTTP request
 * @return bool true if one or more headers presents in request, otherwise false
 */
function isHeaderPresent(array $httpRequestTcpLinesArray):bool
{
    return count($httpRequestTcpLinesArray) > 1 && $httpRequestTcpLinesArray[1];
}

/**
 * checking for body in HTTP request
 * @param array $httpRequestTcpLinesArray  array of each line of HTTP request
 * @return bool true if body present in request, otherwise false
 */
function isBodyPresent(array $httpRequestTcpLinesArray):bool
{
    return ($arrLength = count($httpRequestTcpLinesArray)) > 2 && !$httpRequestTcpLinesArray[$arrLength - 2];
}

/**
 * @param $string
 * @return array
 */
function parseTcpStringAsHttpRequest($string) {
    //get array of each string of HTTP request
    $httpRequestTcpLinesArray = explode("\n", $string);

    //get array of fields contained in first string of HTTP request: [method, URI path, HTTP/protocol version]
    $httpRequestFirstLineParamsArray = explode(" ",$httpRequestTcpLinesArray[0]);

    //get body
    $body = '';
    $isBodyPresent = isBodyPresent($httpRequestTcpLinesArray);
    if ($isBodyPresent){
        $body = end($httpRequestTcpLinesArray);
    }

    //get headers array
    $headers = [];
    if (isHeaderPresent($httpRequestTcpLinesArray)){
        $headers = getHeadersFromHTTPRequest($httpRequestTcpLinesArray, $isBodyPresent);
    }



    return array(
        "method" => $httpRequestFirstLineParamsArray[0],
        "uri" => $httpRequestFirstLineParamsArray[1],
        "headers" => $headers,
        "body" => $body,
    );
}

/**
 * @param array $httpRequestTcpStringsArray
 * @return bool
 */


$http = parseTcpStringAsHttpRequest($contents);
echo(json_encode($http, JSON_PRETTY_PRINT));
//echo("hello...");
