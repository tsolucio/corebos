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
 * Function to get picklist fields for the given module
 * @ param $fld_module
 * It gets the picklist details array for the given module in the given format
 * $fieldlist = Array(Array('fieldlabel'=>$fieldlabel,'generatedtype'=>$generatedtype,'columnname'=>$columnname,'fieldname'=>$fieldname,'value'=>picklistvalues))
 */
function getUserFldArray($fld_module, $roleid) {
	global $adb;
	$user_fld = array();
	$tabid = getTabid($fld_module);

	$query="SELECT vtiger_field.fieldlabel,vtiger_field.columnname,vtiger_field.fieldname, vtiger_field.uitype, vtiger_picklist.multii18n
		FROM vtiger_field
		LEFT JOIN vtiger_picklist on vtiger_field.fieldname = vtiger_picklist.name
		WHERE (displaytype in (1,2,3,4) and vtiger_field.tabid=? and vtiger_field.uitype in ('15','33','16')
			or (vtiger_field.tabid=? and fieldname='salutationtype' and fieldname !='vendortype' and fieldname !='firstname'))
			and vtiger_field.presence in (0,2) ORDER BY vtiger_picklist.picklistid ASC";

	$result = $adb->pquery($query, array($tabid, $tabid));
	$noofrows = $adb->num_rows($result);

	$fieldlist = array();
	if ($noofrows > 0) {
		for ($i=0; $i<$noofrows; $i++) {
			$user_fld = array();
			$user_fld['fieldlabel'] = $adb->query_result($result, $i, 'fieldlabel');
			$user_fld['generatedtype'] = $adb->query_result($result, $i, 'generatedtype');
			$user_fld['multii18n'] = ($adb->query_result($result, $i, 'multii18n')=='' ? 1 : $adb->query_result($result, $i, 'multii18n'));
			$user_fld['columnname'] = $adb->query_result($result, $i, 'columnname');
			$user_fld['fieldname'] = $adb->query_result($result, $i, 'fieldname');
			$user_fld['uitype'] = $adb->query_result($result, $i, 'uitype');
			$user_fld['value'] = getAssignedPicklistValues($user_fld['fieldname'], $roleid, $adb);
			$fieldlist[] = $user_fld;
		}
	}
	return $fieldlist;
}

/**
 * Function to get modules which have picklists
 * @param boolean true will include non-role based picklist, false will not include them
 * @return array of modules with picklists in this format: array($tabid1=>$tablabel1,$tabid2=>$tablabel2,...,$tabidn=>$tablabeln)
 */
function getPickListModules($includeNonRole = false) {
	global $adb;
	$inr = ($includeNonRole ? ',16' : '');
	$query = 'select distinct vtiger_tab.tablabel, vtiger_tab.name as tabname
		from vtiger_field
		inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
		where uitype IN (15,33'.$inr.') and vtiger_field.tabid != 29 and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2)';
	$result = $adb->pquery($query, array());
	while ($row = $adb->fetch_array($result)) {
		$modules[$row['tablabel']] = $row['tabname'];
	}
	return $modules;
}

/**
 * this function returns all the roles present in the CRM so that they can be displayed in the picklist module
 * @return array $role - the roles present in the CRM in the array format
 */
function getrole2picklist() {
	global $adb;
	$query = "select rolename,roleid from vtiger_role where roleid not in('H1') order by roleid";
	$result = $adb->pquery($query, array());
	while ($row = $adb->fetch_array($result)) {
		$role[$row['roleid']] = $row['rolename'];
	}
	return $role;
}

/**
 * this function returns the picklists available for a module
 * @param array $picklist_details - the details about the picklists in the module
 * @return array $module_pick - the picklists present in the module in an array format
 */
function get_available_module_picklist($picklist_details) {
	$module_pick = array();
	foreach ($picklist_details as $key => $val) {
		$module_pick[$picklist_details[$key]['fieldname']] = getTranslatedString($picklist_details[$key]['fieldlabel']);
	}
	return $module_pick;
}

/**
 * this function returns all the picklist values that are available for a given
 * @param string $fieldName - the name of the field
 * @return array $arr - the array containing the picklist values
 */
function getAllPickListValues($fieldName, $lang = array()) {
	global $adb;
	$sql = 'SELECT * FROM vtiger_'.$adb->sql_escape_string($fieldName);
	$result = $adb->query($sql);
	$count = $adb->num_rows($result);

	$arr = array();
	for ($i=0; $i<$count; $i++) {
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
 * given a picklist field name, returns all the editable picklist values for that field
 * @param string $fieldName - the name of the picklist
 * @param boolean $lang - true if elements should be returned translated
 * @param object $adb - the pear database object
 * @return array the editable picklist values
 */
function getEditablePicklistValues($fieldName, $lang, $adb) {
	$values = array();
	$fieldName = $adb->sql_escape_string($fieldName);
	$res = $adb->query("select $fieldName from vtiger_$fieldName where presence=1 and $fieldName <> '--None--'");
	$RowCount = $adb->num_rows($res);
	if ($RowCount > 0) {
		if ($lang) {
			$frs = $adb->pquery('select fieldid from vtiger_field where fieldname=? limit 1', array($fieldName));
			$fieldid = $adb->query_result($frs, 0, 0);
			$module = getModuleForField($fieldid);
		}
		for ($i=0; $i<$RowCount; $i++) {
			$pick_val = $adb->query_result($res, $i, $fieldName);
			if ($lang) {
				$values[$pick_val] = getTranslatedString($pick_val, $module);
			} else {
				$values[$pick_val] = $pick_val;
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
function getNonEditablePicklistValues($fieldName, $lang, $adb) {
	$values = array();
	$fieldName = $adb->sql_escape_string($fieldName);
	$sql = "select $fieldName from vtiger_$fieldName where presence=0";
	$result = $adb->query($sql);
	$count = $adb->num_rows($result);
	for ($i=0; $i<$count; $i++) {
		$non_val = $adb->query_result($result, $i, $fieldName);
		if (!empty($lang[$non_val])) {
			$values[]=$lang[$non_val];
		} else {
			$values[]=$non_val;
		}
	}
	if (empty($values)) {
		$values = '';
	}
	return $values;
}

/**
 * this function accepts the fieldname of a picklist and returns true if it contains noneditable values and false if not
 * @param string $fieldName - the name of the picklist
 * @return boolean true if the picklist has noneditable values
 */
function hasNonEditablePicklistValues($fieldName) {
	global $adb;
	$result = $adb->query('select 1 from vtiger_'.$adb->sql_escape_string($fieldName).' where presence=0 limit 1');
	return ($adb->num_rows($result)==1);
}

/**
 * this function accepts the fieldname of a picklist and returns true if it contains noneditable values and false if not
 * @param string $fieldName - the name of the picklist
 * @return boolean true if the picklist has noneditable values
 */
function hasMultiLanguageSupport($fieldName) {
	global $adb;
	$result = $adb->pquery('select multii18n from vtiger_picklist where name=?', array($fieldName));
	return ((int)$adb->query_result($result, 0, 'multii18n')==1);
}

/**
 * returns if a picklist has empty or duplicate values
 * @param string $fieldName - the name of the picklist
 * @return boolean true if an empty or duplicate value exists and false otherwise
 */
function isPicklistValid($fieldName) {
	global $adb;
	$cleanName = $adb->sql_escape_string($fieldName);
	$emptyval = $adb->query('select 1 from vtiger_'.$cleanName.' where '.$cleanName."=''");
	$withNoDups = $adb->query('SELECT distinct '.$cleanName.' FROM vtiger_'.$cleanName);
	$withDups = $adb->query('SELECT distinct ('.$cleanName.' COLLATE utf8_bin) FROM vtiger_'.$cleanName);
	return ($adb->num_rows($emptyval)==0 && $adb->num_rows($withNoDups)==$adb->num_rows($withDups));
}

/**
 * this function accepts the fieldname of a picklist and eliminates empty and duplicate values
 * @param string $module - the name of the module the picklist is in
 * @param string $fieldName - the name of the picklist
 * @return boolean true if values were changed and false if the picklist was already clean
 */
function cleanPicklist($module, $fieldName) {
	global $adb;
	$wascleaned = false;
	if (!isPicklistValid($fieldName)) {
		$mod = Vtiger_Module::getInstance($module);
		if ($mod) {
			$plist = Vtiger_Field::getInstance($fieldName, $mod);
			if ($plist) {
				$cleanName = $adb->sql_escape_string($fieldName);
				$plisttable = $plist->table;
				$plistcolumn = $plist->column;
				$adb->pquery("UPDATE vtiger_$cleanName SET $cleanName=? WHERE $cleanName='' or $cleanName is null", array(Field_Metadata::PICKLIST_EMPTY_VALUE));
				$adb->pquery("UPDATE $plisttable SET $plistcolumn=? WHERE $plistcolumn='' or $plistcolumn is null", array(Field_Metadata::PICKLIST_EMPTY_VALUE));
				$result = $adb->query('select * from vtiger_'.$cleanName.' order by '.$cleanName);
				while ($prow = $adb->fetch_array($result)) {
					$hasDups = $adb->pquery('SELECT '.$cleanName.' FROM vtiger_'.$cleanName.' where '.$cleanName.'=? order by '.$cleanName, array($prow[$cleanName]));
					if ($adb->num_rows($hasDups)>1) {
						$oldVal = $adb->query_result($hasDups, 0, $cleanName);
						$newVal = $adb->query_result($hasDups, 1, $cleanName);
						$plist->delPicklistValues(array($oldVal => $newVal));
					}
				}
				$wascleaned = true;
			}
		}
	}
	return $wascleaned;
}

/**
 * this function returns all the assigned picklist values for the given tablename for the given roleid
 * @param string $tableName - the picklist tablename
 * @param integer $roleid - the roleid of the role for which you want data
 * @param object $adb - the peardatabase object
 * @return array $val - the assigned picklist values in array format
 */
function getAssignedPicklistValues($tableName, $roleid, $adb, $lang = array()) {
	static $cacheObsolete = array();
	static $questionMarkLists = [];
	static $paramLists = [];

	$cache = new corebos_cache();
	$cacheId = $tableName . '#' . $roleid;
	if ($cache->isUsable()) {
		if ($cache->getCacheClient()->has($cacheId)) {
			return $cache->getCacheClient()->get($cacheId);
		}
	} elseif (isset($cacheObsolete[$cacheId])) {
		return $cacheObsolete[$cacheId];
	}

	$arr = array();

	$result = $adb->pquery('select 1 from vtiger_picklist where name=?', array($tableName));
	if ($adb->num_rows($result)) {
		if (!isset($paramLists[$roleid])) {
			$roleids = array_merge(array($roleid), array_keys(getSubordinateRoleAndUsers($roleid, false)));
			$questionMarkLists[$roleid] = generateQuestionMarks($roleids);
			$paramLists[$roleid] = array_merge($roleids, $roleids);
		}
		$tname = $adb->sql_escape_string("vtiger_$tableName");
		$sql = 'SELECT '.$adb->sql_escape_string($tableName).' FROM '. $tname
			.' inner join vtiger_role2picklist on '.$tname.'.picklist_valueid=vtiger_role2picklist.picklistvalueid'
			.' and roleid in ('.$questionMarkLists[$roleid].') order by field(roleid,'.$questionMarkLists[$roleid].'), sortid';
		$result = $adb->pquery($sql, $paramLists[$roleid]);

		if (!empty($result)) {
			while (!$result->EOF) {
				$pick_val = $result->FetchRow();
				$pick_val = $pick_val[$tableName];
				if (isset($lang[$pick_val]) && $lang[$pick_val] != '') {
					$arr[$pick_val] = $lang[$pick_val];
				} else {
					$arr[$pick_val] = $pick_val;
				}
			}
		}
	} else { // uitype 16
		$result = $adb->query('SELECT '.$adb->sql_escape_string($tableName).' FROM '.$adb->sql_escape_string("vtiger_$tableName"));
		if (!empty($result)) {
			while (!$result->EOF) {
				$pick_val = $result->FetchRow();
				$pick_val = $pick_val[$tableName];
				if (isset($lang[$pick_val]) && $lang[$pick_val] != '') {
					$arr[$pick_val] = $lang[$pick_val];
				} else {
					$arr[$pick_val] = $pick_val;
				}
			}
		}
	}

	if ($cache->isUsable()) {
		$cache->getCacheClient()->set($cacheId, $arr);
	} else {
		$cacheObsolete[$cacheId] = $arr;
	}
	return $arr;
}

/**
 * Function to list all modules for userid
 * It gets all the allowed entities to be shown in a picklist uitype 1613. 1633 and return an array in the following format
 * $modules = Array($index=>$tabname,$index1=>$tabname1)
 */
function getAllowedPicklistModules($allowNonEntities = 0) {
	global $adb;
	//get All the modules the current user is permitted to Access.
	$allAllowedModules=getPermittedModuleNames();
	$allEntities = array();
	$entitycondition = ($allowNonEntities ? '' : 'isentitytype=1 and ');
	$entityQuery = "SELECT name FROM vtiger_tab WHERE $entitycondition name NOT IN ('Rss','Recyclebin')";
	$result = $adb->pquery($entityQuery, array());
	while ($result && $row = $adb->fetch_array($result)) {
		$allEntities[] = $row['name'];
	}
	return array_intersect($allAllowedModules, $allEntities);
}

function getPicklistValuesSpecialUitypes($uitype, $fieldname, $value, $action = 'EditView') {
	global $adb, $current_user, $default_charset;

	if ($uitype == '1614') {
		$uitype = '1613';
		$allowNonEntities = 1;
	} elseif ($uitype == '3314') {
		$uitype = '3313';
		$allowNonEntities = 1;
	} else {
		$allowNonEntities = 0;
	}
	$options = array();
	$pickcount = 0;
	if ($uitype == '1613') {
		$picklistValues = getAllowedPicklistModules($allowNonEntities);
		foreach ($picklistValues as $pValue) {
			$value = decode_html($value);
			$pickListValue = decode_html($pValue);
			if ($value == trim($pickListValue)) {
				$chk_val = 'selected';
				$pickcount++;
			} else {
				$chk_val = '';
			}
			$pickListValue = to_html($pickListValue);
			if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
				$options[] = array(htmlentities(getTranslatedString($pickListValue, $pickListValue), ENT_QUOTES, $default_charset), $pickListValue, $chk_val);
			} else {
				$options[] = array(getTranslatedString($pickListValue, $pickListValue), $pickListValue, $chk_val);
			}
		}
	} elseif ($uitype == '3313') {
		$valueArr = explode('|##|', $value);
		foreach ($valueArr as $key => $value) {
			$valueArr[$key] = trim(html_entity_decode($value, ENT_QUOTES, $default_charset));
		}
		$picklistValues = getAllowedPicklistModules($allowNonEntities);
		if (!empty($picklistValues)) {
			foreach ($picklistValues as $pickListValue) {
				if (in_array(trim($pickListValue), $valueArr)) {
					$chk_val = 'selected';
					$pickcount++;
				} else {
					$chk_val = '';
				}
				if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
					$options[] = array(htmlentities(getTranslatedString($pickListValue, $pickListValue), ENT_QUOTES, $default_charset), $pickListValue,$chk_val);
				} else {
					$options[] = array(getTranslatedString($pickListValue, $pickListValue),$pickListValue,$chk_val );
				}
			}
		}
	} elseif ($uitype == '1024') {
		$arr_evo=explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $value);
		if ($action != 'DetailView') {
			$roleid = $current_user->roleid;
			$subrole = getRoleSubordinates($roleid);
			$uservalues = array_merge($subrole, array($roleid));
			for ($i=0; $i < count($uservalues); $i++) {
				$currentValId = $uservalues[$i];
				$currentValName = getRoleName($currentValId);
				if (in_array(trim($currentValId), $arr_evo)) {
					$chk_val = 'selected';
				} else {
					$chk_val = '';
				}
				$options[] = array($currentValName,$currentValId,$chk_val);
			}
		} else {
			for ($i=0; $i < count($arr_evo); $i++) {
				$roleid=$arr_evo[$i];
				$rolename=getRoleName($roleid);
				if (is_admin($current_user)) {
					$options[$i]='<a href="index.php?module=Settings&action=RoleDetailView&roleid='.$roleid.'">'.$rolename.'</a>';
				} else {
					$options[$i]=$rolename;
				}
			}
		}
	} elseif ($uitype == '1615') {
		$actual = getPickListModules(true);
		$i = 0;
		foreach ($actual as $mod) {
			$options[$i++] = array(
				getTranslatedString($mod, $mod),
				$mod,
				$value,
				get_available_module_picklist(getUserFldArray($mod, $current_user->roleid))
			);
		}
	} elseif ($uitype == '1616') {
		$cvrs = $adb->query('select cvid, viewname, entitytype from vtiger_customview order by viewname');
		while ($cv = $adb->fetch_array($cvrs)) {
			$i18nmod = getTranslatedString($cv['entitytype'], $cv['entitytype']);
			$options[] = array(
				$cv['viewname'].$i18nmod,
				$cv['cvid'],
				$cv['viewname'].' ('.$i18nmod.')',
				($cv['cvid']==$value ? 'selected' : ''),
			);
		}
	} elseif ($uitype == '1025') {
		$values = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $value);
		if (!empty($value) && !empty($values[0])) {
			$srchmod=  getSalesEntityType($values[0]);
			for ($i=0; $i < count($values); $i++) {
				$id = $values[$i];
				$displayValueArray = getEntityName($srchmod, $id);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $value2) {
						$shown_val = $value2;
					}
				}
				if (!(vtlib_isModuleActive($srchmod) && isPermitted($srchmod, 'DetailView', $id))) {
					$options[$i]=$shown_val;
				} else {
					$options[$i]='<a href="index.php?module='.$srchmod.'&action=DetailView&record='.$id.'">'.$shown_val.'</a>';
				}
			}
		}
	}
	uasort($options, function ($a, $b) {
		return (strtolower($a[0]) < strtolower($b[0])) ? -1 : 1;
	});
	return $options;
}
?>
