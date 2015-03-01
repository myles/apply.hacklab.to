<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

$app->get('/', function () use ($app) {
  return $app['twig']->render('index.twig');
})
->bind('home');

// match will accept GET or POST requests
$app->match('/apply', function (Request $request) use ($app) {
  $form = $app['form.factory']->createBuilder('form')
    ->add('name', 'text', [
      'label' => 'Name',
      'attr' => [ 'placeholder' => 'Name or alias' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('email', 'email', [
      'label' => 'Email address',
      'attr' => [ 'placeholder' => 'foobar@example.com' ],
      'constraints' => [ new Assert\NotBlank(), new Assert\Email() ],
    ])
    ->add('bio', 'textarea', [
      'label' => 'Why do you want to join Hacklab?',
      'attr' => [ 'placeholder' => 'Enter a few sentences about yourself and why you want to join Hacklab.TO' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('face-url', 'url', [
      'label' => 'Bio Picture (URL)',
      'attr' => [ 'placeholder' => 'http://www.example.com/my-face.jpg' ],
      'constraints' => [],
    ])
    ->add('face-file', 'file', [
      'label' => 'Bio Picture (Upload)',
      'constraints' => [],
    ])
    ->add('member', 'text', [
      'label' => 'Which member referred you?',
      'attr' => [ 'placeholder' => 'Ada Lovelace' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('twitter', 'text', [
      'label' => 'Twitter',
      'attr' => [ 'placeholder' => 'username' ],
      'constraints' => [],
    ])
    ->add('facebook', 'url', [
      'label' => 'Facebook',
      'attr' => [ 'placeholder' => 'http://www.facebook.com/your.name' ],
      'constraints' => [],
    ])
    ->add('hear', 'textarea', [
      'label' => 'How\'d you hear about us?',
      'attr' => [ 'placeholder' => 'Some bloke just down the street' ],
      'constraints' => [],
    ])
    ->getForm();

  $form->handleRequest($request);

  if ($form->isValid()) {
    $data = $form->getData();

    // do something with the data

    // redirect somewhere
    return $app->redirect('payment');
  }

  return $app['twig']->render('apply.twig', [
    'form' => $form->createView()
  ]);
})
->bind('apply');

$app->get('/payment', function () use ($app) {
  return $app['twig']->render('payment.twig');
})
->bind('payment');
