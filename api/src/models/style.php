<?php
class Style extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'style';

    public function competitions(){

      return $this->belongsToMany('Competition', 'styles_competitions');

    } 

}
?>
