<?php
    date_default_timezone_set("GMT");
    $config = include_once ("./config/config.php");

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

const HTTP_REQUEST_BODY_LINES_NUMBER = 2; //TODO remove in config or anothet constatnts host
/**
 * @param array $httpRequestTcpLinesArray
 * @param bool $isBodyPresent if true than explode headers wo 2 last lines/elements
 * @return array of headers array [k => v]
 */
function getHeadersFromHTTPRequest(array $httpRequestTcpLinesArray, bool $isBodyPresent): array {
    //
    $bodyLinesNum = $isBodyPresent ? HTTP_REQUEST_BODY_LINES_NUMBER : 0;
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

    //get array of fields contained in first - start string of HTTP request: [method, URI path, HTTP/protocol version]
    $httpRequestStartLineParamsArray = explode(" ",$httpRequestTcpLinesArray[0]);

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
        "method" => $httpRequestStartLineParamsArray[0],
        "uri" => $httpRequestStartLineParamsArray[1],
        "headers" => $headers,
        "body" => $body,
    );
}


    function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {
//        ...
        global $config;
       echo $config['protocolVersion']." ".$statuscode." ".$statusmessage."\n".$headers."\n".$body;
}

    /**
     * @param array $headers
     * @param String $headerName
     * @return String
     */
    function getHeaderValue(array $headers, String $headerName): String{
        foreach($headers as $header){
            if(preg_match("/^".$headerName."$/i", $header[0])){
                return $header[1];
            }
        }
        return '';
    }

    function getPathFromResolveHosts($hostHeaderValue, $resolveHostValues){
        foreach($resolveHostValues as $resolveHostValue => $path){
            if (preg_match("/^".$resolveHostValue.".*/i", $hostHeaderValue)){
                return($path);
            }
        }
        return '';
    }

    /** get response status code in dependency of request parameters
     * @param String $method
     * @param String $uri
     * @return String status code
     */
    function    getResponseStatusCode(String $method, String $uri, array $headers): String{
        global $config;
        /*$hostHeaderValue = getHeaderValue($headers, $config['requiredHeaderName']);

        $fileFullPath = getPathFromResolveHosts($hostHeaderValue, $config['resolveHostValues']);
        if ($fileFullPath === '' || (!file_exists(".".$fileFullPath.$uri))){
            return '404';
        }*/


        if (!preg_match('/^POST$/', $method) ) {
            return '400';
        }
       return '200';

    }


    /**
     * @param Sring $searchingStr
     * @return bool
     */
    function isUserDataFoundInFile(String $searchingStr): bool{
        global $config; //TODO what another way use global variable in many functions
        $fileContent = file_get_contents($config['fileNameUserStorage']);
        return !!preg_match("/^".$searchingStr."$/m", $fileContent);
    }

/** Get response body
 * @param array $headers
 * @param String $uri
 * @param String $body
 * @return String like 'login:password' or empty string otherwise
 */
    function getResponseBody(array $headers, String $uri, String $body): String {
        global $config;
        $hostHeaderValue = getHeaderValue($headers, $config['requiredHeaderName']);

        $fileFullPath = getPathFromResolveHosts($hostHeaderValue, $config['resolveHostValues']);
        if ((file_exists(".".$fileFullPath.$uri))){
            return file_get_contents(".".$fileFullPath.$uri);
        }
        return ''; //TODO if file is empty it's not correct. What should return when file not exist.
    }

    /** Process request & pass response strings into outputHttpResponse(...) function for output
     * @param String $method
     * @param String $uri
     * @param array $headers
     * @param String $body
     */
    function processHttpRequest(String $method, String $uri, array $headers, String $body):void {
//        ...
        global $config;
        $responseBody = getResponseBody($headers, $uri, $body);
        if (!$responseBody){
            $responseStatusCode = '404';
        } else {
            $responseStatusCode = getResponseStatusCode($method, $uri, $headers);
        }

        $responseStatusMessage = $config['responseStatusMessageByCode'][$responseStatusCode];

        $responseHeaders = '';

        //TODO what headers required?
        foreach ($config['headers'] as $k => $v){
            if ($k === 'Date:') {
                $responseHeaders.=$v();
            } elseif ($k === 'Content-Length:') {
                $responseHeaders.=$v($responseBody);
            } else {
                $responseHeaders.=$v;
            }
            $responseHeaders.="\n";
        }
        outputHttpResponse($responseStatusCode, $responseStatusMessage, $responseHeaders, $responseBody);
    }

   //$contents = readHttpLikeInput();
    $contents = $config['testRequest5Another'];

    $http = parseTcpStringAsHttpRequest($contents);
    processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);


//echo $config['header']['Date:'](time());



