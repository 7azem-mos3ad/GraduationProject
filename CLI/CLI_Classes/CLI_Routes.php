<?php
class CLI_Routes
{
    public static function GetRoutes()
    {
        $Routes = (new NI_route)->Routes();
        $ApiRoutes = (new NI_Api_route)->Routes();

        echo "\e[1;33;40m Normal Routes \e[0m\n";
        foreach ($Routes as $key =>$value) {
            echo "\e[1;33;40m $key Routes \e[0m\n";
            foreach ($value as $path) {
                echo "\e[1;33;40m $path \e[0m\n";
            }
        }

        echo "\e[1;33;40m Api Routes \e[0m\n";
        foreach ($ApiRoutes as $key =>$value) {
            echo "\e[1;33;40m $key Routes \e[0m\n";
            foreach ($value as $path) {
                echo "\e[1;33;40m $path \e[0m\n";
            }
        }
    }

    public static function MakeRouteFiles()
    {
        $dbarr = CLI_Helper::GetDBColumnArray();
        foreach ($dbarr as $table => $ColArr) {
            $NewRoute = ROUTE . $table . '.php';
            if (!is_file($NewRoute)) {
                $MyRoute = fopen($NewRoute, "w");
                $code = '
                NI_route::get("/'.$table.'/home", function () {
                    NI_Controller::run("'.$table.'@home");
                });

                NI_route::get("/'.$table.'/find/{{id}}", function ($id) {
                    NI_Controller::run("'.$table.'@find");
                });
                
                NI_route::get("/'.$table.'/data", function () {
                    NI_Controller::run("'.$table.'@data");
                });
                
                NI_route::post("/'.$table.'/edit/{{id}}", function ($id) {
                    NI_Controller::run("'.$table.'@edit");
                });
                
                NI_route::post("/'.$table.'/add", function () {
                    NI_Controller::run("'.$table.'@add");
                });
                
                NI_route::post("/'.$table.'/delete/{{id}}", function ($id) {
                    NI_Controller::run("'.$table.'@delete");
                });
                
                ';
                fwrite($MyRoute, $code);
                echo "\e[1;33;40m ROUTE $table create successfully \e[0m\n";
            } else {
                echo "\e[1;33;40m ROUTE $table already exists \e[0m\n";
            }
        }
    }

    public static function MakeApiRouteFiles()
    {
        $dbarr = CLI_Helper::GetDBColumnArray();
        foreach ($dbarr as $table => $ColArr) {
            $NewRoute = APIROUTE . $table . '.php';
            if (!is_file($NewRoute)) {
                $MyRoute = fopen($NewRoute, "w");
                $code = '
                
                NI_Api_route::get("/'.$table.'/data",function(){
                    NI_Api_Controller::run("'.$table.'@data");
                });
                
                NI_Api_route::get("/'.$table.'/find/{{id}}",function($id){
                    NI_Api_Controller::run("'.$table.'@find");
                });

                NI_Api_route::post("/'.$table.'/edit/{{id}}",function($id){
                    NI_Api_Controller::run("'.$table.'@edit");
                });

                NI_Api_route::post("/'.$table.'/delete/{{id}}",function($id){
                    NI_Api_Controller::run("'.$table.'@delete");
                });
                
                NI_Api_route::post("/'.$table.'/add",function(){
                    NI_Api_Controller::run("'.$table.'@add");
                });
                ';
                fwrite($MyRoute, $code);
                echo "\e[1;33;40m ROUTE $table create successfully \e[0m\n";
            } else {
                echo "\e[1;33;40m ROUTE $table already exists \e[0m\n";
            }
        }
    }
}
