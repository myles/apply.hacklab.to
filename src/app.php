<?php

date_default_timezone_set('UTC');

// Config parsing
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
$locator = new FileLocator([ __DIR__ . '/../config' ]);
$config = Yaml::parse(file_get_contents(
  $locator->locate('config.' . $app['env'] . '.yml', null, false)[0]
));

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\SessionServiceProvider;

$app->register(new UrlGeneratorServiceProvider());
$app->register(new TwigServiceProvider(), [
  'twig.path' => __DIR__.'/../views',
]);
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider([
  'form.secret' => $config['formSecret']
]));
$app->register(new TranslationServiceProvider());
$app->register(new SessionServiceProvider());
