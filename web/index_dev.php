<?php

// Serve static files in PHP's 5.4 CLI server
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();
$app['env'] = 'dev';

$app['debug'] = true;

require __DIR__.'/../src/app.php';
require __DIR__.'/../src/controllers.php';

$app->run();
