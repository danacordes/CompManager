<?php

use Illuminate\Database\Capsule\Manager as DB;

$app->group('/style', function() use ($app) {

  $app->get('/listCountByCompetition', function ($request, $response, $args )use ($app){
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

    //get the style entry counts for the comp
    $entryCountsRaw = DB::select("SELECT style_id, count(*) as count FROM entry WHERE competition_id = $competitionId GROUP BY style_id");

    //convert them to a usable data structure
    $entryCounts = [];
    foreach($entryCountsRaw as $count){
      $entryCounts[$count->style_id] = $count->count;
    }
    unset($entryCountsRaw);

    //list all the styles in the competition
    $competition = Competition::find($competitionId);
    $competitionStyles = $competition->styles;

    //iterate through them, adding in the count
    foreach($competitionStyles as $style){
      $count = $entryCounts[$style->id];
      $style->count = isset($count)?$count:0;
    }

    $result['style'] = $competitionStyles;

    return $response->withJSON($result);
  });

});
?>
