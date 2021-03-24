<?php

class NI_response
{
    public static function forAxios(array $response)
    {
        $http_response_code = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        header('HTTP/1.1 ' . $response['status'] . ' ' . $http_response_code[$response['status']]);
        header("Access-Control-Allow-Origin: * ");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $json_response = is_object($response['data']) ? $response['data'] : json_encode($response['data']);
        echo($json_response);
        exit;
    }
    public static function pagination($data, $limit =10)
    {
        $page = !isset($_GET['page']) ? 1 : $_GET['page'];
        $offset = ($page - 1) * $limit; // offset
        $total_items = count($data); // total items
        $total_pages = ceil($total_items / $limit);
        $final = array_splice($data, $offset, $limit);
        return ['info' => [
            'total_pages' => $total_pages,
            'limit' => $limit,
            'current' => $page
        ],
        'data' => $final];
    }
}
