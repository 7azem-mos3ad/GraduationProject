<?php
require_once 'config.php';
// Load the Google API PHP Client Library.
require_once ROOT . '/vendor/autoload.php';

Sentry\init(['dsn' => 'https://19606ee744d848e0939a8b1601695383@o463147.ingest.sentry.io/5467944' ]);

throw new Exception("My first Sentry error!");
