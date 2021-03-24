<?php
class NI_view
{
    public static $path;

    public static function notifications()
    {
        $error = [
            'ErrorType' => $_COOKIE['ErrorType']??null,
            'ErrorMsg'=> $_COOKIE['ErrorMsg']??null
        ];
        self::TwigComponents(['notifications'], $error);
    }
    public static function V_php()
    {
        $page = self::$path . implode(SEP, func_get_args()) . '.php';
        if (file_exists($page)) {
            require_once $page;
        } else {
            require_once ROOT.SEP.'ServerErrorHandeler.php';
            exit;
        }
        self::notifications();
    }

    public static function V_html()
    {
        $page = self::$path . implode(SEP, func_get_args()) . '.html';
        if (file_exists($page)) {
            require_once $page;
        } else {
            require_once ROOT.SEP.'ServerErrorHandeler.php';
            exit;
        }
    }

    public static function Mustache()
    {
        $view_args = func_get_args();
        global $NI_Mustache;
        $page = self::$path . implode(SEP, $view_args[0]) . '.html';
        if (file_exists($page)) {
            $templats = file_get_contents($page);
            echo $NI_Mustache->render($templats, $view_args[1]);
            self::notifications();
        } else {
            require_once ROOT.SEP.'ServerErrorHandeler.php';
            exit;
        }
    }

    public static function Twig()
    {
        $view_args = func_get_args();
        $page = self::$path . implode(SEP, $view_args[0]) . '.html';

        if (file_exists($page)) {
            $css_arr = $view_args[1]['css_arr'];
            $header_js_arr = $view_args[1]['header_js_arr'];
            $footer_js_arr = $view_args[1]['footer_js_arr'];

            $arr_for_twig = $view_args[0];
            $file_for_twig = array_pop($arr_for_twig);
            $file_for_twig = $file_for_twig . '.html';
            $path_for_twig = self::$path . implode(SEP, $arr_for_twig);
            $path_for_twig = rtrim($path_for_twig, SEP);

            NI_template::head($_SESSION['lang'], $css_arr, $header_js_arr);
            global $arr_lang;
            NI_lang::set($arr_lang);
            $args = NI_template::$url_array;

            $render_data = $view_args[2];
            $render_data['lang'] = $arr_lang;
            $render_data['form'] = implode('/', explode('/', explode("?", $_SERVER['REQUEST_URI'])[0]));


            $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader($path_for_twig));
            echo $twig->render($file_for_twig, $render_data);

            NI_template::footer($footer_js_arr);
            self::notifications();
        } else {
            require_once ROOT.SEP.'ServerErrorHandeler.php';
            exit;
        }
    }

    public static function TwigComponents()
    {
        $view_args = func_get_args();
        $page = self::$path . implode(SEP, $view_args[0]) . '.html';

        if (file_exists($page)) {
            $arr_for_twig = $view_args[0];
            $file_for_twig = array_pop($arr_for_twig);
            $file_for_twig = $file_for_twig . '.html';
            $path_for_twig = self::$path . implode(SEP, $arr_for_twig);
            $path_for_twig = rtrim($path_for_twig, SEP);

            $render_data = $view_args[1];
            $render_data['lang'] = NI_lang::$arr_lang;
            $render_data['form'] = implode('/', explode('/', explode("?", $_SERVER['REQUEST_URI'])[0]));
            


            $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader($path_for_twig));
            echo $twig->render($file_for_twig, $render_data);
        } else {
            require_once ROOT.SEP.'ServerErrorHandeler.php';
            exit;
        }
    }
}
