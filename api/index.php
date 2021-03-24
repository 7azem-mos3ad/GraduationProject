<?php
require_once 'init.php';
NI_Api::$response['status'] = 200;
NI_Api::$response['data'] = null;
NI_Api::run(implode('/', explode('/', explode("?", explode("api", $_SERVER['REQUEST_URI'])[1])[0])));
