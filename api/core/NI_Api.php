<?php
class NI_Api
{
    // public static $url;
    // public static $method;
    public static $data = [];

    public static $response = [];


    public static function run($action)
    {
        // self::$url =$action;
        // self::$method = $method;
        self::$data = self::CatchAndHandelRequestData();
        // self::HandelMethod();
        NI_Api_route::run($action);
        NI_Api::Api_Handeler();
    }

    
    public static function CatchAndHandelRequestData()
    {
        if (!isset($_SERVER["CONTENT_TYPE"])) {
            return null;
        }
        if (strpos($_SERVER["CONTENT_TYPE"], 'x-www-form-urlencoded') !== false) {
            $arr = explode('&', file_get_contents("php://input"));
            $newarr = [];
            foreach ($arr as $temp) {
                $temparr = explode('=', $temp);
                $key = $temparr[0];
                $value = str_replace('%40', '@', (string) $temparr[1]);
                $newarr[$key] = $value;
            }
            return $newarr;
        } elseif (strpos($_SERVER["CONTENT_TYPE"], 'form-data') !== false) {
            if (empty($_POST) && empty($_FILES)) {
                return null;
            }
            return [$_POST, $_FILES];
        }
    }

    public static function Api_Handeler()
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
        header('HTTP/1.1 ' . self::$response['status'] . ' ' . $http_response_code[self::$response['status']]);
        header("Access-Control-Allow-Origin: * ");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $json_response = is_object(self::$response['data']) ? self::$response['data'] : json_encode(self::$response['data']);
        echo($json_response);
        exit;
    }
}
