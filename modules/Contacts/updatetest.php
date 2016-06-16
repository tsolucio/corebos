<?php 
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

require_once('include/database/PearDatabase.php');
include_once('data/CRMEntity.php');
require_once('include/utils/utils.php');

function updatetest($entity){
	global $log,$adb,$current_user;
	$orig_pot = $entity->getId();
    $log->debug("Entered in wfwithminutes with orig_pot value is: ".$orig_pot);
    $orig_id = explode('x',$orig_pot);
    $orig_id = $orig_id[1];
     if($orig_id=='1084'){
    $focusnew = CRMEntity::getInstance("Contacts");
    $focusnew->retrieve_entity_info($orig_id,"Contacts");
    $focusnew->id=$orig_id;
    $focusnew->mode='edit';
    $focusnew->column_fields['lastname']='test';
    $focusnew->column_fields['assigned_user_id']=$current_user->id;
    $focusnew->save("Contacts");
    }

}
?>