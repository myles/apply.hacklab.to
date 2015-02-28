<?php

$app->get('/{name}', function ($name) use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => $name,
    ));
})
->value('name', 'world');
