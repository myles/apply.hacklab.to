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
      'label' => 'Real Name *',
      'attr' => [ 'placeholder' => 'John Smith' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('nick', 'text', [
      'label' => 'Nickname *',
      'attr' => [ 'placeholder' => 'jsmithy' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('contactEmail', 'email', [
      'label' => 'Contact Email address *',
      'attr' => [ 'placeholder' => 'foobar@example.com' ],
      'constraints' => [ new Assert\NotBlank(), new Assert\Email() ],
    ])
    ->add('listEmail', 'email', [
      'label' => 'Mailing List Email address *',
      'attr' => [ 'placeholder' => 'foobar+hacklab@example.com' ],
      'constraints' => [ new Assert\NotBlank(), new Assert\Email() ],
    ])
    ->add('bio', 'textarea', [
      'label' => 'Why do you want to join Hacklab? *',
      'attr' => [ 'placeholder' => 'Enter a few sentences about yourself and why you want to join Hacklab.TO' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('faceUrl', 'url', [
      'label' => 'Bio Picture (URL)',
      'attr' => [ 'placeholder' => 'http://www.example.com/my-face.jpg' ],
      'constraints' => [],
    ])
    ->add('faceFile', 'file', [
      'label' => 'Bio Picture (Upload)',
      'constraints' => [],
    ])
    ->add('member', 'text', [
      'label' => 'Name of sponsoring member *',
      'attr' => [ 'placeholder' => 'Ada Lovelace' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('member2', 'text', [
      'label' => 'Seconding member? *',
      'attr' => [ 'placeholder' => 'Alan Turing' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('twitter', 'text', [
      'label' => 'Twitter',
      'attr' => [ 'placeholder' => '@username' ],
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



    // This would be replaced with a primary key in a DB one day.
    $app['session']->set('formData', $data);;

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
