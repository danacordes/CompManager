<?php

$app->group('/entry', function() use ($app) {

  $app->get('/list', function ($request, $response, $args )use ($app){

    $result = [];
//die(print_r($this->get('db'), true));
//die(print_r($this->db, true));
    //$entries = $this->get('db')->table('entries')->find(1);
    $result['entry'] = Entry::all();
/*
    $stmt = $this->db->query('SELECT * FROM entries');
    while($row = $stmt->fetch()){
     $entries[] = $row; 
    }
*/

    return $response->withJSON($result);
  });

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

    //is this request for the currently logged in user
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

});
?>
