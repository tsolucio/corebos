<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : EntittyLog
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/

global $currentModule, $rstart;

$focus = CRMEntity::getInstance($currentModule);

$idlist= vtlib_purify($_REQUEST['massedit_recordids']);
$viewid = vtlib_purify($_REQUEST['viewname']);
$return_module = vtlib_purify($_REQUEST['massedit_module']);
$return_action = 'index';

//Added to fix 4600
$url = getBasic_Advance_SearchURL();

if(isset($_REQUEST['start']) && $_REQUEST['start']!=''){
	$rstart = "&start=".vtlib_purify($_REQUEST['start']);
}

if(isset($idlist)) {
	$recordids = explode(';', $idlist);
	for($index = 0; $index < count($recordids); ++$index) {
		$recordid = $recordids[$index];
		if($recordid == '') continue;
		if(isPermitted($currentModule,'EditView',$recordid) == 'yes') {
			// Save each module record with update value.
			$focus->retrieve_entity_info($recordid, $currentModule);
			$focus->mode = 'edit';		
			$focus->id = $recordid;		
			foreach($focus->column_fields as $fieldname => $val)
			{    	
				if(isset($_REQUEST[$fieldname."_mass_edit_check"])) {
					if($fieldname == 'assigned_user_id'){
						if($_REQUEST['assigntype'] == 'U')  {
							$value = $_REQUEST['assigned_user_id'];
						} elseif($_REQUEST['assigntype'] == 'T') {
							$value = $_REQUEST['assigned_group_id'];
						}
					} else {
						if(is_array($_REQUEST[$fieldname]))
							$value = $_REQUEST[$fieldname];
						else
							$value = trim($_REQUEST[$fieldname]);
					}
					$focus->column_fields[$fieldname] = $value;
				}
				else {
					$focus->column_fields[$fieldname] = decode_html($focus->column_fields[$fieldname]);
				}
			}
	   		$focus->save($currentModule);
		}
	}
}

$parenttab = getParentTab();
header("Location: index.php?module=$return_module&action=$return_action&parenttab=$parenttab$rstart");
?>