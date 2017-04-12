<?php

use Respect\Validation\Validator as v;

$KEY_NAME = 'name';
$KEY_PASSWORD = 'password';
$KEY_EMAIL = 'email';

$COMPETITION_USER_TYPE_NONE      = -1;
$COMPETITION_USER_TYPE_ORGANIZER = 0;
$COMPETITION_USER_TYPE_ENTRANT   = 1;
$COMPETITION_USER_TYPE_JUDGE     = 2;
$COMPETITION_USER_TYPE_STEWARD   = 3; 
$COMPETITION_USER_TYPES = [ 
  $COMPETITION_USER_TYPE_ORGANIZER => 'Organizer',
  $COMPETITION_USER_TYPE_ENTRANT   => 'Entrant',
  $COMPETITION_USER_TYPE_JUDGE     => 'Judge',
  $COMPETITION_USER_TYPE_STEWARD   => 'Steward',
];

$ORGANIZATION_USER_TYPE_NONE      = -1;
$ORGANIZATION_USER_TYPE_ORGANIZER = 0;
$ORGANIZATION_USER_TYPE_MEMBER    = 1;
$ORGANIZATION_USER_TYPES = [ 
  $ORGANIZATION_USER_TYPE_ORGANIZER => 'Organizer',
  $ORGANIZATION_USER_TYPE_MEMBER    => 'Entrant',
];

$VALIDATORS = [
  'password'    => v::length(6, 90),
  'email'       => v::email(),
  'name'        => v::length(6, 255),
  'club'        => v::optional(v::length(1, 255)),
  'address'     => v::optional(v::length(1, 255)),
  'state'       => v::optional(v::length(1, 5)),
  'id'          => v::intVal(),
  'opt-id'      => v::optional(v::intVal()),
  'boolean'     => v::intVal()->max(1)->min(0),
  'opt-boolean' => v::optional(v::intVal()->max(1)->min(0)),
];

?>
