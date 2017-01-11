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
class crmtogo_WS_Utils {

	static function initModuleGlobals($module) {
		global $mod_strings, $current_language;
		if ($module == 'Events') {
			$module = 'Calendar';
		}
	}
	
	static function getVtigerVersion() {
		global $vtiger_current_version;
		return $vtiger_current_version;
	}
	
	static function getVersion() {
		$db = PearDatabase::getInstance();
		$versionResult = $db->pquery("SELECT version FROM vtiger_tab WHERE name='Mobile'", array());
		return $db->query_result($versionResult, 0, 'version');
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
			$db = PearDatabase::getInstance();
			$result = $db->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($moduleName));
			if ($result && $db->num_rows($result)) {
				self::$moduleWSIdCache[$moduleName] = $db->query_result($result, 0, 'id');
			}
		}
		return self::$moduleWSIdCache[$moduleName];
	}
	
	static function getEntityModuleWSIds($ignoreNonModule = true) {
		$db = PearDatabase::getInstance();
		$modulewsids = array();
		$result = false;
		if($ignoreNonModule) {
			$result = $db->pquery("SELECT id, name FROM vtiger_ws_entity WHERE ismodule=1", array());
		} 
		else {
			$result = $db->pquery("SELECT id, name FROM vtiger_ws_entity", array());
		}
		while($resultrow = $db->fetch_array($result)) {
			$modulewsids[$resultrow['name']] = $resultrow['id'];
		}
		return $modulewsids;
	}
	
	static function getEntityFieldnames($module) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT fieldname FROM vtiger_entityname WHERE modulename=?", array($module));
		$fieldnames = array();
		if($result && $db->num_rows($result)) {
			$fieldnames = explode(',', $db->query_result($result, 0, 'fieldname'));
		}
		switch($module) {
			case 'HelpDesk': 
				$fieldnames = self::array_replace('title', 'ticket_title', $fieldnames); 
				break;
			case 'Documents': 
				$fieldnames = self::array_replace('title', 'notes_title', $fieldnames); 
				break;
		}
		return $fieldnames;
	}
	
	static function getModuleColumnTableByFieldNames($module, $fieldnames) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT fieldname,columnname,tablename FROM vtiger_field WHERE tabid=? AND fieldname IN (".
			generateQuestionMarks($fieldnames) . ")", array(getTabid($module), $fieldnames)
		);
		$columnnames = array();
		if ($result && $db->num_rows($result)) {
			while($resultrow = $db->fetch_array($result)) {
				$columnnames[$resultrow['fieldname']] = array('column' => $resultrow['columnname'], 'table' => $resultrow['tablename']);
			}
		}
		return $columnnames;
	}
	
	static function detectModulenameFromRecordId($wsrecordid) {
		$db = PearDatabase::getInstance();
		$idComponents = vtws_getIdComponents($wsrecordid);
		$result = $db->pquery("SELECT name FROM vtiger_ws_entity WHERE id=?", array($idComponents[0]));
		if($result && $db->num_rows($result)) {
			return $db->query_result($result, 0, 'name');
		}
		return false;
	}
	
	static $detectFieldnamesToResolveCache = array();
	
	static function detectFieldnamesToResolve($module) {
		$db = PearDatabase::getInstance();
		if(isset(self::$detectFieldnamesToResolveCache[$module])) {
			return self::$detectFieldnamesToResolveCache[$module];
		}
		$resolveUITypes = array(10, 101, 116, 117, 26, 357, 50, 51, 52, 53, 57, 59, 66, 68, 73, 75, 76, 77, 78, 80, 81);
		$result = $db->pquery(
			"SELECT fieldname FROM vtiger_field WHERE uitype IN(". 
			generateQuestionMarks($resolveUITypes) .") AND tabid=?", array($resolveUITypes, getTabid($module)) 
		);
		$fieldnames = array();
		while($resultrow = $db->fetch_array($result)) {
			$fieldnames[] = $resultrow['fieldname'];
		}
		
		// Cache information		
		self::$detectFieldnamesToResolveCache[$module] = $fieldnames;
		return $fieldnames;
	}

	static $gatherModuleFieldGroupInfoCache = array();
	static function gatherModuleFieldGroupInfo($module) {
		$db = PearDatabase::getInstance();
		$current_language = crmtogo_WS_Controller::sessionGet('language') ;
		$current_module_strings = return_module_language($current_language, $module);
		self::initModuleGlobals($module);
		// Cache hit?
		if(isset(self::$gatherModuleFieldGroupInfoCache[$module])) {
			return self::$gatherModuleFieldGroupInfoCache[$module];
		}
		if ($module != 'Calendar') {
			$result = $db->pquery(
				"SELECT fieldname, fieldlabel, blocklabel, uitype, typeofdata, displaytype FROM vtiger_field INNER JOIN
				vtiger_blocks ON vtiger_blocks.tabid=vtiger_field.tabid AND vtiger_blocks.blockid=vtiger_field.block 
				WHERE vtiger_field.tabid=? AND vtiger_field.presence != 1 AND vtiger_field.tablename !='vtiger_ticketcomments'  ORDER BY vtiger_blocks.sequence, vtiger_field.sequence", array(getTabid($module))
			);
		}
		else {
			$result = $db->pquery(
				"SELECT fieldname, fieldlabel, blocklabel, uitype, typeofdata, displaytype FROM vtiger_field INNER JOIN
				vtiger_blocks ON vtiger_blocks.tabid=vtiger_field.tabid AND vtiger_blocks.blockid=vtiger_field.block 
				WHERE vtiger_field.tabid=? AND vtiger_field.presence != 1 and fieldname != 'eventstatus' and fieldname !=  'activitytype' ORDER BY vtiger_blocks.sequence, vtiger_field.sequence", array(getTabid($module))
			);
		}

		$fieldgroups = array();
		while($resultrow = $db->fetch_array($result)) {
			if (array_key_exists ($resultrow['blocklabel'], $current_module_strings)) {
				$blocklabel = $current_module_strings[$resultrow['blocklabel']];
			}
			else {
				$blocklabel = getTranslatedString($resultrow['blocklabel']);
			}
			if (array_key_exists ($resultrow['fieldlabel'], $current_module_strings)) {
				$fieldlabel = $current_module_strings[$resultrow['fieldlabel']];
			}
			else {
				$fieldlabel = getTranslatedString($resultrow['fieldlabel']);
			}
			if(!isset($fieldgroups[$blocklabel])) {
				$fieldgroups[$blocklabel] = array();
			}
			$fieldgroups[$blocklabel][$resultrow['fieldname']] = 
				array(
					'label' => $fieldlabel,
					'uitype'=> self::fixUIType($module, $resultrow['fieldname'], $resultrow['uitype']),
					'typeofdata'=>$resultrow['typeofdata'],
					'displaytype'=>$resultrow['displaytype'],
					'mandatory'=>self::getMandatory ($resultrow['typeofdata'])
				);
		}
		
		// Cache information
		self::$gatherModuleFieldGroupInfoCache[$module] = $fieldgroups;
		return $fieldgroups;
	}
	
	static function documentFoldersInfo() {
		$db = PearDatabase::getInstance();
		$folders = $db->pquery("SELECT folderid, foldername FROM vtiger_attachmentsfolder", array());
		$folderOptions = array();
		while( $folderrow = $db->fetch_array($folders) ) {
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

	static function getassignedtoValues($userObj,$module,$assigned_user_id='') {
		//get users info
		$tabid = getTabid($module);
		$recordprefix= self::getEntityModuleWSId('Users') ;
		if ($assigned_user_id=='') {
			$assigned_user_id_ws = $recordprefix.'x'.$userObj->id;
			$assigned_user_id = $userObj->id;
		}
		else {
			$assigned_user_id_ws = $assigned_user_id;
		}
	    if ($userObj->is_admin==false) {
			$resultuser =get_user_array(FALSE, "Active", $assigned_user_id_ws,'private');
		}
		else { 
			$resultuser =get_user_array(FALSE, "Active", $assigned_user_id_ws);
		}
		//add prefix to key
		$data = array_flip($resultuser);
		foreach($data as $key => &$val) { 
			$val = $recordprefix.'x'.$val; 
		}
		$resultuser = array_flip($data);
		foreach ($resultuser  as $userid=>$username) {
			if ($userid	== $assigned_user_id) {
				$user_array[$userid] = array($username=>'selected');
			}
			else {
				$user_array[$userid] = array($username=>'');
			}
		}
		//handle groups
		$resultgroups= array();
		$resultgroups=vtws_getUserWebservicesGroups ($tabid, $userObj);
		//add prefix to key for groups
		$groups_combo = array();
		if (count($resultgroups) > 0) {
			$newgrouporder = array ();
			foreach($resultgroups as $key => &$val) { 
				$newgrouporder[$val['id']] = $val['name'];
			}
			foreach ($newgrouporder  as $groupid=>$groupname) {
				if ($groupid == $assigned_user_id) {
					$group_array[$groupid] = array($groupname=>'selected');
				}
				else {
					$group_array[$groupid] = array($groupname=>'');
				}
			}
		}
		$fieldvalue = array();
		$fieldvalue[]=$user_array;
		$fieldvalue[] =$group_array;
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
		else if ($module == 'Calendar' || $module == 'Events' || $module == 'Timecontrol') {
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
				$picklistValues = self::getassignedtoValues($current_user,$module);
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
					$describeInfo['fields'][$index] = $fieldInfo;
				}
			}
		}		
		else if ($module == 'Documents') {
			foreach($describeInfo['fields'] as $index => $fieldInfo) {
				if ($fieldInfo['name'] == 'folderid') {
					$picklistValues = self::documentFoldersInfo();
					$fieldInfo['type']['picklistValues'] = $picklistValues;
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
		$db = PearDatabase::getInstance();
		$relationResult = $db->pquery("SELECT name FROM vtiger_relatedlists WHERE tabid=? and related_tabid=? and presence=0", array(getTabid($sourceModule), getTabid($targetModule)));
		$functionName = false;
		if ($db->num_rows($relationResult)) {
			$functionName = $db->query_result($relationResult, 0, 'name');
		}
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

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tabid] == 3) {
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
		if ($type_array[1]=='M') {
			return 'M';
		}
		else {
			return '';
		}
	}
	
	/**     Function to get all the comments for a troubleticket
	  *     @param int $ticketid -- troubleticket id
	  *     return all the comments as a sequencial string which are related to this ticket
	**/
	static function getTicketComments($ticket) {
		$db = PearDatabase::getInstance();
        $commentlist = '';
        $sql = "select * from vtiger_ticketcomments where ticketid=?";
		$recordid = vtws_getIdComponents($ticket['id']);
		$recordid = $recordid[1];
        $result = $db->pquery($sql, array($recordid));
		$recordprefix= self::getEntityModuleWSId('Users') ;
        for($i=0;$i<$db->num_rows($result);$i++) {
                $comment = $db->query_result($result,$i,'comments');
                if($comment != '') {
                        $commentlist[$i]['commentcontent'] = $comment;
                        $commentlist[$i]['assigned_user_id'] = $recordprefix.'x'.$db->query_result($result,$i,'ownerid');
                        $commentlist[$i]['createdtime'] = $db->query_result($result,$i,'createdtime');
                }
        }
        return $commentlist;
	}
	/**     Function to create a comment for a troubleticket
	  *     @param int $ticketid -- troubleticket id, comments array
	  *     returns the comment as a array
	**/
	static function createTicketComment($id,$commentcontent,$user) {
		global $adb,$current_user,$log;
		$current_user = $user;

		$targetModule = 'HelpDesk';

		$focus = CRMEntity::getInstance('HelpDesk');
		$focus->retrieve_entity_info($id, $targetModule);
		$focus->id = $id;
		$focus->mode = 'edit';
		$focus->column_fields['comments'] = $commentcontent;
		$log->fatal($focus->column_fields);
		$focus->save($targetModule);

		return true;
	}
	
	//     Function to find the related modulename by given fieldname

	static function getEntityName($fieldname, $module='') {
		$db = PearDatabase::getInstance();
		// Exception for Assets Module
		if($module == 'Assets'){
			switch($fieldname){
				case 'account' : $fieldname = 'account_id'; break;
				case 'product' : $fieldname = 'product_id'; break;
			}
		}
		$sql = "SELECT `modulename` FROM `vtiger_entityname` WHERE `entityidcolumn` = ? LIMIT 1";
		$result = $db->pquery($sql, array($fieldname));
		return $db->query_result($result,0,'modulename');;
	}

	/**
	 * Function to get the where condition for a module based on the field table entries
	 * @param  string $listquery  -- ListView query for the module
	 * @param  string $module     -- module name
	 * @param  string $search_val -- entered search string value
	 * @return string $where      -- where condition for the module based on field table entries
	 */
	static function getUnifiedWhere($listquery,$module,$search_val,$current_user){
		$db = PearDatabase::getInstance();
		require('user_privileges/user_privileges_'.$current_user->id.'.php');

		$search_val = $db->sql_escape_string($search_val);
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0){
			$query = "SELECT columnname, tablename FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2)";
			$qparams = array(getTabid($module));
		}else{
			$profileList = getCurrentUserProfileList();
			$query = "SELECT columnname, tablename FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid = vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid = vtiger_field.fieldid WHERE vtiger_field.tabid = ? AND vtiger_profile2field.visible = 0 AND vtiger_profile2field.profileid IN (". generateQuestionMarks($profileList) . ") AND vtiger_def_org_field.visible = 0 and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
			$qparams = array(getTabid($module), $profileList);
		}
		$result = $db->pquery($query, $qparams);
		$noofrows = $db->num_rows($result);

		$where = '';
		for($i=0;$i<$noofrows;$i++){
			$columnname = $db->query_result($result,$i,'columnname');
			$tablename = $db->query_result($result,$i,'tablename');

			// Search / Lookup customization
			if($module == 'Contacts' && $columnname == 'accountid') {
				$columnname = "accountname";
				$tablename = "vtiger_account";
			}
			// END

			//Before form the where condition, check whether the table for the field has been added in the listview query
			if(strstr($listquery,$tablename)){
				if($where != ''){
					$where .= " OR ";
				}
				$where .= $tablename.".".$columnname." LIKE '". formatForSqlLike($search_val) ."'";
			}
		}
		return $where;
	}
	
	static function fixReferenceIdByModule($module, $fieldid) {
		if ($module =='Tickets') {
			if ($fieldid=='parent_id') {
				$fieldid='account_id';
			}
		}
		elseif ($module =='HelpDesk') {
			if ($fieldid=='parent_id') {
				$fieldid='account_id';
			}
		}
		elseif ($module =='Potentials') {
			if ($fieldid=='related_to') {
				$fieldid='account_id';
			}
		}
		elseif ($module =='Assets') {
			if ($fieldid=='account') {
				$fieldid='account_id';
			}
			elseif ($fieldid=='product') {
				$fieldid='product_id';
			}
			elseif ($fieldid=='contact') {
				$fieldid='contact_id';
			}
		}
		return $fieldid;
	}
	
	static function getContactBase64Image($contactid) {
		$contactid = explode ('x',$contactid);
		$db = PearDatabase::getInstance();
		$sql = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						WHERE vtiger_crmentity.setype = 'Contacts Image' and vtiger_seattachmentsrel.crmid = ?";
		$result = $db->pquery($sql, array($contactid[1]));
		$noofrows = $db->num_rows($result);
		if ($noofrows >0) {
			$imageId = $db->query_result($result, 0, 'attachmentsid');
			$imagePath = $db->query_result($result, 0, 'path');
			$imageName = decode_html($db->query_result($result, 0, "name"));
			$imgpath = $imagePath.$imageId."_".$imageName;
			$type = pathinfo($imgpath, PATHINFO_EXTENSION);
			$data = file_get_contents($imgpath);
			$str = "data:image/".$type.";base64,".base64_encode($data);
			return $str ;
			
		}
		else {
			return '';
		}
	}
	static function getProductBase64Image($productid) {
		$productid = explode ('x',$productid);
		$db = PearDatabase::getInstance();
		$sql = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						WHERE vtiger_crmentity.setype = 'Products Image' and vtiger_seattachmentsrel.crmid = ? limit 1";
		$result = $db->pquery($sql, array($productid[1]));
		$noofrows = $db->num_rows($result);
		if ($noofrows >0) {
			$imageId = $db->query_result($result, 0, 'attachmentsid');
			$imagePath = $db->query_result($result, 0, 'path');
			$imageName = decode_html($db->query_result($result, 0, "name"));
			$imgpath = $imagePath.$imageId."_".$imageName;
			$type = pathinfo($imgpath, PATHINFO_EXTENSION);
			$data = file_get_contents($imgpath);
			$str = "data:image/".$type.";base64,".base64_encode($data);
			return $str ;
			
		}
		else {
			return '';
		}
	}
	static function gettaxclassInformation($productid) {
		$productid = explode ('x',$productid);
		$db = PearDatabase::getInstance();
		$sql = "SELECT taxpercentage FROM vtiger_producttaxrel
				WHERE taxid = 1 and productid = ?";
		$result = $db->pquery($sql, array($productid[1]));
		$noofrows = $db->num_rows($result);
		if ($noofrows >0) {
			$taxpercentage = $db->query_result($result, 0, 'taxpercentage');
			return $taxpercentage ;
			
		}
		else {
			return '';
		}
	}
	static function getDetailedDocumentInformation($documentrecord) {
		$documentid = explode ('x',$documentrecord['id']);
		$db = PearDatabase::getInstance();
		$sql = "SELECT filename,filetype,fileversion, filedownloadcount,notecontent,filesize, path, vtiger_attachments.attachmentsid FROM vtiger_notes
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
					INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_notes.notesid
					INNER JOIN vtiger_attachments ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid 
					WHERE vtiger_notes.notesid = ? and vtiger_crmentity.deleted = 0";
		$result = $db->pquery($sql, array($documentid[1]));
		$noofrows = $db->num_rows($result);
		if ($noofrows >0) {
			$documentrecord ['filename'] = $db->query_result($result, 0, 'filename');
			$documentrecord ['filetype'] = $db->query_result($result, 0, 'filetype');
			$documentrecord ['fileversion'] = $db->query_result($result, 0, 'fileversion');
			$documentrecord ['filedownloadcount'] = $db->query_result($result, 0, 'filedownloadcount');
			$documentrecord ['notecontent'] = $db->query_result($result, 0, 'notecontent');
			$documentrecord ['filesize'] = $db->query_result($result, 0, 'filesize');
			$documentrecord ['attachmentinfo']['path'] = $db->query_result($result, 0, 'path');
			$documentrecord ['attachmentinfo']['attachmentsid'] = $db->query_result($result, 0, 'attachmentsid');
			$documentrecord ['attachmentinfo']['attachmentname'] = $documentrecord ['filename'];
		}
		return $documentrecord;
	}
	
	static function getConfigDefaults() {
		$db = PearDatabase::getInstance();
		$sql = "SELECT * FROM berli_crmtogo_defaults";
		$result = $db->pquery($sql, array());
		$config = array ();
		$config ['language'] = $db->query_result($result, 0, 'crmtogo_lang');
		$config ['fetch_limit'] = $db->query_result($result, 0, 'fetch_limit');
		$config ['theme'] = $db->query_result($result, 0, 'defaulttheme');
		//Get organizations details
		$sql="select * from vtiger_organizationdetails";
		$res_orgdt = $db->pquery($sql, array());
		//Handle for allowed organation logo/logoname likes UTF-8 Character
		$companyDetails = array();
		$config['company_name'] = $db->query_result($res_orgdt,0,'organizationname');
		$config['company_website'] = $db->query_result($res_orgdt,0,'website');
		$config['company_logo'] = decode_html($db->query_result($res_orgdt,0,'logoname'));
		return $config;
	}
	
	static function getUserConfigSettings($userid) {
		$db = PearDatabase::getInstance();
		$sql = "SELECT * FROM berli_crmtogo_config  where crmtogouser = ? ";
		$result = $db->pquery($sql, array($userid));
		$noofrows = $db->num_rows($result);
		if ($noofrows >0) {
			$config = array ();
			$config ['NavigationLimit'] = $db->query_result($result, 0, 'navi_limit');
			$config ['theme'] = $db->query_result($result, 0, 'theme_color');
			$config ['compactcalendar'] = $db->query_result($result, 0, 'compact_cal');
		}
		else {
			//initialize config for new user by taking admin config
			$sql = "SELECT * FROM berli_crmtogo_config where crmtogouser = 1";
			$result = $db->pquery($sql, array());
			$noofrows = $db->num_rows($result);
			$config = array ();
			for($i=0; $i<$noofrows; $i++){
				$navi_limit = $db->query_result($result, $i, 'navi_limit');
				$theme = $db->query_result($result, $i, 'theme_color');;
				$compact_cal = $db->query_result($result, $i, 'compact_cal');;
				$config ['NavigationLimit'] = $navi_limit;
				$config ['theme'] = $theme;
				$config ['compactcalendar'] = $compact_cal;
				$incl_sql = "INSERT INTO berli_crmtogo_config ( crmtogouser, navi_limit,  theme_color, compact_cal  ) VALUES (?,?,?,?)";
				$db->pquery($incl_sql, array($userid,$navi_limit, $theme,$compact_cal));
			}
		}
		return $config;
	}
	
	static function getUserConfigModuleSettings($userid) {
		$config_module = array ();
		$db = PearDatabase::getInstance();
		$sql = "SELECT * FROM berli_crmtogo_modules where crmtogo_user = ? order by order_num";
		$result = $db->pquery($sql, array($userid));
		$noofrows = $db->num_rows($result);
		if ($noofrows >0) {
			for($i=0; $i<$noofrows; $i++){
				$module = $db->query_result($result, $i, 'crmtogo_module');
				$config_module[$module]['name'] = $module;
				$config_module[$module]['label'] = $db->query_result($result, $i, 'crmtogo_module');
				$config_module[$module]['active'] = $db->query_result($result, $i, 'crmtogo_active');
				$config_module[$module]['ordernum'] = $db->query_result($result, $i, 'order_num');
				$config_module[$module]['userid'] = $db->query_result($result, $i, 'crmtogo_user');
			}
		}
		else {
			//initialize config for new user assuming admin has all available modules in use
			$sql = "SELECT * FROM berli_crmtogo_modules where crmtogo_user = 1 order by order_num";
			$result = $db->pquery($sql, array());
			$noofrows = $db->num_rows($result);
			$module = array ();
			for($i=0; $i<$noofrows; $i++){
				$module = $db->query_result($result, $i, 'crmtogo_module');
				$config_module[$module]['name'] = $module;
				$config_module[$module]['label'] = $db->query_result($result, $i, 'crmtogo_module');
				$config_module[$module]['active'] = $db->query_result($result, $i, 'crmtogo_active');
				$config_module[$module]['ordernum'] = $db->query_result($result, $i, 'order_num');
				$config_module[$module]['userid'] = $db->query_result($result, $i, 'crmtogo_user');
				$incl_sql = "INSERT INTO berli_crmtogo_modules ( crmtogo_user, crmtogo_module, crmtogo_active , order_num ) VALUES (?,?,?,?)";
				$db->pquery($incl_sql, array($userid, $module, '1', $i));
			}
		}
		return $config_module;
	}
	
	static function getConfigComments() {
		//todo: find better way to identify modules with comments
		$comments_module = array ();
		$db = PearDatabase::getInstance();
		$sql = "SELECT * FROM vtiger_links where linktype = 'DETAILVIEWWIDGET' and linkurl = 'block://ModComments:modules/ModComments/ModComments.php'";
		$result = $db->pquery($sql, array());
		$noofrows = $db->num_rows($result);
		if ($noofrows >0) {
			for($i=0; $i<$noofrows; $i++){
				$tabid = $db->query_result($result, $i, 'tabid');
				$comments_module[] =vtlib_getModuleNameById($tabid);
			}
		}
		array_push($comments_module,'HelpDesk');
		return $comments_module;
	}
	
	static function getUsersLanguage($lang) {
		$user_lang = return_module_language($lang, 'Mobile');
		return $user_lang;
	}

}
