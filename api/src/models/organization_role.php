<?php
class OrganizationRole extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'organizations_roles';

    protected $guarded = ['id'];

    public function getRoleName($role_type){
      global $ORGANIZATION_USER_TYPES;
      return $ORGANIZATION_USER_TYPES[$role_type];
    }

}
?>
