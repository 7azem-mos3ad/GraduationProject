<?php
$DeveloperCode = [
    ... call_app_resources(ROOT . SEP . 'app' . SEP . 'Controller'),
    ... call_app_resources(ROOT . SEP . 'routes'),
    ... call_app_resources(ROOT . SEP . 'app' . SEP . 'Model')
];

$CoreLoader = [
    ROOT . SEP . 'core' . SEP . 'autoload.php',
    ROOT . SEP . 'core' . SEP . 'Mustache' . SEP . 'Autoloader.php',
];

$coreClassLoader = [
    ROOT . SEP . 'core' . SEP . 'core' . SEP,
    ROOT . SEP . 'core' . SEP . 'SingelLibs' . SEP,
    ROOT . SEP . 'engien' . SEP . 'template' . SEP
];

require_once 'vendor' . SEP . 'autoload.php';

array_map(static function ($path) {
    array_map(static function ($filename) {
        require_once $filename;
    }, glob("{$path}/*.php"));
}, $coreClassLoader);

array_map(static function ($file) {
    if (file_exists($file)) {
        require_once $file;
    }
}, $CoreLoader);

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}
if (isset($_POST['changlang'])) {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == "ar") {
        $_SESSION['lang'] = "en";
    } elseif (isset($_SESSION['lang']) && $_SESSION['lang'] == "en") {
        $_SESSION['lang'] = "ar";
    }
}

use \Whoops\Run as whoops;

if (DEV == true) {
    $NI_whoops = new whoops;
    $NI_whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $NI_whoops->register();
    $NI_bench = new Ubench;
}

NI_view::$path = VIEW;


if (USEDB == true) {
    $NI_connect = new NI_connect(HOST, PORT, DBNAME, USER, PASS);
    switch (DBTYPE) {
    case 'mysql':
            $NI_connect->mysql();
            $conn = $NI_connect->connection();
        break;
    case 'sqlsrv':
            $NI_connect->sqlsrv();
        break;
    case 'sqlite':
            $NI_connect->sqlite();
        break;
    }
}

Mustache_Autoloader::register();
$NI_Mustache = new Mustache_Engine;

use SimpleExcel\SimpleExcel;

if (TRACKING == true) {
    $UserInfo = new UserInfo();
    $excel = (new SimpleExcel('csv'));
    if (file_exists(Tracktable)) {
        $excel->parser->loadFile(Tracktable);
        if (strpos($UserInfo->getCurrentURL(), 'dashboard') === false) {
            $excel->writer->addRow(array($UserInfo->getIP(), $UserInfo->getReverseDNS(), $UserInfo->getCurrentURL(), (empty(explode('.', $UserInfo->getRefererURL())[1]) ? "other" : explode('.', $UserInfo->getRefererURL())[1]) , $UserInfo->getDevice(), $UserInfo->getOS(), $UserInfo->getBrowser(), $UserInfo->getLanguage(), empty($UserInfo->getCountryCode()) ? 'local' : $UserInfo->getCountryCode(), $UserInfo->getCountryName(), $UserInfo->getRegionCode(), $UserInfo->getRegionName(), $UserInfo->getCity(), $UserInfo->getZipcode(), $UserInfo->getLatitude(), $UserInfo->getLongitude(), $UserInfo->isProxy(), date("F d, Y h:i:s A")));
            $excel->writer->saveFile('Tracktable', Tracktable);
        }
    } else {
        $excel->writer->saveFile('Tracktable', Tracktable);
    }
}

function call_app_resources(string $rootDir, $allData = array()) : array
{
    $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
    $dirContent = scandir($rootDir);
    foreach ($dirContent as $key => $content) {
        $path = $rootDir . SEP . $content;
        if (!in_array($content, $invisibleFileNames)) {
            if (is_file($path) && is_readable($path)) {
                if (!empty(pathinfo($path)) && isset(pathinfo($path)['extension']) && pathinfo($path)['extension'] == 'php') {
                    $allData[] = $path;
                }
            } elseif (is_dir($path) && is_readable($path)) {
                $allData = call_app_resources($path, $allData);
            }
        }
    }
    return $allData;
}

array_map(static function ($file) {
    if (file_exists($file)) {
        require_once $file;
    }
}, $DeveloperCode);
