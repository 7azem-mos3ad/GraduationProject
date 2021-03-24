<?php
class CLI_Helper
{
    public static function CheckMatchingFilesOnFolders($dir, int $nmatch, $stander = array())
    {
        $scan = scandir($dir);
        $match_scan = array_intersect($scan, $stander);
        if (count($match_scan) >= $nmatch) {
            echo "\e[1;32;40m dir $dir \e[0m  \e[0;32;40m ready  \e[0m  \n";
            return true;
        } else {
            echo "\e[0;31;40m dir $dir dose not compatable with NI \e[0m\n";
            return false;
        }
    }

    public static function GetCodeFromPhpFile($file)
    {
        $file2 = file_get_contents($file);
        $file2 = str_replace("<?php", "", $file2);
        $file2 = str_replace("?>", "", $file2);
        return $file2;
    }

    public static function GetPluginFilesArrays($from)
    {
        $temp = [];
        $dir = [];
        $files = [];

        $arr = (is_dir($from)) ? scandir($from) : [];

        $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd", "installer", "lang");

        foreach ($arr as $key) {
            if (!in_array($key, $invisibleFileNames)) {
                array_push($temp, $key);
            }
        }

        foreach ($temp as $key) {
            if (preg_match('/(\.)([a-zA-Z0-9\s_\\.\-\(\):])+/', $key)) {
                array_push($files, $from . SEP . $key);
            } else {
                array_push($dir, $key);
            }
        }

        foreach ($dir as $key) {
            $temprecarr = self::GetPluginFilesArrays($from . SEP . $key);
            array_push($files, $temprecarr);
        }

        return ($files);
    }

    public static function ConvertPluginArraysToArray($arr)
    {
        $result = [];

        foreach ($arr as $key) {
            if (is_array($key)) {
                $temp = self::ConvertPluginArraysToArray($key);

                foreach ($temp as $value) {
                    array_push($result, $value);
                }
            } else {
                array_push($result, $key);
            }
        }
        return ($result);
    }

    public static function ReadCLITableFile()
    {
        $tableArray = ROOT . SEP . 'CLI' . SEP . 'tables.php';
        if (is_file($tableArray)) {
            return include $tableArray;
        }
        unset($tableArray);
        return array();
    }

    public static function ReadCLISeedssFile()
    {
        $SeedsFiles = ROOT . SEP . 'CLI' . SEP . 'seeds.json';
        $ReadSeedsFiles = file_get_contents($SeedsFiles);
        return json_decode($ReadSeedsFiles, true);
    }

    public static function GetDBColumnArray()
    {
        $database_arr = [];
        $t_arr = self::ReadCLITableFile();

        foreach ($t_arr as $key => $value) {
            $temp_arr = [];
            $Col_line_arr = explode(',', $value);
            foreach ($Col_line_arr as $keyINcol => $valINcol) {
                $ColumnName = explode(' ', ltrim(preg_replace('/\s+/', ' ', $valINcol), ' '))[0];
                if (!empty($ColumnName)) {
                    array_push($temp_arr, $ColumnName);
                }
            }
            $database_arr[$key] = $temp_arr;
        }
        return $database_arr;
    }
}
