<?php

$app->group('/style', function() use ($app) {

  $app->get('/listByCompetition', function ($request, $response, $args )use ($app){
    global $VOLUNTEER_TYPE_ORGANIZER;

    //confirm required attributes
    $competitionId = $request->getQueryParam('competitionId');

    if(empty($competitionId)){
      return $response->withJSON(['error'=>'competitionId required to list styles.']);
    }

    //force to int
    $competitionId = intval($competitionId);

    //ensure that the user is logged in
    if(!User::isAuthenticated($this)){
      return $response->withJSON(['error'=>'Anonymous cannot list styles.']);
    }

    //is this user an Organizer in this comp
    $isCompetitionOrganizer = false;
    $currentUserId = User::getCurrentUserId($this);
    $roles = User::find($currentUserId)->roles->toArray();
    foreach($roles as $role){

      if(
        $role['competition_id'] === $competitionId &&
        $role['volunteer_type'] === $VOLUNTEER_TYPE_ORGANIZER
      ){
        $isCompetitionOrganizer = true;
      }
    }

    if(!$isCompetitionOrganizer){
      return $response->withJSON(['error'=>'Can only list entries if competition organizer.']);
    }

    $result['style'] = Style::all();//where([
//      'competition_id' => $competitionId,
//    ])->get();

    return $response->withJSON($result);
  });

});
?>
