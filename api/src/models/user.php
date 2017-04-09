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
      global $COMPETITION_USER_TYPE_STEWARD;
      return User::isCompetitionRole($userId, $competitionId, $COMPETITION_USER_TYPE_STEWARD);

    }

    /*
     * @param $userId 
     * @param $comptitionId
     */
    public function isEntrant($userId, $competitionId){
      global $COMPETITION_USER_TYPE_ENTRANT;
      return User::isCompetitionRole($userId, $competitionId, $COMPETITION_USER_TYPE_ENTRANT);

    }
    

    /*
     * @param $userId 
     * @param $comptitionId
     */
    public function isJudge($userId, $competitionId){
      global $COMPETITION_USER_TYPE_JUDGE;
      return User::isCompetitionRole($userId, $competitionId, $COMPETITION_USER_TYPE_JUDGE);

    }
    

    /*
     * @param $userId 
     * @param $comptitionId
     */
    public function isCompetitionOrganizer($userId, $competitionId){
      global $COMPETITION_USER_TYPE_ORGANIZER;
      return User::isCompetitionRole($userId, $competitionId, $COMPETITION_USER_TYPE_ORGANIZER);

    }
    
    /*
     * @param $userId 
     * @param $comptitionId
     */

    public function isCompetitionRole($userId, $competitionId, $role_type){

      return in_array($role_type, User::getCompetitionRoles($userId, $competitionId));

    }
    
    public function getCompetitionRoles($userId, $competitionId){
      if(empty($userId) || empty($competitionId)){
        return [];
      }

      //pull user roles for competition
      $roles = DB::table('competitions_roles')
        ->distinct()
        ->where([
          'competition_id'  => $competitionId,
          'user_id'         => $userId
        ])
        ->get(['role_type']);

      $result = [];
      foreach($roles as $role){
        $result[] = $role->role_type;
      }
     // die(print_r($result,true));

      return $result;

    }
 
    public function isOrganizationMember($userId, $organizationId){
      global $ORGANIZATION_USER_TYPE_MEMBER;
      return User::isOrganizationRole($userId, $organizationId, $ORGANIZATION_USER_TYPE_MEMBER);

    }

    public function isOrganizationOrganizer($userId, $organizationId){
      global $ORGANIZATION_USER_TYPE_ORGANIZER;
      return User::isOrganizationRole($userId, $organizationId, $ORGANIZATION_USER_TYPE_ORGANIZER);

    }
    
    public function isOrganizationRole($userId, $organizationId, $role_type){

      return in_array($role_type, User::getOrganizationRoles($userId, $organizationId));

    }

    public function getOrganizationRoles($userId, $organizationId){
      if(empty($userId) || empty($organizationId)){
        return [];
      }

      //pull user roles for organization 
      $roles = DB::table('organizations_roles')
        ->distinct()
        ->where([
          'organization_id'  => $organizationId,
          'user_id'         => $userId
        ])
        ->get(['role_type']);

      $result = [];
      foreach($roles as $role){
        $result[] = $role->role_type;
      }
      //die(print_r($result,true));

      return $result;

    }


    public function competitionRoles(){
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
        return $this->hasMany('CompetitionRole');
      //}

    }
private function logSql(){
    DB::listen(function($sql, $bindings, $time){
      var_dump('dumping');
      var_dump($sql);
      var_dump($bindings);
      var_dump($time);
      die('sql dumped');
    });
}


    public function organizationRoles(){
      
      //$result = $this->hasMany('OrganizationRole')->where('organization_id',$organizationId)->get()->toArray();
      //die(print_r($result,true));
      //die(print_r($this->hasMany('OrganizationRole')->toSql()));

      return $this->hasMany('OrganizationRole');
    }

    public function competitions(){

      return $this->belongsToMany('Competition', 'volunteer')
        ->withTimestamps();

    }

    public function getRoles($userId, $competitionId){
      global $COMPETITION_USER_TYPE_NONE;
//die(print_r([$userId, $competitionId], true));

      if(empty($competitionId) || empty($userId)){
        //TODO proper error handling
        return $COMPETITION_USER_TYPE_NONE;
      }

      $this->find();

    }

}
?>
