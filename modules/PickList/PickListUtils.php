<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

/**
 * this file will be used to store the functions to be used in the picklist module
 */

/**
 * Function to get picklist fields for the given module
 * @ param $fld_module
 * It gets the picklist details array for the given module in the given format
 * $fieldlist = Array(Array('fieldlabel'=>$fieldlabel,'generatedtype'=>$generatedtype,'columnname'=>$columnname,'fieldname'=>$fieldname,'value'=>picklistvalues))
 */
function getUserFldArray($fld_module,$roleid){
	global $adb, $log;
	$user_fld = Array();
	$tabid = getTabid($fld_module);

	$query="select vtiger_field.fieldlabel,vtiger_field.columnname,vtiger_field.fieldname, vtiger_field.uitype" .
			" FROM vtiger_field inner join vtiger_picklist on vtiger_field.fieldname = vtiger_picklist.name" .
			" where (displaytype=1 and vtiger_field.tabid=? and vtiger_field.uitype in ('15','55','33','16') " .
			" or (vtiger_field.tabid=? and fieldname='salutationtype' and fieldname !='vendortype')) " .
			" and vtiger_field.presence in (0,2) ORDER BY vtiger_picklist.picklistid ASC";

	$result = $adb->pquery($query, array($tabid, $tabid));
	$noofrows = $adb->num_rows($result);

	if($noofrows > 0){
		$fieldlist = array();
		for($i=0; $i<$noofrows; $i++){
			$user_fld = array();
			$fld_name = $adb->query_result($result,$i,"fieldname");

			$user_fld['fieldlabel'] = $adb->query_result($result,$i,"fieldlabel");
			$user_fld['generatedtype'] = $adb->query_result($result,$i,"generatedtype");
			$user_fld['columnname'] = $adb->query_result($result,$i,"columnname");
			$user_fld['fieldname'] = $adb->query_result($result,$i,"fieldname");
			$user_fld['uitype'] = $adb->query_result($result,$i,"uitype");
			$user_fld['value'] = getAssignedPicklistValues($user_fld['fieldname'], $roleid, $adb);
			$fieldlist[] = $user_fld;
		}
	}
	return $fieldlist;
}

/**
 * Function to get modules which has picklist values
 * It gets the picklist modules and return in an array in the following format
 * $modules = Array($tabid=>$tablabel,$tabid1=>$tablabel1,$tabid2=>$tablabel2,-------------,$tabidn=>$tablabeln)
 */
function getPickListModules(){
	global $adb;
	// vtlib customization: Ignore disabled modules.
	$query = 'select distinct vtiger_field.fieldname,vtiger_field.tabid,vtiger_tab.tablabel, vtiger_tab.name as tabname,uitype from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid where uitype IN (15,33) and vtiger_field.tabid != 29 and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2) order by vtiger_field.tabid ASC';
	// END
	$result = $adb->pquery($query, array());
	while($row = $adb->fetch_array($result)){
		$modules[$row['tablabel']] = $row['tabname'];
	}
	return $modules;
}

/**
 * this function returns all the roles present in the CRM so that they can be displayed in the picklist module
 * @return array $role - the roles present in the CRM in the array format
 */
function getrole2picklist(){
	global $adb;
	$query = "select rolename,roleid from vtiger_role where roleid not in('H1') order by roleid";
	$result = $adb->pquery($query, array());
	while($row = $adb->fetch_array($result)){
		$role[$row['roleid']] = $row['rolename'];
	}
	return $role;

}

/**
 * this function returns the picklists available for a module
 * @param array $picklist_details - the details about the picklists in the module
 * @return array $module_pick - the picklists present in the module in an array format
 */
function get_available_module_picklist($picklist_details){
	$avail_pick_values = $picklist_details;
	foreach($avail_pick_values as $key => $val){
		$module_pick[$avail_pick_values[$key]['fieldname']] = getTranslatedString($avail_pick_values[$key]['fieldlabel']);
	}
	return $module_pick;
}

/**
 * this function returns all the picklist values that are available for a given
 * @param string $fieldName - the name of the field
 * @return array $arr - the array containing the picklist values
 */
function getAllPickListValues($fieldName,$lang = Array() ){
	global $adb;
	$sql = 'SELECT * FROM vtiger_'.$adb->sql_escape_string($fieldName);
	$result = $adb->query($sql);
	$count = $adb->num_rows($result);

	$arr = array();
	for($i=0;$i<$count;$i++){
		$pick_val = $adb->query_result($result, $i, $fieldName);
		if (!empty($lang[$pick_val])) {
			$arr[$pick_val] = $lang[$pick_val];
		} else {
			$arr[$pick_val] = $pick_val;
		}
	}
	return $arr;
}


/**
 * this function accepts the fieldname and the language string array and returns all the editable picklist values for that fieldname
 * @param string $fieldName - the name of the picklist
 * @param array $lang - the language string array
 * @param object $adb - the peardatabase object
 * @return array $pick - the editable picklist values
 */
function getEditablePicklistValues($fieldName, $lang= array(), $adb){
	$values = array();
	$fieldName = $adb->sql_escape_string($fieldName);
	$sql="select $fieldName from vtiger_$fieldName where presence=1 and $fieldName <> '--None--'";
	$res = $adb->query($sql);
	$RowCount = $adb->num_rows($res);
	if($RowCount > 0){
		for($i=0;$i<$RowCount;$i++){
			$pick_val = $adb->query_result($res,$i,$fieldName);
			if($lang[$pick_val] != ''){
				$values[$pick_val]=$lang[$pick_val];
			}else{
				$values[$pick_val]=$pick_val;
			}
		}
	}
	return $values;
}

/**
 * this function accepts the fieldname and the language string array and returns all the non-editable picklist values for that fieldname
 * @param string $fieldName - the name of the picklist
 * @param array $lang - the language string array
 * @param object $adb - the peardatabase object
 * @return array $pick - the no-editable picklist values
 */
function getNonEditablePicklistValues($fieldName, $lang=array(), $adb){
	$values = array();
	$fieldName = $adb->sql_escape_string($fieldName);
	$sql = "select $fieldName from vtiger_$fieldName where presence=0";
	$result = $adb->query($sql);
	$count = $adb->num_rows($result);
	for($i=0;$i<$count;$i++){
		$non_val = $adb->query_result($result,$i,$fieldName);
		if($lang[$non_val] != ''){
			$values[]=$lang[$non_val];
		}else{
			$values[]=$non_val;
		}
	}
	if(count($values)==0){
		$values = "";
	}
	return $values;
}

/**
 * this function returns all the assigned picklist values for the given tablename for the given roleid
 * @param string $tableName - the picklist tablename
 * @param integer $roleid - the roleid of the role for which you want data
 * @param object $adb - the peardatabase object
 * @return array $val - the assigned picklist values in array format
 */
function getAssignedPicklistValues($tableName, $roleid, $adb, $lang=array()){
	static $cache = array();

	$cacheId = $tableName . '#' . $roleid;
	if (isset($cache[$cacheId])) {
		return $cache[$cacheId];
	}

	$arr = array();

	$sub = getSubordinateRoleAndUsers($roleid, false);
	$subRoles = array($roleid);
	$subRoles = array_merge($subRoles, array_keys($sub));

	$sql = "select picklistid from vtiger_picklist where name = ?";
	$result = $adb->pquery($sql, array($tableName));
	if($adb->num_rows($result)){
		$picklistid = $adb->query_result($result, 0, "picklistid");

		$roleids = array();
		foreach($subRoles as $role){
			$roleids[] = $role;
		}

		$sql = "SELECT ".$adb->sql_escape_string($tableName)." FROM ". $adb->sql_escape_string("vtiger_$tableName")
				. " inner join vtiger_role2picklist on ".$adb->sql_escape_string("vtiger_$tableName").".picklist_valueid=vtiger_role2picklist.picklistvalueid"
				. " and roleid in (".generateQuestionMarks($roleids).") order by field(roleid,".generateQuestionMarks($roleids)."), sortid";
		$result = $adb->pquery($sql, array_merge($roleids,$roleids));
		$count = $adb->num_rows($result);

		if($count) {
			while($resultrow = $adb->fetch_array($result)) {
				$pick_val = decode_html($resultrow[$tableName]);
				//$pick_val = decode_html($pick_val);  // we have to do it twice for it to work on listview!!
				if(isset($lang[$pick_val]) and $lang[$pick_val] != '') {
					$arr[$pick_val] = $lang[$pick_val];
				}
				else {
					$arr[$pick_val] = $pick_val;
				}
			}
		}
	}
	// END

	$cache[$cacheId] = $arr;
	return $arr;
}
/**
 * Function to list all modules for userid
 * It gets all the allowed entities to be shown in a picklist uitype 1613. 1633 and return an array in the following format
 * $modules = Array($index=>$tabname,$index1=>$tabname1)
 */
function getAllowedPicklistModules($allowNonEntities=0) {
	global $adb;
	//get All the modules the current user is permitted to Access.
	$allAllowedModules=getPermittedModuleNames();
	$allEntities = array();
	$entitycondition = ($allowNonEntities ? '' : 'isentitytype=1 and ');
	$entityQuery = "SELECT name FROM vtiger_tab WHERE $entitycondition name NOT IN ('Rss','Webmails','Recyclebin','Events')";
	$result = $adb->pquery($entityQuery, array());
	while($result && $row = $adb->fetch_array($result)){
		$allEntities[] = $row['name'];
	}
	$allowedEntities=array_intersect($allAllowedModules, $allEntities);
	return $allowedEntities;
}

function getPicklistValuesSpecialUitypes($uitype,$fieldname,$value,$action='EditView'){
	global $adb,$log,$current_user, $default_charset;

	$fieldname = $adb->sql_escape_string($fieldname);
	if ($uitype == '1614') {
		$uitype = '1613';
		$allowNonEntities = 1;
	} elseif ($uitype == '3314') {
		$uitype = '3313';
		$allowNonEntities = 1;
	} else {
		$allowNonEntities = 0;
	}
	$picklistValues = getAllowedPicklistModules($allowNonEntities);
	$options = array();
	$pickcount = 0;
	if($uitype == "1613"){
		$found = false;
		foreach ($picklistValues as $pKey=>$pValue) {
			$value = decode_html($value);
			$pickListValue = decode_html($pValue);
			if($value == trim($pickListValue)) {
				$chk_val = "selected";
				$pickcount++;
				$found = true;
			}
			else {
				$chk_val = '';
			}
			$pickListValue = to_html($pickListValue);
			if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate')
				$options[] = array(htmlentities(getTranslatedString($pickListValue, $pickListValue),ENT_QUOTES,$default_charset),$pickListValue,$chk_val);
			else
				$options[] = array(getTranslatedString($pickListValue, $pickListValue),$pickListValue,$chk_val);
		}
	}elseif($uitype == "3313"){
		$valueArr = explode("|##|", $value);
		foreach ($valueArr as $key => $value) {
			$valueArr[$key] = trim(html_entity_decode($value, ENT_QUOTES, $default_charset));
		}
		if(!empty($picklistValues)){
			foreach($picklistValues as $order=>$pickListValue){
				if(in_array(trim($pickListValue),$valueArr)){
					$chk_val = "selected";
					$pickcount++;
				}else{
					$chk_val = '';
				}
				if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate'){
					$options[] = array(htmlentities(getTranslatedString($pickListValue, $pickListValue),ENT_QUOTES,$default_charset),$pickListValue,$chk_val );
				}else{
					$options[] = array(getTranslatedString($pickListValue, $pickListValue),$pickListValue,$chk_val );
				}
			}

			if($pickcount == 0 && !empty($value)){
				$options[] = array($app_strings['LBL_NOT_ACCESSIBLE'],$value,'selected');
			}
		}
	}elseif($uitype == "1024"){
		$arr_evo=explode(' |##| ',$value);
		if($action != 'DetailView'){
			$roleid = $current_user->roleid;
			$subrole = getRoleSubordinates($roleid);
			$uservalues = array_merge($subrole,array($roleid));
			for($i=0;$i<sizeof($uservalues);$i++) {
				$currentValId=$uservalues[$i];
				$currentValName= getRoleName($currentValId);
				if(in_array(trim($currentValId),$arr_evo)){
					$chk_val = 'selected';
				}else{
					$chk_val = '';
				}
				$options[] = array($currentValName,$currentValId,$chk_val);
			}
		}else{
			for($i=0;$i<sizeof($arr_evo);$i++) {
				$roleid=$arr_evo[$i];
				$rolename=getRoleName($roleid);
				if((is_admin($current_user))) {
					$options[$i]='<a href="index.php?module=Settings&action=RoleDetailView&parenttab=Settings&roleid='.$roleid.'">'.$rolename.'</a>';
				} else {
					$options[$i]=$rolename;
				}
			}
		}
	}
	uasort($options, function($a,$b) {return (strtolower($a[0]) < strtolower($b[0])) ? -1 : 1;});
	return $options;
}
?>
