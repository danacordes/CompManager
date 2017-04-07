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
