<?php
class CompetitionRole extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'competitions_roles';

    protected $guarded = ['id'];
    
    public function getRoleName($role_type){
      global $COMPETITION_USER_TYPES;
      return $COMPETITION_USER_TYPES[$role_type];
    }

}
?>
