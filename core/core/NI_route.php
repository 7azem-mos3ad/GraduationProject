<?php
class NI_route
{
    protected static $routes = [];
    protected static $PostRoutes = [];
    protected static $PutRoutes = [];
    protected static $DeleteRoutes = [];
    protected static $any = [];
    protected static function get_strig_between($string)
    {
        $tempVar = [];
        $Arr_string = explode('/', $string);
        foreach ($Arr_string as $string) {
            $string = ' ' . $string;
            $ini = strpos($string, '{{');
            if ($ini == 0) {
                continue;
            }
            $ini += strlen('{{');
            $len = strpos($string, '}}', $ini) - $ini;
            array_push($tempVar, substr($string, $ini, $len));
        }
        return count($tempVar);
    }

    public static function MatchParamFromUrl($MasterRouteArray, $ActionRoute, $NumOfParameter = 0, $newPassVarArr = [])
    {
        $pregdigit = "/+[a-zA-Z0-9_.-{}]*";
        $mypreg =  $ActionRoute[0].str_repeat($pregdigit, count($ActionRoute)-1).'';
        $matches  = preg_grep("#^$mypreg#i", array_keys($MasterRouteArray));
        
        $checkIfStop =0;
        $keyToRedirect =0;
        foreach ($matches as $k => $key) {
            $RoutePath = explode('/', $key);
            $NewRoutePath = $RoutePath;
            $NewActionRoute = $ActionRoute;
            $NumOfParameter = self::get_strig_between($key);


            for ($i=0; $i < $NumOfParameter; $i++) {
                array_pop($NewRoutePath);
                array_pop($NewActionRoute);
            }
            

            if (count($RoutePath) == count($ActionRoute) && ($NewRoutePath === $NewActionRoute)) {
                for ($i=(count($RoutePath)-$NumOfParameter); $i < count($RoutePath) ; $i++) {
                    array_push($newPassVarArr, $ActionRoute[$i]);
                }
                $checkIfStop=1;
                $keyToRedirect = $key;
                break;
            }
        }


        if ($checkIfStop == 1) {
            $callback =  $MasterRouteArray[$keyToRedirect];
            echo call_user_func_array($callback, $newPassVarArr);
            return ;
        } else {
            require_once ROOT.SEP.'ServerErrorHandeler.php';
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            exit;
        }
    }

    public function Routes()
    {
        return [
            'get' => array_keys(self::$routes),
            'post' => array_keys(self::$PostRoutes),
            'put' => array_keys(self::$PutRoutes),
            'delete' => array_keys(self::$DeleteRoutes),
            'any' => array_keys(self::$any)
        ];
    }
    public static function get($action, Closure $callback)
    {
        if (key_exists($action, self::$routes)) {
            throw new Exception("Warning! This route has been used before");
        }
        self::$routes[$action] = $callback;
    }

    public static function post($action, Closure $callback)
    {
        if (key_exists($action, self::$PostRoutes)) {
            throw new Exception("Warning! This route has been used before");
        }
        self::$PostRoutes[$action] = $callback;
    }

    public static function put($action, Closure $callback)
    {
        //NI_request::$data = NI_request::FromatPostData(file_get_contents("php://input"));
        if (key_exists($action, self::$PutRoutes)) {
            throw new Exception("Warning! This route has been used before");
        }
        self::$PutRoutes[$action] = $callback;
    }

    public static function delete($action, Closure $callback)
    {
        //NI_request::$data = NI_request::FromatPostData(file_get_contents("php://input"));
        if (key_exists($action, self::$DeleteRoutes)) {
            throw new Exception("Warning! This route has been used before");
        }
        self::$DeleteRoutes[$action] = $callback;
    }

    public static function any($action, Closure $callback)
    {
        if (key_exists($action, self::$any)) {
            throw new Exception("Warning! This route has been used before");
        }
        self::$any[$action] = $callback;
    }

    public static function run($action)
    {
        $ActionRoute = explode('/', $action);
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'GET':

                if (array_key_exists($action, self::$routes)) {
                    $callback =  self::$routes[$action];
                    echo call_user_func($callback);
                    return ;
                } elseif (array_key_exists($action, self::$any)) {
                    $callback =  self::$any[$action];
                    echo call_user_func($callback);
                    return ;
                } else {
                    self::MatchParamFromUrl(self::$routes, $ActionRoute);
                }
                break;

            case 'POST':

                if (array_key_exists($action, self::$PostRoutes)) {
                    $callback =  self::$PostRoutes[$action];
                    echo call_user_func($callback);
                    return ;
                } elseif (array_key_exists($action, self::$any)) {
                    $callback =  self::$any[$action];
                    echo call_user_func($callback);
                    return ;
                } else {
                    self::MatchParamFromUrl(self::$PostRoutes, $ActionRoute);
                }
                break;

            case 'PUT':

                if (array_key_exists($action, self::$PutRoutes)) {
                    $callback =  self::$PutRoutes[$action];
                    echo call_user_func($callback);
                    return ;
                } elseif (array_key_exists($action, self::$any)) {
                    $callback =  self::$any[$action];
                    echo call_user_func($callback);
                    return ;
                } else {
                    self::MatchParamFromUrl(self::$PutRoutes, $ActionRoute);
                }
                break;

            case 'DELETE':

                if (array_key_exists($action, self::$DeleteRoutes)) {
                    $callback =  self::$DeleteRoutes[$action];
                    echo call_user_func($callback);
                    return ;
                } elseif (array_key_exists($action, self::$any)) {
                    $callback =  self::$any[$action];
                    echo call_user_func($callback);
                    return ;
                } else {
                    self::MatchParamFromUrl(self::$DeleteRoutes, $ActionRoute);
                }
                break;
            
            default:
            if (array_key_exists($action, self::$any)) {
                $callback =  self::$any[$action];
                echo call_user_func($callback);
            } else {
                require_once ROOT.SEP.'ServerErrorHandeler.php';
                header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
                exit;
            }
            break;
            
        }
    }
}
