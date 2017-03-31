<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
// TODO: https://github.com/slimphp/Slim-Csrf

// https://github.com/bryanjhv/slim-session
$app->add(new \Slim\Middleware\Session([
  'name' => 'COMPMANAGER',
  'autorefresh' => 'true',
  'lifetime' => '1 hour',
]));

