<?php

require_once '../config.php';
require_once '../init.php';
$API_core_filepath = call_app_resources(ROOT . SEP . 'api' . SEP . 'core');
$API_route_filepath = call_app_resources(ROOT . SEP . 'api' . SEP . 'routes');
$api_Controller = call_app_resources(ROOT.SEP.'api'.SEP.'Controller'.SEP);
$all_api_files = [...$API_core_filepath, ...$API_route_filepath, ...$api_Controller];
array_map(static function ($file) {
    if (file_exists($file)) {
        require_once $file;
    }
}, $all_api_files);
unset($API_core_filepath);
unset($API_route_filepath);
unset($api_Controller);
unset($all_api_files);
