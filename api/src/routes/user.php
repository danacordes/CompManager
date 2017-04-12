<?php

$app->group('/user', function() use ($app) {
  global $VALIDATORS;

  $getUserValidator = [
    'user_id' => $VALIDATORS['id'],
  ];
  $app->get('/getUser', function ($request, $response, $args )use ($app){
    
    if($request->getAttribute('has_errors')){
      return $response->withJSON(['error' => $request->getAttribute('errors')]);
    }

    $userId = (int)$request->getQueryParam('user_id');

    if(isset($userId)){
      if(
        $userId == User::getCurrentUserId($this) || //can request yourself
       User::canReadUserInfo($this, $userId) 
      ){
//die(print_r(User::getCurrentUserId($this), true));
        $user = User::find($userId);
        if(!empty($user)){
          $result['user'] = User::filterFields($user);
        }
      } else {
        return $response->withJSON(['error' => 'You lack the permissions needed to request this user info.']);
      }
    }

    if(!empty($result)){
      return $response->withJSON($result);
    }

  })->add(new \DavidePastore\Slim\Validation\Validation($getUserValidator));

  $registerValidator = array(
    'password'        => $VALIDATORS['password'],
    'email'           => $VALIDATORS['email'],
    'name'            => $VALIDATORS['name'],
    'club'            => $VALIDATORS['club'],
    'address_1'       => $VALIDATORS['address'],
    'address_2'       => $VALIDATORS['address'],
    'city'            => $VALIDATORS['address'],
    'state'           => $VALIDATORS['state'],
    'zip'             => $VALIDATORS['state'],
    'competition_id'  => $VALIDATORS['opt-id'],
    'organization_id' => $VALIDATORS['opt-id'],
   // 'role_types'      => $ids,
  );

  $app->post('/register', function ($request, $response, $args )use ($app){
    global $COMPETITION_USER_TYPES;

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
    
    //create the user
    $user = User::create($userData);

    //if requested, set up a competition role
    if(!empty($params['competition_id']) && isset($params['role_types'])){
      //parse out the requested roles
      $role_types = explode(',',$params['role_types']);
      //compare the list to the roles supported by the system
      $role_types = array_intersect($role_types,array_keys($COMPETITION_USER_TYPES));
      
      //set up array for role creation
      $competitionRole['user_id'] = $user['id'];
      $competitionRole['competition_id'] = $params['competition_id'];

      //iterate over the requested roles, adding them
      foreach($role_types as $role_type){
        $competitionRole['role_type'] = $role_type; 
//die(print_r($competitionRole, true));
        CompetitionRole::create($competitionRole);
      }
    }

    if(!empty($user)){
      $result['user'] = [$user];
    }

    return $response->withJSON($result);
    
  })->add(new \DavidePastore\Slim\Validation\Validation($registerValidator));

});
?>
