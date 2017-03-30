<?php

$app->group('/entries', function() use ($app) {

  $app->get('/list', function ($request, $response, $args )use ($app){

    $result = [];
//die(print_r($this->get('db'), true));
//die(print_r($this->db, true));
    //$entries = $this->get('db')->table('entries')->find(1);
    $result['entries'] = Entry::all();
/*
    $stmt = $this->db->query('SELECT * FROM entries');
    while($row = $stmt->fetch()){
     $entries[] = $row; 
    }
*/

    return $response->withJSON($result);
  });

});
?>
