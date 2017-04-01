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

    $competitionId = $request->getQueryParam('competitionId');

    if(empty($competitionId)){
      return $response->withJSON(['error'=>'competitionId required to list entries.']);
    }

    $competitionId = intval($competitionId);

    $result['entry'] = Entry::where([
      'competition_id' => $competitionId
    ])->get();

    return $response->withJSON($result);
  });

  $app->get('/listByCompetitionUser', function ($request, $response, $args )use ($app){
    global $VOLUNTEER_TYPE_ORGANIZER;

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
    $currentUser = -1;
    $session = $this->session;
    if(isset($session->user)){
      $currentUser = $session->user['id'];
      if($currentUser === $userId){
        $isCurrentUser = true;
      }
    }


    //is this user an Organizer in this comp
    $isCompetitionOrganizer = false;
    $roles = User::find($currentUser)->roles->toArray();
    foreach($roles as $role){

      if(
        $role['competition_id'] === $competitionId &&
        $role['volunteer_type'] === $VOLUNTEER_TYPE_ORGANIZER
      ){
        $isCompetitionOrganizer = true;
      }
    }

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
