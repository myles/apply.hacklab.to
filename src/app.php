<?php

date_default_timezone_set('UTC');

// Config parsing
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
$locator = new FileLocator([ __DIR__ . '/../config' ]);
$config = Yaml::parse(file_get_contents(
  $locator->locate('config.' . $app['env'] . '.yml', null, false)[0]
));

$app['config'] = $config;

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\SessionServiceProvider;
// use Silex\Provider\SecurityServiceProvider;

$app->register(new UrlGeneratorServiceProvider());
$app->register(new TwigServiceProvider(), [
  'twig.path' => __DIR__.'/../views',
]);
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider([
  'form.secret' => $app['config']['formSecret']
]));
$app->register(new TranslationServiceProvider());
$app->register(new SessionServiceProvider([
  'cookie_secure' => (boolean) $app['config']['enforceHttps'],
  'cookie_httponly' => true,
]));
// $app->register(new SecurityServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => $app['config']['database']['driver'],
        'dbname' => $app['config']['database']['dbname'],
        'user' => $app['config']['database']['user'],
        'password' => $app['config']['database']['password'],
        'host' => $app['config']['database']['host'],
        'path' => __DIR__.'/../' . $app['config']['database']['path'],
    ),
));

if ($app['config']['enforceHttps']) {
  $app['controllers']->requireHttps();
}
