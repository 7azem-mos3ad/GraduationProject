<?php

/**
 * Tish class calls Controllers and inject request data from NI_request
 */

class NI_Controller
{
    /**
     * Run Controller depend on passed param
     *
     * @param [string] $Controller
     * @return void
     */
    public static function run($Controller)
    {
        // Convert Param to method and class
        $class_method = explode('@', $Controller);
        $class = $class_method[0];
        $method = $class_method[1];

        // check if class and method are exist
        if (!empty($Controller) && method_exists($class, $method)) {
            // inject request data as Param to method
            $Data_for_send = NI_request::$data ?? null;
            $class::$method($Data_for_send);
            
            //stop any other call
            exit;
        } else {
            echo 'method dose not exist';
        }
    }
}
