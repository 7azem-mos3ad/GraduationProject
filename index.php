<?php
/**
 * @author Ahmed Hisham -> ahmedhesham2012@yahoo.com
 * @version 4.1
 * init.php call everything on app
 * All requests redirect to index using .htaccess
 */
require_once 'config.php';
require_once 'init.php';
NI_route::run(parse_url($_SERVER['REQUEST_URI'])['path']);
