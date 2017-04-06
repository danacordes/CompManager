<?php
class Competition extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'competition';

    public function styles(){

      return $this->belongsToMany('Style', 'styles_competitions');

    }

    public function entries(){
      
      return $this->hasMany('Entry');

    }

}
?>
