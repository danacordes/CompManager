<?php

$app->get('/logout', function ($request, $response, $args )use ($app){

  $this->session::destroy();

});

$app->get('/validate', function ($request, $response, $args )use ($app){
 
  $session = $this->session;
//die(print_r(isset($session->user), true));

  //if we have user info stored, return it
  if(isset($session->user)){
//die(print_r($session->user, true));

    $result['user']['name'] = $session->user['name'];
    $result['user']['id'] = $session->user['id'];
    $result['user']['email'] = $session->user['email'];
    return $response->withJSON($result);

  }
  
});

$app->post('/login', function ($request, $response, $args )use ($app){

  global $KEY_EMAIL, $KEY_PASSWORD, $KEY_NAME;

  $parsedBody = $request->getParsedBody();
//die(print_r($parsedBody, true));

//die(print_r(empty($userdata[$KEY_PASSWORD]), true));

  //requires both email and password
  if(
    empty($parsedBody[$KEY_EMAIL]) ||
    empty($parsedBody[$KEY_PASSWORD])
  ){
    //throw new Exception('Email address and password are required for login.');
    return $response->withJSON(['error'=>'Email address and password are required for login.']);
  }
  
  //lookup user by email address
  $user = User::where([
      'email' => $parsedBody[$KEY_EMAIL]
    ])->first();

  //pull password hash from user, if the user exists, and confirm auth
  if(
    empty($user->password) || 
    !password_verify($parsedBody[$KEY_PASSWORD], $user->password)
  ){
    unset($user);
  }

  //user exists, but bad auth
  if(empty($user)){
    return $response->withJSON(['error'=>'Could not find user with that email address and password.']);
  }

  if(!empty($user)){
    //user is logged in!
    //remove previous user info, if present
    $session = $this->session;
    if(isset($session->user)){
      unset($session->user);
    }

    //store current user info in the session
    $session->user = $user->toArray();

//die(print_r($session->user, true));
    $result['user']['name'] = $session->user['name'];
    $result['user']['id'] = $session->user['id'];
    $result['user']['email'] = $session->user['email'];
    return $response->withJSON($result);
  }
  
});

?>
