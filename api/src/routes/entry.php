<?php

$app->group('/entry', function() use ($app) {
  global $VALIDATORS;

  $app->get('/listByCompetition', function ($request, $response, $args )use ($app){

    //confirm required attributes
    $competitionId = $request->getQueryParam('competitionId');

    if(empty($competitionId)){
      return $response->withJSON(['error'=>'competitionId required to list entries.']);
    }

    //force to int
    $competitionId = intval($competitionId);

    //ensure that the user is logged in
    if(!User::isAuthenticated($this)){
      return $response->withJSON(['error'=>'Anonymous cannot list entries.']);
    }


    //is this user an Organizer in this comp
    $currentUserId = User::getCurrentUserId($this);
    die(print_r(User::find($currentUserId)->organizationRoles()->get()->toArray(), true));;
    $isCompetitionOrganizer = User::isOrganizer($currentUserId, $competitionId);

    if(!$isCompetitionOrganizer){
      return $response->withJSON(['error'=>'Can only list entries if competition organizer.']);
    }

    $result['entry'] = Entry::where([
      'competition_id' => $competitionId,
    ])->get();

    return $response->withJSON($result);
  });

  $app->get('/listByCompetitionStyle', function ($request, $response, $args )use ($app){

    //confirm required attributes
    $competitionId = $request->getQueryParam('competitionId');
    $styleId = $request->getQueryParam('styleId');

    if(empty($competitionId)){
      return $response->withJSON(['error'=>'competitionId required to list entries.']);
    }
    if(empty($styleId)){
      return $response->withJSON(['error'=>'styleId required to list entries.']);
    }

    //force to int
    $competitionId = intval($competitionId);

    //force to array
    $styleIds = explode(',',$styleId);

    //ensure that all are ints
    array_walk($styleIds, function(&$style){ 
      $style = intval($style); 
    });

    //remove id zero from the array, these were non-int numbers from the
    //previous step
    $styleIds = array_diff($styleIds, [0]);

    //is this user an Organizer in this comp
    $currentUserId = User::getCurrentUserId($this);
    $isCompetitionOrganizer = User::isOrganizer($currentUserId, $competitionId);
    if(!$isCompetitionOrganizer){
      return $response->withJSON(['error'=>'Can only list entries if competition organizer.']);
    }

//die(print_r($styleIds, true));
    $result['entry'] = Entry::where([
      'competition_id' => $competitionId,
    ])
    ->whereIn('style_id', $styleIds)
    ->get();

    return $response->withJSON($result);
  });


  $app->get('/listByCompetitionUser', function ($request, $response, $args )use ($app){

    //confirm required attributes
    $userId = $request->getQueryParam('userId');
    $competitionId = $request->getQueryParam('competitionId');

    if(empty($userId)){
      return $response->withJSON(['error'=>'userId required to list entries.']);
    }
    if(empty($competitionId)){
      return $response->withJSON(['error'=>'competitionId required to list entries.']);
    }

    //force to int
    $userId = intval($userId);
    $competitionId = intval($competitionId);

    $isCurrentUser = false;
    $currentUserId = -1;
    //$session = $this->session;
    //die(print_r(empty($session->user), true).'!');
    //die(print_r(User::isAuthenticated($this), true).'!');
    if(User::isAuthenticated($this)){
      $currentUserId = User::getCurrentUserId($this);
      if($currentUserId === $userId){
        $isCurrentUser = true;
      }
    } else {
      return $response->withJSON(['error'=>'Anonymous cannot list entries.']);
    }


    //is this user an Organizer in this comp
    $currentUserId = User::getCurrentUserId($this);
    $isCompetitionOrganizer = User::isOrganizer($currentUserId, $competitionId);

    //die(print_r([$currentUser, $isCompetitionOrganizer, $isCurrentUser],true));

    //$this->db::enableQueryLog();
    //$this->db::getQueryLog();

    if(!$isCompetitionOrganizer && !$isCurrentUser){
      return $response->withJSON(['error'=>'Can only list entries for current user, or if competition organizer.']);
    }

    $result['entry'] = Entry::where([
      'user_id' => $userId,
      'competition_id' => $competitionId,
    ])->get();

    return $response->withJSON($result);
  });


  $registerEntryValidator = array(
    'competition_id'  => $VALIDATORS['id'],
    'style_id'        => $VALIDATORS['id'],
    'name'            => $VALIDATORS['name'],
  );

  $app->post('/register', function ($request, $response, $args )use ($app){
    //confirm auth
    //confirm user is member of compeititon, if not, add them as entrant
    global $COMPETITION_USER_TYPES;

    if($request->getAttribute('has_errors')){
      return $response->withJSON(['error' => $request->getAttribute('errors')]);
    }

    if(!User::isAuthenticated($this)){
      return $response->withJSON(['error' => "Can only add entries if logged in."]);
    }

    $params = $request->getParsedBody();
    $entryData = [];

    //required header fields
    $entryData['user_id']         = User::getCurrentUserId($this); 
    $entryData['competition_id']  = (int)$params['competition_id']; 
    $entryData['style_id']        = (int)$params['style_id']; 
    $entryData['name']            = $params['name']; 

    /*
    if(!User::isEmailUnused($entryData['email'])){
      return $response->withJSON(['error' => "The email address '{$entryData['email']}' has already been registered.  If you've had one to many and can't remember you password, please hit the 'Reset Password' button."]);
    }
     */
    
    //create the entry 
    $entry = Entry::create($entryData);
    if(!empty($entry)){
      $result['entry'] = [$entry];
    }
 
    //TODO get style_type_id based on style_id
    //TODO get entry_attributes for style_type
    //TODO add params for required fields, error if missing
    //TODO add params for optional fields, if present
    //required fields
    //optional fields
    //if(!empty($params['zip']))        $entryData['zip']        = $params['zip']; 


    //TODO: validate advanced (EAV) values and build create objects
    //TODO: execute creates
    //TODO: return new Entry

    return $response->withJSON($result);
    
  })->add(new \DavidePastore\Slim\Validation\Validation($registerEntryValidator));


});
?>
<?php
