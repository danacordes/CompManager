<?php

use Illuminate\Database\Capsule\Manager as DB;

class User extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'user';

    protected $guarded = ['id'];

    public function isAuthenticated($context){

      $session = $context->session;
      return (!empty($session) && !empty($session->user));

    }

    public function getCurrentUserId($context){

      $session = $context->session;
      if(User::isAuthenticated($context)){
        return $session->user['id'];
      }

    }

    public function isEmailUnused($email){

      return 0 === DB::table('user')
        ->where('email', $email)
        ->count();
      
    }
     
    /*
     * @param $userId 
     * @param $comptitionId
     */
    public function isSteward($userId, $competitionId){

      return in_array($VOLUNTEER_TYPE_STEWARD, User::getUserRoles($userId, $competitionId));

    }
    


    /*
     * @param $userId 
     * @param $comptitionId
     */
    public function isEntrant($userId, $competitionId){

      return in_array($VOLUNTEER_TYPE_ENTRANT, User::getUserRoles($userId, $competitionId));

    }
    

    /*
     * @param $userId 
     * @param $comptitionId
     */
    public function isJudge($userId, $competitionId){

      return in_array($VOLUNTEER_TYPE_JUDGE, User::getUserRoles($userId, $competitionId));
    }
    

    /*
     * @param $userId 
     * @param $comptitionId
     */
    public function isOrganizer($userId, $competitionId){

      return in_array($VOLUNTEER_TYPE_ORGANIZER, User::getUserRoles($userId, $competitionId));
    }
    
    /*
     * @param $userId 
     * @param $comptitionId
     */

    public function getUserRoles($userId, $competitionId){
      if(empty($userId) || empty($competitionId)){
        return [];
      }

      //pull user roles for competition
      $roles = DB::table('competition_role')
        ->distinct()
        ->where([
          'competition_id'  => $competitionId,
          'user_id'         => $userId
        ])
        ->get(['volunteer_type']);

      $result = [];
      foreach($roles as $role){
        $result[] = $role->volunteer_type;
      }
     // die(print_r($result,true));

      return $result;

    }

    public function roles(){
    //public function roles($competitionId){
      //$competitionId = func_get_arg(0);
      //print_r($competitionId);
      //die($competitionId.'');
      //die(print_r(func_get_arg(0),true));
      //die(print_r(func_get_args(),true));

      //if(isset($competitionId)){
      //  return $this->hasMany('Role')
      //    ->where(['role.competition_id' => $competitionId]);
      //} else {
      //  die(print_r($this->hasMany('Role'),true));
        return $this->hasMany('Role');
      //}

    }

    public function competitions(){

      return $this->belongsToMany('Competition', 'volunteer')
        ->withTimestamps();

    }

    public function getRoles($userId, $competitionId){
      global $VOLUNTEER_TYPE_NONE;
//die(print_r([$userId, $competitionId], true));

      if(empty($competitionId) || empty($userId)){
        //TODO proper error handling
        return $VOLUNTEER_TYPE_NONE;
      }

      $this->find();

    }

}
?>
