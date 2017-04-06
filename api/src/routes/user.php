<?php

$app->group('/user', function() use ($app) {

  $app->get('/get', function ($request, $response, $args )use ($app){
    
    $user_id = $request->getQueryParam('id');

    if(!empty($user_id)){
      $user = User::find($user_id);
      if(!empty($user)){
        $result['user'] = $user;
      }
    }

    if(!empty($result)){
      return $response->withJSON($result);
    }

  });

  $app->post('/register', function ($request, $response, $args )use ($app){

    $parsedBody = $request->getParsedBody();
//die(print_r($parsedBody, true));
    //TODO: validate user uniqueness
    //TODO: throw validation exceptions
    //TODO: implement rest of user field
    //TODO: implement org, comp, etc, associations
    $userData = [];

    $userData['name'] = $parsedBody['name']; 
    $userData['email'] = $parsedBody['email']; 
    $userData['password'] = password_hash($parsedBody['password'], PASSWORD_DEFAULT); 
    
//die(print_r($userData, true));
    
    $user = User::create($userData);

    if(!empty($user)){
      $result['user'] = [$user];
      return $response->withJSON($result);
    }
    
  });

});
?>
