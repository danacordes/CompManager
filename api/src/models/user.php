<?php

$KEY_NAME = 'name';
$KEY_PASSWORD = 'password';
$KEY_EMAIL = 'email';

class User extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'user';

    protected $guarded = ['id'];

}
?>
