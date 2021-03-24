<?php
class CLI_Plugin
{
    public static function GitPluginFrom_github(array $input)
    {
        if (isset($input[1]) && isset($input[2])) {
            $user = $input[1];
            $app = $input[2];
            mkdir(MARKET . $app, 0755);
            $url = 'https://codeload.github.com/' . $user . '/' . $app . '/zip/master';
            $plugins = MARKET . $app . SEP . $app;
            $plugin = MARKET . $app . SEP;
            system(" wget $url -O $plugins");
            chmod($plugins, 0777);
            system(" unzip $plugins -d $plugin");
            system(" mv market-place/$app/$app-master/* market-place/$app/");
            system(" rm market-place/$app/$app");
            system(" rm -r market-place/$app/$app-master");
        } else {
            echo 'command: php install git "username" "repo"';
        }
    }

    public static function CheckPluginFiles(array $input)
    {
        if (isset($input[1])) {
            if (!is_dir(MARKET . $input[1])) {
                echo "plugin $input[1] dose not exist";
                return false;
            } else {
                $scan = scandir(MARKET . $input[1]);
                $design_plugin = ['api_model', 'controller', 'css', 'installer', 'js', 'lang', 'model', 'storage', 'view'];
                $match_scan = array_intersect($scan, $design_plugin);
                if (count($match_scan) >= 9) {
                    echo "\e[1;32;40m plugin '$input[1]' \e[0m  \e[0;32;40m ready  \e[0m  \n";
                    echo "\e[0;31;42m now will check files and folders \e[0m\n\n";
                    $p_path = MARKET . $input[1] . SEP;
                    $dir_lang = CLI_Helper::CheckMatchingFilesOnFolders($p_path . 'lang', 2, ['ar.php', 'en.php']);
                    $dir_installer = CLI_Helper::CheckMatchingFilesOnFolders(
                        $p_path . 'installer',
                        4,
                        ['api_route.php', 'route.php', 'seeds.php', 'tables.php']
                    );

                    if ($dir_lang == true && $dir_installer == true) {
                        echo "\n \e[0;33;40m plugin compatable with NI and ready to install \e[0m\n";

                        return true;
                    }
                } else {
                    echo "plugin $input[1] dose not compatable with NI";
                    return false;
                }
            }
        } else {
            echo 'command php NI check "plugin_name"';
            return false;
        }
    }

    public static function InstallPlugin(array $input)
    {
        if (self::CheckPluginFiles($input) != true) {
            return;
        }

        $ServiceDir = MARKET . $input[1] . SEP . 'installer' . SEP;

        $file_api_route = CLI_Helper::GetCodeFromPhpFile($ServiceDir . 'api_route.php');
        $app_api_route = fopen(ROOT . SEP . 'api' . SEP . 'routes' . SEP . 'route.php', 'a+');
        fwrite($app_api_route, $file_api_route);
        fclose($app_api_route);

        $file_route = CLI_Helper::GetCodeFromPhpFile($ServiceDir . 'route.php');
        $app_route = fopen(ROOT . SEP . 'routes' . SEP . 'route.php', 'a+');
        fwrite($app_route, $file_route);
        fclose($app_route);

        $file_seeds = CLI_Helper::GetCodeFromPhpFile($ServiceDir . 'seeds.php');
        $app_seeds = fopen(ROOT . SEP . 'CLI' . SEP . 'seeds.php', 'a+');
        fwrite($app_seeds, $file_seeds);
        fclose($app_seeds);

        $file_tables = CLI_Helper::GetCodeFromPhpFile($ServiceDir . 'tables.php');
        $app_tables = fopen(ROOT . SEP . 'CLI' . SEP . 'tables.php', 'a+');
        fwrite($app_tables, $file_tables);
        fclose($app_tables);

        $lang_ar = CLI_Helper::GetCodeFromPhpFile(MARKET . $input[1] . SEP . 'lang' . SEP . 'ar.php');
        $app_lang_ar = fopen(ROOT . SEP . 'engien' . SEP . 'lang' . SEP . 'ar.php', 'a+');
        fwrite($app_lang_ar, $lang_ar);
        fclose($app_lang_ar);

        $lang_en = CLI_Helper::GetCodeFromPhpFile(MARKET . $input[1] . SEP . 'lang' . SEP . 'en.php');
        $app_lang_en = fopen(ROOT . SEP . 'engien' . SEP . 'lang' . SEP . 'en.php', 'a+');
        fwrite($app_lang_en, $lang_en);
        fclose($app_lang_en);


        self::CopyPluginFromMarkitToApp(MARKET . $input[1]);
    }

    public static function CopyPluginFromMarkitToApp($from)
    {
        $scan_plugin = CLI_Helper::GetPluginFilesArrays($from);
        $file_list = CLI_Helper::ConvertPluginArraysToArray($scan_plugin);
        foreach ($file_list as $file) {
            $current_file_path = str_replace($from, "", $file);
            $arr = explode(SEP, $current_file_path);
            array_shift($arr);
            $FileType = $arr[0];

            switch ($FileType) {
                case 'view':
                    if (!is_dir(VIEW . $arr[1])) {
                        mkdir(VIEW . $arr[1], 0755);
                    }
                    if (count($arr) - 3  <= 0) {
                        if (!file_exists(VIEW . $arr[1] . SEP . $arr[2])) {
                            copy($file, VIEW . $arr[1] . SEP . $arr[2]);
                        } else {
                            copy($file, VIEW . $arr[1] . SEP . time() . $arr[2]);
                        }
                    } else {
                        $arr2 = $arr;
                        $arr22 = $arr;
                        array_shift($arr2);
                        array_shift($arr22);
                        array_pop($arr2);
                        $newinnerpath = VIEW . implode(SEP, $arr2);
                        $NewInnerFilePath = VIEW . implode(SEP, $arr22);
                        if (!is_dir($newinnerpath)) {
                            mkdir($newinnerpath);
                        }
                        if (!file_exists($NewInnerFilePath)) {
                            $NewInnerFilePath_array = explode(SEP, $NewInnerFilePath);
                            array_pop($NewInnerFilePath_array);
                            $n2 = implode(SEP, $NewInnerFilePath_array);
                            //copy($file, $NewInnerFilePath);
                            copy($file, $n2);
                        }
                    }
                    break;

                case 'model':
                    if (!is_dir(MODEL . $arr[1])) {
                        mkdir(MODEL . $arr[1], 0755);
                    }
                    if (count($arr) - 3  <= 0) {
                        if (!file_exists(MODEL . $arr[1] . SEP . $arr[2])) {
                            copy($file, MODEL . $arr[1] . SEP . $arr[2]);
                        } else {
                            copy($file, MODEL . $arr[1] . SEP . time() . $arr[2]);
                        }
                    } else {
                        $arr2 = $arr;
                        $arr22 = $arr;
                        array_shift($arr2);
                        array_shift($arr22);
                        array_pop($arr2);
                        $newinnerpath = MODEL . implode(SEP, $arr2);
                        $NewInnerFilePath = MODEL . implode(SEP, $arr22);
                        if (!is_dir($newinnerpath)) {
                            mkdir($newinnerpath);
                        }
                        if (!file_exists($NewInnerFilePath)) {
                            $NewInnerFilePath_array = explode(SEP, $NewInnerFilePath);
                            array_pop($NewInnerFilePath_array);
                            $n2 = implode(SEP, $NewInnerFilePath_array);
                            //copy($file, $NewInnerFilePath);
                            copy($file, $n2);
                        }
                    }
                    break;

                case 'api_model':
                    if (!is_dir(API_MODEL . $arr[1])) {
                        mkdir(API_MODEL . $arr[1], 0755);
                    }
                    if (count($arr) - 3  <= 0) {
                        if (!file_exists(API_MODEL . $arr[1] . SEP . $arr[2])) {
                            copy($file, API_MODEL . $arr[1] . SEP . $arr[2]);
                        } else {
                            copy($file, API_MODEL . $arr[1] . SEP . time() . $arr[2]);
                        }
                    } else {
                        $arr2 = $arr;
                        $arr22 = $arr;
                        array_shift($arr2);
                        array_shift($arr22);
                        array_pop($arr2);
                        $newinnerpath = API_MODEL . implode(SEP, $arr2);
                        $NewInnerFilePath = API_MODEL . implode(SEP, $arr22);
                        if (!is_dir($newinnerpath)) {
                            mkdir($newinnerpath);
                        }
                        if (!file_exists($NewInnerFilePath)) {
                            $NewInnerFilePath_array = explode(SEP, $NewInnerFilePath);
                            array_pop($NewInnerFilePath_array);
                            $n2 = implode(SEP, $NewInnerFilePath_array);
                            //copy($file, $NewInnerFilePath);
                            copy($file, $n2);
                        }
                    }
                    break;

                case 'storage':
                    if (!is_dir(STORAGE . $arr[1])) {
                        mkdir(STORAGE . $arr[1], 0755);
                    }
                    if (count($arr) - 3  <= 0) {
                        if (!file_exists(STORAGE . $arr[1] . SEP . $arr[2])) {
                            copy($file, STORAGE . $arr[1] . SEP . $arr[2]);
                        } else {
                            copy($file, STORAGE . $arr[1] . SEP . time() . $arr[2]);
                        }
                    } else {
                        $arr2 = $arr;
                        $arr22 = $arr;
                        array_shift($arr2);
                        array_shift($arr22);
                        array_pop($arr2);
                        $newinnerpath = STORAGE . implode(SEP, $arr2);
                        $NewInnerFilePath = STORAGE . implode(SEP, $arr22);
                        if (!is_dir($newinnerpath)) {
                            mkdir($newinnerpath);
                        }
                        if (!file_exists($NewInnerFilePath)) {
                            $NewInnerFilePath_array = explode(SEP, $NewInnerFilePath);
                            array_pop($NewInnerFilePath_array);
                            $n2 = implode(SEP, $NewInnerFilePath_array);
                            //copy($file, $NewInnerFilePath);
                            copy($file, $n2);
                        }
                    }
                    break;

                case 'controller':
                    if (!is_dir(CONTROLLER . $arr[1])) {
                        mkdir(CONTROLLER . $arr[1], 0755);
                    }
                    if (count($arr) - 3  <= 0) {
                        if (!file_exists(CONTROLLER . $arr[1] . SEP . $arr[2])) {
                            copy($file, CONTROLLER . $arr[1] . SEP . $arr[2]);
                        } else {
                            copy($file, CONTROLLER . $arr[1] . SEP . time() . $arr[2]);
                        }
                    } else {
                        $arr2 = $arr;
                        $arr22 = $arr;
                        array_shift($arr2);
                        array_shift($arr22);
                        array_pop($arr2);
                        $newinnerpath = CONTROLLER . implode(SEP, $arr2);
                        $NewInnerFilePath = CONTROLLER . implode(SEP, $arr22);
                        if (!is_dir($newinnerpath)) {
                            mkdir($newinnerpath);
                        }
                        if (!file_exists($NewInnerFilePath)) {
                            $NewInnerFilePath_array = explode(SEP, $NewInnerFilePath);
                            array_pop($NewInnerFilePath_array);
                            $n2 = implode(SEP, $NewInnerFilePath_array);
                            //copy($file, $NewInnerFilePath);
                            copy($file, $n2);
                        }
                    }
                    break;

                case 'js':
                    if (!isset($arr[2])) {
                        if (!file_exists(JS . $arr[1])) {
                            copy($file, JS . $arr[1]);
                        } else {
                            copy($file, JS . time() . $arr[1]);
                        }
                    } else {
                        if (!file_exists(JS . $arr[1])) {
                            mkdir(JS . $arr[1], 0755);
                        }
                        if (!file_exists(JS . $arr[1] . SEP . $arr[2])) {
                            copy($file, JS . $arr[1] . SEP . $arr[2]);
                        } else {
                            copy($file, JS . $arr[1] . SEP . time() . $arr[2]);
                        }
                    }
                    break;

                case 'css':
                    if (!isset($arr[2])) {
                        if (!file_exists(CSS . $arr[1])) {
                            copy($file, CSS . $arr[1]);
                        } else {
                            copy($file, CSS . time() . $arr[1]);
                        }
                    } else {
                        if (!file_exists(CSS . $arr[1])) {
                            mkdir(CSS . $arr[1], 0755);
                        }
                        if (!file_exists(CSS . $arr[1] . SEP . $arr[2])) {
                            copy($file, CSS . $arr[1] . SEP . $arr[2]);
                        } else {
                            copy($file, CSS . $arr[1] . SEP . time() . $arr[2]);
                        }
                    }
                    break;

                default:
                    break;
            }
        }
    }
}
