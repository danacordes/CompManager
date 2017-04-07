<?php

use Respect\Validation\Validator as v;

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

  $passwordValidator  = v::length(6 ,20);
  $emailValidator     = v::email();
  $nameValidator      = v::length(6 ,50);
  $validator = array(
    'password'  => $passwordValidator,
    'email'     => $emailValidator,
    'name'      => $nameValidator
  );

  $app->post('/register', function ($request, $response, $args )use ($app){

    if($request->getAttribute('has_errors')){
      return $response->withJSON(['error' => $request->getAttribute('errors')]);
    }
//die(print_r($request->getAttribute('errors'), true));
//die(print_r($parsedBody, true));

    $params = $request->getQueryParams();
    //TODO: throw validation exceptions
    //TODO: implement rest of user field
    //TODO: implement org, comp, etc, associations
    $userData = [];

    $userData['name'] = $params['name']; 
    $userData['email'] = $params['email']; 
    $userData['password'] = password_hash($params['password'], PASSWORD_DEFAULT); 

    //confirm uniqueness of email address
    if(!User::isEmailUnused($userData['email'])){
      return $response->withJSON(['error' => "The email address '{$userData['email']}' has already been registered.  If you've had one to many and can't remember you password, please hit the 'Reset Password' button."]);
    }
die(print_r($userData, true));
    
    $user = User::create($userData);

    if(!empty($user)){
      $result['user'] = [$user];
    }

    return $response->withJSON($result);
    
  })->add(new \DavidePastore\Slim\Validation\Validation($validator));

});
?>
