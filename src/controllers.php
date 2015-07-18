<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;

$app['security.firewalls'] = [
  'unsecured' => [
    'anonymous' => true,
    'pattern' => '^/admin',
  ],
  'admin' => [
    'pattern' => '^/admin',
    'http' => true,
    'users' => [
      'admin' => ['ROLE_ADMIN', $app['config']['adminPassword']],
    ],
  ],
];
$app['security.access_rules'] = [
  ['^/admin', 'ROLE_ADMIN'],
  ['^.*$', 'ROLE_USER'],
];

$sharedAuthentication = function (Request $request, Silex\Application $app) {
  if ($app['session']->get('isSharedAuthenticated') !== true) {
    return new RedirectResponse('/');
  }
};

$app->get('/', function () use ($app) {
  return $app['twig']->render('index.twig');
})
->bind('home');

$app->post('/', function (Request $request) use ($app) {
  if ($request->get('password') === $app['config']['sharedPassword']) {
    $app['session']->set('isSharedAuthenticated', true);
  }
  return $app->redirect('/apply');
});

// match will accept GET or POST requests
$app->match('/apply', function (Request $request) use ($app) {
  $form = $app['form.factory']->createBuilder('form')
    ->add('name', 'text', [
      'label' => 'Real Name *',
      'attr' => [ 'placeholder' => 'Alex Smith' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('username', 'text', [
      'label' => 'Username *',
      'attr' => [ 'placeholder' => 'asmith' ],
      'constraints' => [
        new Assert\NotBlank(),
        new Assert\Regex([
          'pattern' => '/^[a-z0-9]+$/',
          'message' => 'Username can only be alphanumeric characters, all lower case.'
        ])
      ],
    ])
    ->add('nickname', 'text', [
      'label' => 'Nickname (used for door announcements, etc.) *',
      'attr' => [ 'placeholder' => 'Alex' ],
      'constraints' => [
        new Assert\NotBlank(),
      ],
    ])
    ->add('contact_email', 'email', [
      'label' => 'Contact Email address *',
      'attr' => [ 'placeholder' => 'foobar@example.com' ],
      'constraints' => [ new Assert\NotBlank(), new Assert\Email() ],
    ])
    ->add('list_email', 'email', [
      'label' => 'Mailing List Email address *',
      'attr' => [ 'placeholder' => 'foobar+hacklab@example.com' ],
      'constraints' => [ new Assert\NotBlank(), new Assert\Email() ],
    ])
    ->add('bio_reason', 'textarea', [
      'label' => 'Why do you want to join Hacklab? *',
      'attr' => [ 'placeholder' => 'Enter a few sentences about yourself and why you want to join Hacklab.TO' ],
      'constraints' => [ new Assert\NotBlank() ],
    ])
    ->add('face_url', 'url', [
      'label' => 'Bio Picture (URL)',
      'attr' => [ 'placeholder' => 'http://www.example.com/my-face.jpg' ],
      'constraints' => [],
    ])
    ->add('face_file', 'file', [
      'label' => 'Bio Picture (Upload)',
      'constraints' => [],
    ])
    ->add('token_type', 'choice', [
      'label' => 'Preferred Access Token Type',
      'expanded' => true,
      'choices' => [ 'fob' => 'Key Fob', 'card' => 'Card' ],
      'data' => 'fob'
    ])
    ->add('sponsor', 'text', [
      'label' => 'Name of sponsoring member',
      'attr' => [ 'placeholder' => 'None' ],
      'constraints' => [ ],
    ])
    ->add('second_sponsor', 'text', [
      'label' => 'Seconding member?',
      'attr' => [ 'placeholder' => 'None' ],
      'constraints' => [ ],
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
    ->add('website', 'url', [
      'label' => 'Your Website',
      'attr' => [ 'placeholder' => 'http://www.example.com/' ],
      'constraints' => [],
    ])
    ->add('heard_from', 'textarea', [
      'label' => 'How\'d you hear about us?',
      'attr' => [ 'placeholder' => 'Someone just down the street' ],
      'constraints' => [],
    ])
    ->getForm();

  $form->handleRequest($request);

  if ($form->isValid()) {
    $data = $form->getData();
    $data['username'] = strtolower($data['username']);
    $data['profile_hash'] = sha1($data['username']);
    $hasProfileImage = false;

    if ($data['face_file'] && $data['face_file']->isValid()) {
      $extension = $data['face_file']->guessExtension();
      if (is_null($extension)) {
        $extension = $data['face_file']->getExtension();
      }
      $data['picture'] = $data['username'] . '.' . $extension;
      $data['face_file']->move(
        __DIR__ . '/../profiles/',
        $data['picture']
      );
      $hasProfileImage = true;
    }

    if ($data['face_url']) {
      $tmp_file = '/tmp/apply_' . uniqid();
      if (copy($data['face_url'], $tmp_file)) {
        if (exif_imagetype($tmp_file) == IMAGETYPE_GIF
          || exif_imagetype($tmp_file) == IMAGETYPE_JPEG
          || exif_imagetype($tmp_file) == IMAGETYPE_PNG
          || exif_imagetype($tmp_file) == IMAGETYPE_BMP
        ) {
          $info  = getimagesize($tmp_file);
          copy($tmp_file, __DIR__ . '/../profiles/' . $data['username'] . image_type_to_extension($info[2]));
        } else {
          unlink($tmp_file);
        }
      }
      $hasProfileImage = true;
    }

    try {
      $app['db']->executeUpdate(
        'INSERT INTO applicants (
          name, username, nickname, contact_email, list_email, bio_reason, sponsor,
          second_sponsor, picture, token_type, twitter, facebook, heard_from, profile_hash,
          website
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
          $data['name'],
          $data['username'],
          $data['nickname'],
          $data['contact_email'],
          $data['list_email'],
          $data['bio_reason'],
          $data['sponsor'],
          $data['second_sponsor'],
          $data['picture'],
          $data['token_type'],
          $data['twitter'],
          $data['facebook'],
          $data['heard_from'],
          ($hasProfileImage) ? $data['profile_hash'] : 'NULL',
          $data['website'],
        ]
      );
    } catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $exception) {
      $form->addError(new FormError('Nickname is already in use'));
      return $app['twig']->render('apply.twig', [
        'form' => $form->createView()
      ]);
    }

    $headers = 'From: Applications <applications@hacklab.to>' . "\r\n" .
      'Reply-To: applications@hacklab.to' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();

    if (!mail(
      implode(', ', $app['config']['emails']) ,
      "{$data['name']} ({$data['nickname']}) has applied!",
      $app['twig']->render('email.twig', $data),
      $headers
    )) {
      throw new Exception("Error Sending Email", 1);
    }

    // Reset for the next user
    $app['session']->set('isSharedAuthenticated', false);
    return $app->redirect('/payment');
  }

  return $app['twig']->render('apply.twig', [
    'form' => $form->createView()
  ]);
})
->method('GET|POST')
->before($sharedAuthentication)
->bind('apply');

$app->get('/payment', function () use ($app) {
  return $app['twig']->render('payment.twig');
})
->bind('payment');

$app->get('/logout', function () use ($app) {
  $app['session']->set('isSharedAuthenticated', false);
  return $app->redirect('/');
})
->bind('logout');

$app->get('/profile/{hash}', function ($hash) use ($app) {
  $user = $app['db']->fetchAssoc(
    'SELECT * FROM applicants WHERE profile_hash = ?',
    [ strtolower($hash) ]
  );
  if (!$user) {
    $app->abort(404);
  }

  $filePath = __DIR__ . '/../profiles/' . $user['picture'];
  if (!file_exists($filePath)) {
    $app->abort(404);
  }
  return $app->sendFile($filePath);
})
->assert('hash', '[A-Fa-z0-9]{40}')
->bind('profile');
