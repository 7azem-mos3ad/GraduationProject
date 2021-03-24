<?php
class NI_redirect
{
    public static function reload($time, $path)
    {
        $url = explode('/', $_SERVER['REQUEST_URI']);
        $url = $url[0].'/'.$path;
        header("Refresh: $time; url=$url") ;
    }
    public static function path($path_dir)
    {
        header("location: ".$path_dir);
    }
    public static function with(string $path_dir, string $type, string $msg)
    {
        setcookie('ErrorType', $type, time() + 2, "/");
        setcookie('ErrorMsg', $msg, time() + 2, "/");

        header("location: ".$path_dir);
    }
}
