<?php

use Symfony\Component\HttpFoundation\Request;

$app->get('/', function () use ($app) {
  return $app['twig']->render('index.twig');
})
->bind('home');

$app->get('/apply', function () use ($app) {
  return $app['twig']->render('apply.twig');
})
->bind('apply');
