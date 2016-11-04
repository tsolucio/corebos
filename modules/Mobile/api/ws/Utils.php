<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
class Mobile_WS_Utils {

	static function initAppGlobals() {
		global $current_language, $app_strings, $app_list_strings, $app_currency_strings;
		$app_currency_strings = return_app_currency_strings_language($current_language);
		$app_strings = return_application_language($current_language);
		$app_list_strings = return_app_list_strings_language($current_language);
	}

	static function initModuleGlobals($module) {
		global $mod_strings, $current_language;
		if ($module == 'Events') {
			$module = 'Calendar';
		}
		if(isset($current_language)) {
			@include("modules/$module/language/$current_language.lang.php");
		}
	}
	
	static function getVtigerVersion() {
		global $vtiger_current_version;
		return $vtiger_current_version;
	}
	
	static function getVersion() {
		global $adb;
		$versionResult = $adb->pquery("SELECT version FROM vtiger_tab WHERE name='Mobile'", array());
		return $adb->query_result($versionResult, 0, 'version');
	}
	
	static function array_replace($search, $replace, $array) {
		$index = array_search($search, $array);
		if($index !== false) {
			$array[$index] = $replace;
		}
		return $array;
	}
	
	static function getModuleListQuery($moduleName, $where = '1=1') {
		$module = CRMEntity::getInstance($moduleName);
		return $module->create_list_query('', $where);
	}
	
	static $moduleWSIdCache = array();
	
	static function getEntityModuleWSId($moduleName) {
		
		if (!isset(self::$moduleWSIdCache[$moduleName])) {
			global $adb;
			$result = $adb->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($moduleName));
			if ($result && $adb->num_rows($result)) {
				self::$moduleWSIdCache[$moduleName] = $adb->query_result($result, 0, 'id');
			}
		}
		return self::$moduleWSIdCache[$moduleName];
	}
	
	static function getEntityModuleWSIds($ignoreNonModule = true) {
		global $adb;
		
		$modulewsids = array();
		$result = false;
		if($ignoreNonModule) {
			$result = $adb->pquery("SELECT id, name FROM vtiger_ws_entity WHERE ismodule=1", array());
		} else {
			$result = $adb->pquery("SELECT id, name FROM vtiger_ws_entity", array());
		}
		
		while($resultrow = $adb->fetch_array($result)) {
			$modulewsids[$resultrow['name']] = $resultrow['id'];
		}
		return $modulewsids;
	}
	
	static function getEntityFieldnames($module) {
		global $adb;
		$result = $adb->pquery("SELECT fieldname FROM vtiger_entityname WHERE modulename=?", array($module));
		$fieldnames = array();
		if($result && $adb->num_rows($result)) {
			$fieldnames = explode(',', $adb->query_result($result, 0, 'fieldname'));
		}
		switch($module) {
			case 'HelpDesk': $fieldnames = self::array_replace('title', 'ticket_title', $fieldnames); break;
			case 'Document': $fieldnames = self::array_replace('title', 'notes_title', $fieldnames); break;
		}
		return $fieldnames;
	}
	
	static function getModuleColumnTableByFieldNames($module, $fieldnames) {
		global $adb;
		$result = $adb->pquery("SELECT fieldname,columnname,tablename FROM vtiger_field WHERE tabid=? AND fieldname IN (".
			generateQuestionMarks($fieldnames) . ")", array(getTabid($module), $fieldnames)
		);
		$columnnames = array();
		if ($result && $adb->num_rows($result)) {
			while($resultrow = $adb->fetch_array($result)) {
				$columnnames[$resultrow['fieldname']] = array('column' => $resultrow['columnname'], 'table' => $resultrow['tablename']);
			}
		}
		return $columnnames;
	}
	
	static function detectModulenameFromRecordId($wsrecordid) {
		global $adb;
		$idComponents = vtws_getIdComponents($wsrecordid);
		$result = $adb->pquery("SELECT name FROM vtiger_ws_entity WHERE id=?", array($idComponents[0]));
		if($result && $adb->num_rows($result)) {
			return $adb->query_result($result, 0, 'name');
		}
		return false;
	}
	
	static $detectFieldnamesToResolveCache = array();
	
	static function detectFieldnamesToResolve($module) {
		global $adb;
		
		// Cache hit?
		if(isset(self::$detectFieldnamesToResolveCache[$module])) {
			return self::$detectFieldnamesToResolveCache[$module];
		}
		
		$resolveUITypes = array(10, 101, 116, 117, 26, 357, 50, 51, 52, 53, 57, 59, 66, 68, 73, 75, 76, 77, 78, 80, 81);
		
		$result = $adb->pquery(
			"SELECT fieldname FROM vtiger_field WHERE uitype IN(". 
			generateQuestionMarks($resolveUITypes) .") AND tabid=?", array($resolveUITypes, getTabid($module)) 
		);
		$fieldnames = array();
		while($resultrow = $adb->fetch_array($result)) {
			$fieldnames[] = $resultrow['fieldname'];
		}
		
		// Cache information		
		self::$detectFieldnamesToResolveCache[$module] = $fieldnames;
		
		return $fieldnames;
	}

	static $gatherModuleFieldGroupInfoCache = array();
	
	static function gatherModuleFieldGroupInfo($module) {
		global $adb,$mod_strings, $current_language;
		$current_language = Mobile_WS_Controller::sessionGet('language') ;
		self::initModuleGlobals($module);
		// Cache hit?
		if(isset(self::$gatherModuleFieldGroupInfoCache[$module])) {
			return self::$gatherModuleFieldGroupInfoCache[$module];
		}
		if ($module != 'Calendar') {
			$result = $adb->pquery(
				"SELECT fieldname, fieldlabel, blocklabel, uitype, typeofdata FROM vtiger_field INNER JOIN
				vtiger_blocks ON vtiger_blocks.tabid=vtiger_field.tabid AND vtiger_blocks.blockid=vtiger_field.block 
				WHERE vtiger_field.tabid=? AND vtiger_field.presence != 1 AND vtiger_field.tablename !='vtiger_ticketcomments'  ORDER BY vtiger_blocks.sequence, vtiger_field.sequence", array(getTabid($module))
			);
		}
		else {
			$result = $adb->pquery(
				"SELECT fieldname, fieldlabel, blocklabel, uitype, typeofdata FROM vtiger_field INNER JOIN
				vtiger_blocks ON vtiger_blocks.tabid=vtiger_field.tabid AND vtiger_blocks.blockid=vtiger_field.block 
				WHERE vtiger_field.tabid=? AND vtiger_field.presence != 1 and fieldname != 'eventstatus' and fieldname !=  'activitytype' ORDER BY vtiger_blocks.sequence, vtiger_field.sequence", array(getTabid($module))
			);
		}

		$fieldgroups = array();
		while($resultrow = $adb->fetch_array($result)) {
			if (array_key_exists ($resultrow['blocklabel'], $mod_strings)) {
				$blocklabel = $mod_strings[$resultrow['blocklabel']];
			}
			else {
				$blocklabel = ($resultrow['blocklabel']);
			}
			if (array_key_exists ($resultrow['fieldlabel'], $mod_strings)) {
				$fieldlabel = $mod_strings[$resultrow['fieldlabel']];
			}
			else {
				$fieldlabel = ($resultrow['fieldlabel']);
			}
			if(!isset($fieldgroups[$blocklabel])) {
				$fieldgroups[$blocklabel] = array();
			}
			$fieldgroups[$blocklabel][$resultrow['fieldname']] = 
				array(
					'label' => $fieldlabel,
					'uitype'=> self::fixUIType($module, $resultrow['fieldname'], $resultrow['uitype']),
					'typeofdata'=>self::getMandatory ($resultrow['typeofdata'])
				);
		}
		
		// Cache information
		self::$gatherModuleFieldGroupInfoCache[$module] = $fieldgroups;
		
		return $fieldgroups;
	}
	
	static function documentFoldersInfo() {
		global $adb;
		$folders = $adb->pquery("SELECT folderid, foldername FROM vtiger_attachmentsfolder", array());
		$folderOptions = array();
		while( $folderrow = $adb->fetch_array($folders) ) {
			$folderwsid = sprintf("%sx%s", self::getEntityModuleWSId('DocumentFolders'), $folderrow['folderid']);
			$folderOptions[] = array( 'value' => $folderwsid, 'label' => $folderrow['foldername'] );
		} 
		return $folderOptions;
	}
	
	static function salutationValues() {
		$values = vtlib_getPicklistValues('salutationtype');
		$options = array();
		foreach($values as $value) {
			$options[] = array( 'value' => $value, 'label' => $value);
		}
		return $options;
	}

	static function getassignedtoValues($userObj,$assigned_user_id='') {
		//get users info
		$recordprefix= self::getEntityModuleWSId('Users') ;
		if ($assigned_user_id=='') {
			$assigned_user_id = $recordprefix.'x'.$userObj->id;
		}
	    if ($userObj->is_admin==false) {
			$resultuser =get_user_array(FALSE, "Active", $assigned_user_id,'private');
		}
		else { 
			$resultuser =get_user_array(FALSE, "Active", $assigned_user_id);
		}

		//add prefix to key
		$data = array_flip($resultuser);
		foreach($data as $key => &$val) { 
			$val = $recordprefix.'x'.$val; 
		}
		$resultuser = array_flip($data);
		$users_combo = get_select_options_array($resultuser, $assigned_user_id);
		//handle groups
		$resultgroups= array();
		if ($userObj->is_admin==false) {
			$resultgroups=vtws_getUserAccessibleGroups ($module, $userObj);
		}
		else {
			$resultgroups=vtws_getUserAccessibleGroups ($module, $userObj);
		}
		//add prefix to key for groups
		$groups_combo = array();
		if (count($resultgroups) > 0) {
			$newgrouporder = array ();
			foreach($resultgroups as $key => &$val) { 
				$newid = $recordprefix.'x'.$val['id'];
				$newgrouporder[$newid] = $val['name'];
			}
			$groups_combo = get_select_options_array($newgrouporder, $assigned_user_id);
		}
		$fieldvalue = array();
		$fieldvalue[]=$users_combo;
		$fieldvalue[] =$groups_combo;
		return $fieldvalue;
	}
	
	static function visibilityValues() {
		$options = array();
		// Avoid translation for these picklist values.
		$options[] = array ('value' => 'Private', 'label' => 'Private');
		$options[] = array ('value' => 'Public', 'label' => 'Public');		
		return $options;
	}
	
	static function fixUIType($module, $fieldname, $uitype) {
		if ($module == 'Contacts' || $module == 'Leads') {
			if ($fieldname == 'salutationtype') {
				return 16;
			}
		}
		else if ($module == 'Calendar' || $module == 'Events') {
			if ($fieldname == 'time_start' || $fieldname == 'time_end') {
				// Special type for mandatory time type (not defined in product)
				return 252;
			}
		}
		return $uitype;
	}
	
	static function fixDescribeFieldInfo($module, &$describeInfo,$current_user) {
		//assigned to field settings
		foreach($describeInfo['fields'] as $index => $fieldInfo) {
			if ($fieldInfo['name'] == 'assigned_user_id') {
				$picklistValues = self::getassignedtoValues($current_user);
				$fieldInfo['type']['name'] = 'picklist';
				$fieldInfo['type']['picklistValues'] = $picklistValues;
				//$fieldInfo['type']['defaultValue'] = $picklistValues[0];
				$describeInfo['fields'][$index] = $fieldInfo;
			}
		}
		
		if ($module == 'Leads' || $module == 'Contacts') {
			foreach($describeInfo['fields'] as $index => $fieldInfo) {
				if ($fieldInfo['name'] == 'salutationtype') {
					$picklistValues = self::salutationValues();
					$fieldInfo['uitype'] = self::fixUIType($module, $fieldInfo['name'], $fieldInfo['uitype']) ;
					$fieldInfo['type']['name'] = 'picklist';
					$fieldInfo['type']['picklistValues'] = $picklistValues;
					//$fieldInfo['type']['defaultValue'] = $picklistValues[0];
					
					$describeInfo['fields'][$index] = $fieldInfo;
				}
			}
		}		
		else if ($module == 'Documents') {
			foreach($describeInfo['fields'] as $index => $fieldInfo) {
				if ($fieldInfo['name'] == 'folderid') {
					$picklistValues = self::documentFoldersInfo();
					$fieldInfo['type']['picklistValues'] = $picklistValues;
					//$fieldInfo['type']['defaultValue'] = $picklistValues[0];
					
					$describeInfo['fields'][$index] = $fieldInfo;
				}
			}
		} 
		else if($module == 'Calendar' || $module == 'Events') {
			foreach($describeInfo['fields'] as $index => $fieldInfo) {
				$fieldInfo['uitype'] = self::fixUIType($module, $fieldInfo['name'], $fieldInfo['uitype']); 				
				if ($fieldInfo['name'] == 'visibility') {
					if (empty($fieldInfo['type']['picklistValues'])) {
						$fieldInfo['type']['picklistValues'] = self::visibilityValues();
						$fieldInfo['type']['defaultValue'] = $fieldInfo['type']['picklistValues'][0]['value'];
					}
				}
				$describeInfo['fields'][$index] = $fieldInfo;
			}
		}
	}
	
	static function getRelatedFunctionHandler($sourceModule, $targetModule) {
		global $adb;
		$relationResult = $adb->pquery("SELECT name FROM vtiger_relatedlists WHERE tabid=? and related_tabid=? and presence=0", array(getTabid($sourceModule), getTabid($targetModule)));
		$functionName = false;
		if ($adb->num_rows($relationResult)) $functionName = $adb->query_result($relationResult, 0, 'name');
		return $functionName;
	}
	
	/**
	 * Security restriction (sharing privilege) query part
	 */
	static function querySecurityFromSuffix($module, $current_user) {
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$querySuffix = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$querySuffix .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN 
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role 
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid 
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid 
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					) 
					OR vtiger_crmentity.smownerid IN 
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per 
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					) 
					OR 
						(";
		
					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$querySuffix .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$querySuffix .= " vtiger_groups.groupid IN 
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid 
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$querySuffix .= ")
				)";
		}
		return $querySuffix;
	}
	static function getMandatory($typeofdata) {
		$type_array = explode( '~', $typeofdata );
		return $type_array[1];
	}
	
	/**     Function to get all the comments for a troubleticket
	  *     @param int $ticketid -- troubleticket id
	  *     return all the comments as a sequencial string which are related to this ticket
	**/
	static function getTicketComments($ticket) {
        global $adb;
        $commentlist = '';
        $sql = "select * from vtiger_ticketcomments where ticketid=?";
		$recordid = vtws_getIdComponents($ticket['id']);
		$recordid = $recordid[1];
        $result = $adb->pquery($sql, array($recordid));
		$recordprefix= self::getEntityModuleWSId('Users') ;
        for($i=0;$i<$adb->num_rows($result);$i++) {
                $comment = $adb->query_result($result,$i,'comments');
                if($comment != '') {
                        $commentlist[$i]['commentcontent'] = $comment;
                        $commentlist[$i]['assigned_user_id'] = $recordprefix.'x'.$adb->query_result($result,$i,'ownerid');
                        $commentlist[$i]['createdtime'] = $adb->query_result($result,$i,'createdtime');
                }
        }
        return $commentlist;
	}
	/**     Function to create a comment for a troubleticket
	  *     @param int $ticketid -- troubleticket id, comments array
	  *     returns the comment as a array
	**/
	static function createTicketComment($id,$commentcontent,$user) {
		global $adb,$current_user;
		$current_user = $user;

		$targetModule = 'HelpDesk';
		$recordComponents = vtws_getIdComponents($id);

		$focus = CRMEntity::getInstance('HelpDesk');
		$focus->retrieve_entity_info($recordComponents[1], $targetModule);
		$focus->id = $recordComponents[1];
		$focus->mode = 'edit';
		$focus->column_fields['comments'] = $commentcontent;
		$focus->save($targetModule);
		return true;
	}
	
	//     Function to find the related modulename by given fieldname

	static function getEntityName($fieldname, $module='') {
      global $adb;
		// Exception for Assets Module
		if($module == 'Assets'){
			switch($fieldname){
				case 'account' : $fieldname = 'account_id'; break;
				case 'product' : $fieldname = 'product_id'; break;
			}
		}
		$sql = "SELECT `modulename` FROM `vtiger_entityname` WHERE `entityidcolumn` = ? LIMIT 1";
		$result = $adb->pquery($sql, array($fieldname));
		return $adb->query_result($result,0,'modulename');;
	}
	
}
