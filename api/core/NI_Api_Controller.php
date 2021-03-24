<?php

class NI_Api_Controller
{
    public static function run($Controller)
    {
        $class_method = explode('@', $Controller);

        $class = $class_method[0];
        $method = $class_method[1];

        if (!empty($Controller) && method_exists($class, $method)) {
            $Data_for_send = NI_Api::$data ?? null;
            $class::$method($Data_for_send);
        } else {
            NI_Api::$response['status'] = 404;
            NI_Api::$response['data'] =[
                'msg'=> 'method dose not exist'
            ] ;
            return;
        }
    }
}
