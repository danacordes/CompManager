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

  $passwordValidator  = v::length(6, 90);
  $emailValidator     = v::email();
  $nameValidator      = v::length(6, 255);
  $clubValidator      = v::optional(v::length(1, 255));
  $addressValidator   = v::optional(v::length(1, 255));
  $stateValidator     = v::optional(v::length(1, 5));
  $ids                = v::optional(v::intVal());

  $registerValidator = array(
    'password'        => $passwordValidator,
    'email'           => $emailValidator,
    'name'            => $nameValidator,
    'club'            => $clubValidator,
    'address_1'       => $addressValidator,
    'address_2'       => $addressValidator,
    'city'            => $addressValidator,
    'state'           => $stateValidator,
    'zip'             => $stateValidator,
    'competition_id'  => $ids,
    'organization_id' => $ids,
  );

  $app->post('/register', function ($request, $response, $args )use ($app){

    if($request->getAttribute('has_errors')){
      return $response->withJSON(['error' => $request->getAttribute('errors')]);
    }
//die(print_r($request->getAttribute('errors'), true));
//die(print_r($parsedBody, true));

    $params = $request->getQueryParams();
    //TODO: throw validation exceptions
    //TODO: implement org, comp, etc, associations
    $userData = [];

    //required fields
    $userData['name']       = $params['name']; 
    $userData['email']      = $params['email']; 
    $userData['password']   = password_hash($params['password'], PASSWORD_DEFAULT); 
    //optional fields
    if(!empty($params['club']))       $userData['club']       = $params['club']; 
    if(!empty($params['address_1']))  $userData['address_1']  = $params['address_1']; 
    if(!empty($params['address_2']))  $userData['address_2']  = $params['address_2']; 
    if(!empty($params['city']))       $userData['city']       = $params['city']; 
    if(!empty($params['state']))      $userData['state']      = $params['state']; 
    if(!empty($params['zip']))        $userData['zip']        = $params['zip']; 

    //confirm uniqueness of email address
    if(!User::isEmailUnused($userData['email'])){
      return $response->withJSON(['error' => "The email address '{$userData['email']}' has already been registered.  If you've had one to many and can't remember you password, please hit the 'Reset Password' button."]);
    }
//die(print_r($userData, true));
    
    $user = User::create($userData);

    if(!empty($user)){
      $result['user'] = [$user];
    }

    return $response->withJSON($result);
    
  })->add(new \DavidePastore\Slim\Validation\Validation($registerValidator));

});
?>
