<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/ComboUtil.php';
require_once 'include/utils/CommonUtils.php';

$column_array=array('accountid','contact_id','product_id','campaignid','quoteid','vendorid','potentialid','salesorderid','vendor_id','contactid');
$table_col_array=array('vtiger_account.accountname','vtiger_contactdetails.firstname,vtiger_contactdetails.lastname','vtiger_products.productname',
	'vtiger_campaign.campaignname','vtiger_quotes.subject','vtiger_vendor.vendorname','vtiger_potential.potentialname','vtiger_salesorder.subject',
	'vtiger_vendor.vendorname','vtiger_contactdetails.firstname,vtiger_contactdetails.lastname');

/**This function is used to get the list view header values in a list view during search
*Param $focus - module object
*Param $module - module name
*Param $sort_qry - sort by value
*Param $sorder - sorting order (asc/desc)
*Param $order_by - order by
*Param $relatedlist - flag to check whether the header is for listvie or related list
*Param $oCv - Custom view object
*Returns the listview header values in an array
*/
function getSearchListHeaderValues($focus, $module, $sort_qry = '', $sorder = '', $order_by = '', $relatedlist = '', $oCv = '') {
	global $log, $adb, $app_strings, $mod_strings, $current_user;
	$log->debug("> getSearchListHeaderValues $module, $sort_qry, $sorder, $order_by, $relatedlist");

	$search_header = array();

	//Get the tabid of the module
	$tabid = getTabid($module);
	$bmapname = $module.'_ListColumns';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$cbMapLC = $cbMap->ListColumns();
		$parentmodule = vtlib_purify($_REQUEST['module']);
		$focus->list_fields = $cbMapLC->getSearchFields();
		$focus->list_fields_name = $cbMapLC->getSearchFieldsName();
		if ($parentmodule == 'Home' && $cbMapLC->issetListFieldsMappingFor('Home')) {
			$oCv->list_fields = $focus->list_fields;
			$oCv->list_fields_name = $focus->list_fields_name;
		}
	}
	if ($oCv && isset($oCv->list_fields)) {
		$focus->list_fields = $oCv->list_fields;
	}
	//Added to reduce the no. of queries logging for non-admin users
	$field_list = array();
	$userprivs = $current_user->getPrivileges();
	foreach ($focus->list_fields as $name => $tableinfo) {
		$fieldname = $focus->list_fields_name[$name];
		if ($oCv && isset($oCv->list_fields_name)) {
			$fieldname = $oCv->list_fields_name[$name];
		}
		if ($fieldname == 'accountname' && $module !='Accounts') {
			$fieldname = 'account_id';
		}

		if ($fieldname == 'productname' && $module =='Campaigns') {
			$fieldname = 'product_id';
		}

		if ($fieldname == 'lastname' && $module !='Leads' && $module !='Contacts') {
			$fieldname = 'contact_id';
		}
		$field_list[] = $fieldname;
	}
	//Getting the Entries from Profile2field table
	if (!is_admin($current_user)) {
		$profileList = getCurrentUserProfileList();
		$query = 'SELECT vtiger_field.fieldname
			FROM vtiger_field
			INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
			INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
			WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ('
				. generateQuestionMarks($profileList) .') AND vtiger_field.fieldname IN ('
				. generateQuestionMarks($field_list) .') and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid';
		$result = $adb->pquery($query, array($tabid, $profileList, $field_list));
		$field=array();
		for ($k=0; $k < $adb->num_rows($result); $k++) {
			$field[]=$adb->query_result($result, $k, 'fieldname');
		}

		// if this field array is empty and the user doesn't have one of admin, view all, edit all permissions then the search picklist options will be empty
		// and we cannot navigate the users list - js error will thrown in function getListViewEntries_js in Smarty\templates\Popup.tpl
		if ($module == 'Users' && empty($field)) {
			$field = array('last_name', 'email1');
		}
	}

	// Remove fields which are made inactive
	$focus->filterInactiveFields($module);

	foreach ($focus->list_fields as $name => $tableinfo) {
		if ($oCv) {
			if (isset($oCv->list_fields_name)) {
				if ($oCv->list_fields_name[$name] == '') {
					$fieldname = 'crmid';
				} else {
					$fieldname = $oCv->list_fields_name[$name];
				}
			} else {
				if ($focus->list_fields_name[$name] == '') {
					$fieldname = 'crmid';
				} else {
					$fieldname = $focus->list_fields_name[$name];
				}
			}
			if ($fieldname == 'lastname' && $module !='Leads' && $module !='Contacts') {
				$fieldname = 'contact_id';
			}
			if ($fieldname == 'accountname' && $module !='Accounts') {
				$fieldname = 'account_id';
			}
			if ($fieldname == 'productname' && $module =='Campaigns') {
				$fieldname = 'product_id';
			}
		} else {
			if ($focus->list_fields_name[$name] == '') {
				$fieldname = 'crmid';
			} else {
				$fieldname = $focus->list_fields_name[$name];
			}

			if ($fieldname == 'lastname' && $module !='Leads' && $module !='Contacts') {
				$fieldname = 'contact_id';
			}
		}
		if (($userprivs->hasGlobalReadPermission() || in_array($fieldname, $field)) && $fieldname!='parent_id') {
			$fld_name=$fieldname;
			if ($fieldname == 'contact_id' && $module !='Contacts') {
				$name = $app_strings['LBL_CONTACT_LAST_NAME'];
			} elseif ($fieldname == 'contact_id' && $module =='Contacts') {
				$name = $mod_strings['Reports To'].' - '.$mod_strings['LBL_LIST_LAST_NAME'];
			}
			$search_header[$fld_name] = getTranslatedString($name);
		}
		if ($module == 'HelpDesk' && $fieldname == 'crmid') {
			$fld_name=$fieldname;
			$search_header[$fld_name] = getTranslatedString($name);
		}
	}
	$log->debug('< getSearchListHeaderValues');
	return $search_header;
}

/**This function is used to get the where condition for search listview query along with url_string
*Param $module - module name
*Returns the where conditions and url_string values in string format
*/
function Search($module, $input = '') {
	global $log,$default_charset;

	if (empty($input)) {
		$input = $_REQUEST;
	}

	$log->debug('> Search '.$module);
	$url_string='';
	if (isset($input['search_field']) && $input['search_field'] !='') {
		$search_column=vtlib_purify($input['search_field']);
	}
	$search_string = '';
	if (isset($input['search_text']) && $input['search_text']!='') {
		// search other characters like '|, ?, ?'
		$search_string = vtlib_purify($input['search_text']);
		$stringConvert = function_exists('iconv') ? @iconv('UTF-8', $default_charset, $search_string) : $search_string;
		$search_string=trim($stringConvert);
	}
	if (isset($input['searchtype']) && $input['searchtype']!='') {
		$search_type=vtlib_purify($input['searchtype']);
		if ($search_type == 'BasicSearch') {
			$where=BasicSearch($module, $search_column, $search_string, $input);
		} elseif ($search_type == 'AdvanceSearch') {
		} else { //Global Search
		}
		$url_string = '&search_field='.$search_column.'&search_text='.urlencode($search_string).'&searchtype=BasicSearch';
		if (isset($input['type']) && $input['type'] != '') {
			$url_string .= '&type='.vtlib_purify($input['type']);
		}
		$log->debug('< Search');
		return $where.'#@@#'.$url_string;
	}
}

/** get SQL condition to retrieve a user id given a user_name: used during search
* @param string $table_name - tablename
* @param string $column_name - columnname
* @param string $search_string - searchstring value (user name)
* @return string the where conditions for list query
*/
function get_usersid($table_name, $column_name, $search_string) {
	global $log;
	$log->debug('> get_usersid '.$table_name.','.$column_name.','.$search_string);
	$where = "(trim(vtiger_users.ename) like '". formatForSqlLike($search_string)  . "' or vtiger_groups.groupname like '". formatForSqlLike($search_string) ."')";
	$log->debug('< get_usersid');
	return $where;
}

/**This function is used to get where conditions for a given accountid or contactid during search for their respective names
*Param $column_name - columnname
*Param $search_string - searchstring value (username)
*Returns the where conditions for list query in string format
*/
function getValuesforColumns($column_name, $search_string, $criteria = 'cts', $input = '') {
	global $log, $column_array, $table_col_array;
	$log->debug("> getValuesforColumns $column_name, $search_string");

	if (empty($input)) {
		$input = $_REQUEST;
	}

	if (isset($input['type']) && $input['type'] == 'entchar') {
		$criteria = 'is';
	}

	for ($i=0, $iMax = count($column_array); $i< $iMax; $i++) {
		if ($column_name == $column_array[$i]) {
			$val = $table_col_array[$i];
			$explode_column = explode(',', $val);
			$x = count($explode_column);
			if ($x == 1) {
				$where = getSearch_criteria($criteria, $search_string, $val);
			} else {
				if ($column_name == 'contact_id' && isset($input['type']) && $input['type'] == 'entchar') {
					$cSql = getSqlForNameInDisplayFormat(array('lastname'=>'vtiger_contactdetails.lastname', 'firstname'=>'vtiger_contactdetails.firstname'), 'Contacts');
					$where = "$cSql = '$search_string'";
				} else {
					$where = '(';
					for ($j=0, $jMax = count($explode_column); $j< $jMax; $j++) {
						$where .= getSearch_criteria($criteria, $search_string, $explode_column[$j]);
						if ($j != $x-1) {
							if ($criteria == 'dcts' || $criteria == 'isn') {
								$where .= ' and ';
							} else {
								$where .= ' or ';
							}
						}
					}
					$where .= ')';
				}
			}
			break 1;
		}
	}
	$log->debug('< getValuesforColumns');
	return $where;
}

/**This function is used to get where conditions in Basic Search
*Param $module - module name
*Param $search_field - columnname/field name in which the string has be searched
*Param $search_string - searchstring value (username)
*Returns the where conditions for list query in string format
*/
function BasicSearch($module, $search_field, $search_string, $input = '') {
	global $log, $mod_strings, $current_user, $adb, $column_array;
	$log->debug('> BasicSearch '.$module.','.$search_field.','.$search_string);
	$search_string = ltrim(rtrim($adb->sql_escape_string($search_string)));

	if (empty($input)) {
		$input = $_REQUEST;
	}
	$uitype = 0;
	if ($search_field =='crmid') {
		$column_name='crmid';
		$table_name='vtiger_crmentity';
		$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
	} elseif ($search_field =='currency_id' && ($module=='PriceBooks' || $module=='PurchaseOrder' || $module=='SalesOrder' || $module=='Invoice' || $module=='Quotes')) {
		$column_name='currency_name';
		$table_name='vtiger_currency_info';
		$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
	} elseif ($search_field == 'folderid' && $module == 'Documents') {
		$column_name='foldername';
		$table_name='vtiger_attachmentsfolder';
		$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
	} else {
		//Check added for tickets by accounts/contacts in dashboard
		$search_field_first = $search_field;
		if ($module=='HelpDesk') {
			if ($search_field == 'contactid') {
				return "(vtiger_contactdetails.contact_no like '". formatForSqlLike($search_string) ."')";
			} elseif ($search_field == 'account_id') {
				$search_field = 'parent_id';
			}
		}
		//Check ends

		//Added to search contact name by lastname
		if (($module=='Invoice' || $module=='Documents' || $module=='SalesOrder' || $module=='PurchaseOrder') && ($search_field=='contact_id')) {
			$module = 'Contacts';
			$search_field = 'lastname';
		}
		if ($search_field == 'accountname' && $module != 'Accounts') {
			$search_field = 'account_id';
		}
		if ($search_field == 'productname' && $module == 'Campaigns') {
			$search_field = 'product_id';
		}

		$qry= 'select vtiger_field.columnname,tablename, fieldname
			from vtiger_tab
			inner join vtiger_field on vtiger_field.tabid=vtiger_tab.tabid
			where vtiger_tab.name=? and (fieldname=? or columnname=?)';
		$result = $adb->pquery($qry, array($module, $search_field, $search_field));
		$noofrows = $adb->num_rows($result);
		if ($noofrows!=0) {
			$column_name=$adb->query_result($result, 0, 'columnname');
			$field_name=$adb->query_result($result, 0, 'fieldname');
			//Check added for tickets by accounts/contacts in dashboard
			if ($column_name == 'parent_id') {
				if ($search_field_first	== 'account_id') {
					$search_field_first = 'accountid';
				}
				if ($search_field_first	== 'contactid') {
					$search_field_first = 'contact_id';
				}
				$column_name = $search_field_first;
			}

			//Check ends
			$table_name=$adb->query_result($result, 0, 'tablename');
			$uitype=getUItype($module, $column_name);

			//Added for Member of search in Accounts
			if ($column_name == 'parentid' && $module == 'Accounts') {
				$table_name = 'vtiger_account2';
				$column_name = 'accountname';
			}
			if ($column_name == 'parentid' && $module == 'Products') {
				$table_name = 'vtiger_products2';
				$column_name = 'productname';
			}
			if ($column_name == 'reportsto' && $module == 'Contacts') {
				$table_name = 'vtiger_contactdetails2';
				$column_name = 'lastname';
			}
			if ($column_name == 'inventorymanager' && $module = 'Quotes') {
				$table_name = 'vtiger_usersQuotes';
				$column_name = 'user_name';
			}
			//Added to support user date format in basic search
			if ($uitype == 5 || $uitype == 6 || $uitype == 23 || $uitype == 70) {
				if ($search_string != '' && $search_string != '0000-00-00') {
					$date = new DateTimeField($search_string);
					$value = $date->getDisplayDate();
					if (strpos($search_string, ' ') > -1) {
						$value .= (' ' . $date->getDisplayTime());
					}
				} else {
					$value = $search_string;
				}
			}
			// Added to fix errors while searching check box type fields(like product active. ie. they store 0 or 1. we search them as yes or no) in basic search.
			if ($uitype == 56) {
				if (strtolower($search_string) == 'yes') {
					$where="$table_name.$column_name = '1'";
				} elseif (strtolower($search_string) == 'no') {
					$where="$table_name.$column_name = '0'";
				} else {
					$where="$table_name.$column_name = '-1'";
				}
			} elseif (($uitype == 15 || $uitype == 16) && hasMultiLanguageSupport($field_name)) {
				$currlang = $current_user->language;
				$where = "$table_name.$column_name IN (select translation_key from vtiger_cbtranslation where locale='$currlang' and forpicklist='$module::$field_name'"
					." and i18n LIKE '".formatForSqlLike($search_string) ."') OR $table_name.$column_name like '". formatForSqlLike($search_string) ."'";
			} elseif ($table_name == 'vtiger_crmentity' && $column_name == 'smownerid') {
				$where = get_usersid($table_name, $column_name, $search_string);
			} elseif ($table_name == 'vtiger_crmentity' && $column_name == 'modifiedby') {
				$where = "(trim(vtiger_users2.ename) like '". formatForSqlLike($search_string) . "' or vtiger_groups2.groupname like '". formatForSqlLike($search_string) ."')";
			} elseif (in_array($column_name, $column_array)) {
				$where = getValuesforColumns($column_name, $search_string, 'cts', $input);
			} elseif (isset($input['type']) && $input['type'] == 'entchar') {
				$where="$table_name.$column_name = '". $search_string ."'";
			} else {
				$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
			}
		} else {
			$where = "$search_field like '". formatForSqlLike($search_string) ."'";
		}
	}
	if (false !== stripos($where, "like '%%'")) {
		$where_cond0=str_replace("like '%%'", "like ''", $where);
		$where_cond1=str_replace("like '%%'", 'is NULL', $where);
		$where = '('.$where_cond0.' or '.$where_cond1.')';
	}
	// commented to support searching '%' with the search string.
	if (isset($input['type']) && $input['type'] == 'alpbt') {
		$where = str_replace_once('%', '', $where);
	}

	//uitype 10 handling
	if ($uitype == 10) {
		$where = array();
		$result = $adb->pquery('select fieldid from vtiger_field where tabid=? and fieldname=?', array(getTabid($module), $search_field));

		if ($adb->num_rows($result)>0) {
			$fieldid = $adb->query_result($result, 0, 'fieldid');
			$result = $adb->pquery('select * from vtiger_fieldmodulerel where fieldid=?', array($fieldid));
			$count = $adb->num_rows($result);
			$searchString = formatForSqlLike($search_string);

			for ($i=0; $i<$count; $i++) {
				$relModule = $adb->query_result($result, $i, 'relmodule');
				$relInfo = getEntityField($relModule);
				$relTable = $relInfo['tablename'];
				$relField = $relInfo['fieldname'];

				if (strpos($relField, 'concat') !== false) {
					$where[] = "$relField like '$searchString'";
				} else {
					$where[] = "$relTable.$relField like '$searchString'";
				}
			}
			$where = implode(' or ', $where);
		}
		$where = "($where) ";
	}

	$log->debug('< BasicSearch');
	return $where;
}

/**This function is used to get where conditions in Advance Search
*Param $module - module name
*Returns the where conditions for list query in string format
*/
function getAdvSearchfields($module) {
	global $log, $adb, $current_user, $mod_strings,$app_strings;
	$log->debug('> getAdvSearchfields '.$module);
	$userprivs = $current_user->getPrivileges();

	$tabid = getTabid($module);

	if ($userprivs->hasGlobalReadPermission()) {
		$sql = 'select vtiger_field.* from vtiger_field where vtiger_field.displaytype in (1,2,3) and vtiger_field.presence in (0,2)';
		if ($tabid == 13 || $tabid == 15) {
			$sql.= " and vtiger_field.fieldlabel != 'Add Comment'";
		}
		if ($tabid == 14) {
			$sql.= " and vtiger_field.fieldlabel != 'Product Image'";
		}
		if ($tabid == 4) {
			$sql.= " and vtiger_field.fieldlabel != 'Contact Image'";
		}
		if ($tabid == 13 || $tabid == 10) {
			$sql.= " and vtiger_field.fieldlabel != 'Attachment'";
		}
		$sql.= ' and vtiger_field.fieldid in
			(select min(fieldid) from vtiger_field where vtiger_field.tabid=? group by vtiger_field.fieldlabel) order by block,sequence';

		$params = array($tabid);
	} else {
		$profileList = getCurrentUserProfileList();
		$sql = 'select distinct vtiger_field.*
			from vtiger_field
			inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid
			inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid
			where vtiger_field.displaytype in (1,2,3) and vtiger_field.presence in (0,2) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0';

		$params = array();
		if (count($profileList) > 0) {
			$sql.= ' and vtiger_profile2field.profileid in ('. generateQuestionMarks($profileList) .')';
			$params[] = $profileList;
		}

		if ($tabid == 13 || $tabid == 15) {
			$sql.= " and vtiger_field.fieldlabel != 'Add Comment'";
		}
		if ($tabid == 14) {
			$sql.= " and vtiger_field.fieldlabel != 'Product Image'";
		}
		if ($tabid == 4) {
			$sql.= " and vtiger_field.fieldlabel != 'Contact Image'";
		}
		if ($tabid == 13 || $tabid == 10) {
			$sql.= " and vtiger_field.fieldlabel != 'Attachment'";
		}
		$sql.= ' and vtiger_field.fieldid in
			(select min(fieldid) from vtiger_field where vtiger_field.tabid=? group by vtiger_field.fieldlabel) order by block,sequence';
		$params[] = $tabid;
	}

	$result = $adb->pquery($sql, $params);
	$noofrows = $adb->num_rows($result);
	$select_flag = '';
	$OPTION_SET = '';
	for ($i=0; $i<$noofrows; $i++) {
		$fieldtablename = $adb->query_result($result, $i, 'tablename');
		$fieldcolname = $adb->query_result($result, $i, 'columnname');
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		// $result > 'block'
		$fieldtype = $adb->query_result($result, $i, 'typeofdata');
		$fieldtype = explode('~', $fieldtype);
		$fieldtypeofdata = $fieldtype[0];
		if ($fieldcolname == 'account_id' || $fieldcolname == 'accountid' || $fieldcolname == 'product_id' || $fieldcolname == 'vendor_id'
			|| $fieldcolname == 'contact_id' || $fieldcolname == 'contactid' || $fieldcolname == 'vendorid' || $fieldcolname == 'potentialid'
			|| $fieldcolname == 'salesorderid' || $fieldcolname == 'quoteid' || $fieldcolname == 'parentid' || $fieldcolname == 'recurringtype'
			|| $fieldcolname == 'campaignid' || $fieldcolname == 'inventorymanager' || $fieldcolname == 'currency_id'
		) {
			$fieldtypeofdata = 'V';
		}
		if ($fieldcolname == 'discontinued' || $fieldcolname == 'active') {
			$fieldtypeofdata = 'C';
		}
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');

		// Added to display customfield label in search options
		if ($fieldlabel == '') {
			$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		}

		if ($fieldlabel == 'Related To') {
			$fieldlabel = 'Related to';
		}
		if ($fieldlabel == 'Start Date & Time') {
			$fieldlabel = 'Start Date';
		}
		//$fieldlabel1 = str_replace(' ','_',$fieldlabel); // Is not used anywhere
		//Check added to search the lists by Inventory manager
		if ($fieldtablename == 'vtiger_quotes' && $fieldcolname == 'inventorymanager') {
			$fieldtablename = 'vtiger_usersQuotes';
			$fieldcolname = 'user_name';
		}
		if ($fieldtablename == 'vtiger_contactdetails' && $fieldcolname == 'reportsto') {
			$fieldtablename = 'vtiger_contactdetails2';
			$fieldcolname = 'lastname';
		}
		if ($fieldtablename == 'vtiger_notes' && $fieldcolname == 'folderid') {
			$fieldtablename = 'vtiger_attachmentsfolder';
			$fieldcolname = 'foldername';
		}
		if ($fieldlabel != 'Related to') {
			if ($i==0) {
				$select_flag = 'selected';
			}

			$mod_fieldlabel = getTranslatedString($fieldlabel, $module);
			if ($mod_fieldlabel =='') {
				$mod_fieldlabel = $fieldlabel;
			}

			if ($fieldlabel == 'Product Code') {
				$OPTION_SET .="<option value=\'".$fieldtablename.':'.$fieldcolname.':'.$fieldname.'::'.$fieldtypeofdata."\'".$select_flag.'>'.$mod_fieldlabel.'</option>';
			}
			if ($fieldlabel == 'Reports To') {
				$OPTION_SET .= "<option value=\'".$fieldtablename.':'.$fieldcolname.':'.$fieldname.'::'.$fieldtypeofdata."\'".$select_flag.'>'.$mod_fieldlabel
					.' - '.$mod_strings['LBL_LIST_LAST_NAME'].'</option>';
			} elseif ($fieldcolname == 'contactid' || $fieldcolname == 'contact_id') {
				$OPTION_SET .= "<option value=\'vtiger_contactdetails:lastname:".$fieldname.'::'.$fieldtypeofdata."\' ".$select_flag.'>'
					.$app_strings['LBL_CONTACT_LAST_NAME'].'</option>';
				$OPTION_SET.="<option value=\'vtiger_contactdetails:firstname:".$fieldname.'::'.$fieldtypeofdata."\'>".$app_strings['LBL_CONTACT_FIRST_NAME'].'</option>';
			} elseif ($fieldcolname == 'campaignid') {
				$OPTION_SET .= "<option value=\'vtiger_campaign:campaignname:".$fieldname.'::'.$fieldtypeofdata."\' ".$select_flag.'>'.$mod_fieldlabel.'</option>';
			} else {
				$OPTION_SET .= "<option value=\'".$fieldtablename.':'.$fieldcolname.':'.$fieldname.'::'.$fieldtypeofdata."\' ".$select_flag.'>'
					.str_replace("'", "`", $fieldlabel).'</option>';
			}
		}
	}
	//Added to include Ticket ID in HelpDesk advance search
	if ($module == 'HelpDesk') {
		$mod_fieldlabel = $mod_strings['Ticket ID'];
		if ($mod_fieldlabel =='') {
			$mod_fieldlabel = 'Ticket ID';
		}
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('HelpDesk', true);
		$OPTION_SET .= "<option value=\'$crmEntityTable:crmid:".$fieldname.'::'.$fieldtypeofdata."\'>".$mod_fieldlabel.'</option>';
	}
	$log->debug('< getAdvSearchfields');
	return $OPTION_SET;
}

/**This function is returns the where conditions for each search criteria option in Advance Search
*Param $criteria - search criteria option
*Param $searchstring - search string
*Param $searchfield - fieldname to be search for
*Returns the search criteria option (where condition) to be added in list query
*/
function getSearch_criteria($criteria, $searchstring, $searchfield) {
	global $log;
	$log->debug('> getSearch_criteria '.$criteria.','.$searchstring.','.$searchfield);
	$searchstring = ltrim(rtrim($searchstring));
	if (($searchfield != 'vtiger_troubletickets.update_log')
		&& ($searchfield == 'vtiger_crmentity.modifiedtime' || $searchfield == 'vtiger_crmentity.createdtime' || false !== stripos($searchfield, 'date'))
	) {
		if ($searchstring != '' && $searchstring != '0000-00-00') {
			$date = new DateTimeField($searchstring);
			$value = $date->getDisplayDate();
			if (strpos($searchstring, ' ') > -1) {
				$value .= (' ' . $date->getDisplayTime());
			}
		} else {
			$value = $searchstring;
		}
	}
	if ($searchfield == 'vtiger_account.parentid') {
		$searchfield = 'vtiger_account2.accountname';
	}
	if ($searchfield == 'vtiger_pricebook.currency_id' || $searchfield == 'vtiger_quotes.currency_id' || $searchfield == 'vtiger_invoice.currency_id'
		|| $searchfield == 'vtiger_purchaseorder.currency_id' || $searchfield == 'vtiger_salesorder.currency_id'
	) {
		$searchfield = 'vtiger_currency_info.currency_name';
	}
	$where_string = '';
	switch ($criteria) {
		case 'cts':
			$where_string = $searchfield." like '". formatForSqlLike($searchstring) ."' ";
			if ($searchstring == null) {
					$where_string = '('.$searchfield." like '' or ".$searchfield.' is NULL)';
			}
			break;

		case 'dcts':
			if ($searchfield == 'vtiger_users.user_name' || $searchfield =='vtiger_groups.groupname') {
				$where_string = '('.$searchfield." not like '". formatForSqlLike($searchstring) ."')";
			} else {
				$where_string = '('.$searchfield." not like '". formatForSqlLike($searchstring) ."' or ".$searchfield.' is null)';
			}
			if ($searchstring == null) {
				$where_string = '('.$searchfield." not like '' or ".$searchfield.' is not NULL)';
			}
			break;

		case 'is':
			$where_string = $searchfield." = '".$searchstring."' ";
			if ($searchstring == null) {
				$where_string = '('.$searchfield.' is NULL or '.$searchfield." = '')";
			}
			break;

		case 'isn':
			if ($searchfield == 'vtiger_users.user_name' || $searchfield =='vtiger_groups.groupname') {
				$where_string = '('.$searchfield." != '".$searchstring."')";
			} else {
				$where_string = '('.$searchfield." != '".$searchstring."' or ".$searchfield.' is null)';
			}
			if ($searchstring == null) {
				$where_string = '('.$searchfield." not like '' and ".$searchfield.' is not NULL)';
			}
			break;

		case 'bwt':
			$where_string = $searchfield." like '". formatForSqlLike($searchstring, 2) ."' ";
			break;

		case 'ewt':
			$where_string = $searchfield." like '". formatForSqlLike($searchstring, 1) ."' ";
			break;

		case 'grt':
			$where_string = $searchfield." > '".$searchstring."' ";
			break;

		case 'lst':
			$where_string = $searchfield." < '".$searchstring."' ";
			break;

		case 'grteq':
			$where_string = $searchfield." >= '".$searchstring."' ";
			break;

		case 'lsteq':
			$where_string = $searchfield." <= '".$searchstring."' ";
			break;
	}
	$log->debug('< getSearch_criteria');
	return $where_string;
}

/**This function is returns the where conditions for search
*Param $currentModule - module name
*Returns the where condition to be added in list query in string format
*/
function getWhereCondition($currentModule, $input = '') {
	global $log;
	$log->debug('> getWhereCondition '.$currentModule);

	if (empty($input)) {
		$input = $_REQUEST;
	}

	if ($input['searchtype']=='advance') {
		$advft_criteria_decoded = $advft_criteria_groups_decoded = array();
		$advft_criteria = $input['advft_criteria'];
		if (!empty($advft_criteria)) {
			$advft_criteria_decoded = json_decode($advft_criteria, true);
		}
		$advft_criteria_groups = (isset($input['advft_criteria_groups']) ? $input['advft_criteria_groups'] : '');
		if (!empty($advft_criteria_groups)) {
			$advft_criteria_groups_decoded = json_decode($advft_criteria_groups, true);
		}

		$advfilterlist = getAdvancedSearchCriteriaList($advft_criteria_decoded, $advft_criteria_groups_decoded, $currentModule);
		$adv_string = generateAdvancedSearchSql($advfilterlist);
		if (!empty($adv_string)) {
			$adv_string = '('.$adv_string.')';
		}
		$where = $adv_string.'#@@#&advft_criteria='.$advft_criteria.'&advft_criteria_groups='.$advft_criteria_groups.'&searchtype=advance';
	} elseif (isset($input['type']) && $input['type']=='dbrd') {
		$where = getdashboardcondition($input);
	} else {
		$where = Search($currentModule, $input);
	}
	$log->debug('< getWhereCondition');
	return $where;
}

function getSearchURL($input) {
	global $default_charset;
	$urlString='';
	if (isset($input['searchtype']) && $input['searchtype']=='advance') {
		$advft_criteria = isset($input['advft_criteria']) ? vtlib_purify($input['advft_criteria']) : '';
		if (empty($advft_criteria)) {
			return $urlString;
		}
		$advft_criteria_groups = isset($input['advft_criteria_groups']) ? vtlib_purify($input['advft_criteria_groups']) : '';
		$urlString .= '&advft_criteria='.urlencode($advft_criteria).'&advft_criteria_groups='.urlencode($advft_criteria_groups).'&searchtype=advance';
	} elseif (isset($input['type']) && $input['type']=='dbrd') {
		if (isset($input['leadsource'])) {
			$leadSource = vtlib_purify($input['leadsource']);
			$urlString .= '&leadsource='.$leadSource;
		}
		if (isset($input['date_closed'])) {
			$dateClosed = vtlib_purify($input['date_closed']);
			$urlString .= '&date_closed='.$dateClosed;
		}
		if (isset($input['sales_stage'])) {
			$salesStage = vtlib_purify($input['sales_stage']);
			$urlString .= '&sales_stage='.$salesStage;
		}
		if (!empty($input['closingdate_start']) && !empty($input['closingdate_end'])) {
			$dateClosedStart = vtlib_purify($input['closingdate_start']);
			$dateClosedEnd = vtlib_purify($input['closingdate_end']);
			$urlString .= "&closingdate_start=$dateClosedStart&closingdate_end=".$dateClosedEnd;
		}
		if (isset($input['owner'])) {
			$owner = vtlib_purify($input['owner']);
			$urlString .= '&owner='.$owner;
		}
		if (isset($input['campaignid'])) {
			$campaignId = vtlib_purify($input['campaignid']);
			$urlString .= '&campaignid='.$campaignId;
		}
		if (isset($input['quoteid'])) {
			$quoteId = vtlib_purify($input['quoteid']);
			$urlString .= '&quoteid='.$quoteId;
		}
		if (isset($input['invoiceid'])) {
			$invoiceId = vtlib_purify($input['invoiceid']);
			$urlString .= '&invoiceid='.$invoiceId;
		}
		if (isset($input['purchaseorderid'])) {
			$purchaseOrderId = vtlib_purify($input['purchaseorderid']);
			$urlString .= '&purchaseorderid='.$purchaseOrderId;
		}
		if (isset($input['from_homepagedb']) && $input['from_homepagedb'] != '') {
			$urlString .= '&from_homepagedb='.vtlib_purify($input['from_homepagedb']);
		}
		if (isset($input['type']) && $input['type'] != '') {
			$urlString .= '&type='.vtlib_purify($input['type']);
		}
	} else {
		$value = vtlib_purify($input['search_text']);
		$stringConvert = function_exists('iconv') ? @iconv('UTF-8', $default_charset, $value) : $value;
		$value=trim($stringConvert);
		$field=vtlib_purify($input['search_field']);
		$urlString = "&search_field=$field&search_text=".urlencode($value).'&searchtype=BasicSearch';
		if (!empty($input['type'])) {
			$urlString .= '&type='.vtlib_purify($input['type']);
		}
		if (!empty($input['operator'])) {
			$urlString .= '&operator='.vtlib_purify($input['operator']);
		}
	}
	return $urlString;
}

/**This function is returns the where conditions for dashboard and shows the records when clicked on dashboard graph
*Takes no parameter, process the values got from the html request object
*Returns the search criteria option (where condition) to be added in list query
*/
function getdashboardcondition($input = '') {
	global $adb;

	if (empty($input)) {
		$input = $_REQUEST;
	}

	$where_clauses = array();
	$url_string = "";

	if (isset($input['leadsource'])) {
		$lead_source = $input['leadsource'];
	}
	if (isset($input['date_closed'])) {
		$date_closed = $input['date_closed'];
	}
	if (isset($input['sales_stage'])) {
		$sales_stage = $input['sales_stage'];
	}
	if (isset($input['closingdate_start'])) {
		$date_closed_start = $input['closingdate_start'];
	}
	if (isset($input['closingdate_end'])) {
		$date_closed_end = $input['closingdate_end'];
	}
	if (isset($input['owner'])) {
		$owner = vtlib_purify($input['owner']);
	}
	if (isset($input['campaignid'])) {
		$campaign = vtlib_purify($input['campaignid']);
	}
	if (isset($input['quoteid'])) {
		$quote = vtlib_purify($input['quoteid']);
	}
	if (isset($input['invoiceid'])) {
		$invoice = vtlib_purify($input['invoiceid']);
	}
	if (isset($input['purchaseorderid'])) {
		$po = vtlib_purify($input['purchaseorderid']);
	}

	if (isset($date_closed_start) && $date_closed_start != '' && isset($date_closed_end) && $date_closed_end != '') {
		$where_clauses[] = 'vtiger_potential.closingdate >= '.$adb->quote($date_closed_start).' and vtiger_potential.closingdate <= '.$adb->quote($date_closed_end);
		$url_string .= '&closingdate_start='.$date_closed_start.'&closingdate_end='.$date_closed_end;
	}

	if (isset($sales_stage) && $sales_stage!='') {
		if ($sales_stage=='Other') {
			$where_clauses[] = "(vtiger_potential.sales_stage <> 'Closed Won' and vtiger_potential.sales_stage <> 'Closed Lost')";
		} else {
			$where_clauses[] = 'vtiger_potential.sales_stage = '.$adb->quote($sales_stage);
		}
		$url_string .= '&sales_stage='.$sales_stage;
	}
	if (isset($lead_source) && $lead_source != '') {
		$where_clauses[] = 'vtiger_potential.leadsource = '.$adb->quote($lead_source);
		$url_string .= '&leadsource='.$lead_source;
	}
	if (isset($date_closed) && $date_closed != '') {
		$where_clauses[] = $adb->getDBDateString('vtiger_potential.closingdate').' like '.$adb->quote($date_closed.'%').'';
		$url_string .= '&date_closed='.$date_closed;
	}
	if (isset($owner) && $owner != '') {
		$res = $adb->pquery('select vtiger_users.id from vtiger_users where vtiger_users.ename=?', array($owner));
		$uid = $adb->query_result($res, 0, 'id');
		$where_clauses[] = 'vtiger_crmentity.smownerid = '.$uid;
		$url_string .= '&owner='.$owner;
	}
	if (isset($campaign) && $campaign != '') {
		$where_clauses[] = 'vtiger_campaigncontrel.campaignid = '.$campaign;
		$url_string .= '&campaignid='.$campaign;
	}
	if (isset($quote) && $quote != '') {
		$where_clauses[] = 'vtiger_inventoryproductrel.id = '.$quote;
		$url_string .= '&quoteid='.$quote;
	}
	if (isset($invoice) && $invoice != '') {
		$where_clauses[] = 'vtiger_inventoryproductrel.id = '.$invoice;
		$url_string .= '&invoiceid='.$invoice;
	}
	if (isset($po) && $po != '') {
		$where_clauses[] = 'vtiger_inventoryproductrel.id = '.$po;
		$url_string .= '&purchaseorderid='.$po;
	}
	if (isset($input['from_homepagedb']) && $input['from_homepagedb'] != '') {
		$url_string .= '&from_homepagedb='.vtlib_purify($input['from_homepagedb']);
	}
	if (isset($input['type']) && $input['type'] != '') {
		$url_string .= '&type='.vtlib_purify($input['type']);
	}

	$where = '';
	foreach ($where_clauses as $clause) {
		if ($where != '') {
			$where .= ' and ';
		}
		$where .= $clause;
	}
	return $where.'#@@#'.$url_string;
}

/**This function is used to replace only the first occurence of a given string
Param $needle - string to be replaced
Param $replace - string to be replaced with
Param $replace - given string
Return type is string
*/
function str_replace_once($needle, $replace, $haystack) {
	// Looks for the first occurence of $needle in $haystack
	// and replaces it with $replace.
	$pos = strpos($haystack, $needle);
	if ($pos === false) {
		// Nothing found
		return $haystack;
	}
	return substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
 * Function to get the where condition for a module based on the field table entries
 * @param  string $listquery  -- ListView query for the module
 * @param  string $module     -- module name
 * @param  string $search_val -- entered search string value
 * @return string $where      -- where condition for the module based on field table entries
 */
function getUnifiedWhere($listquery, $module, $search_val, $fieldtype = '') {
	global $adb, $current_user;
	$userprivs = $current_user->getPrivileges();

	$search_val = $adb->sql_escape_string($search_val);
	if ($userprivs->hasGlobalReadPermission()) {
		if ($fieldtype=='') {
			$query = 'SELECT columnname, tablename, fieldname, uitype FROM vtiger_field WHERE tabid=? and vtiger_field.presence in (0,2)';
			$qparams = array(getTabid($module));
		} else {
			$query = 'SELECT columnname, tablename, fieldname, uitype
				FROM vtiger_field
				LEFT JOIN vtiger_ws_fieldtype ON vtiger_field.uitype=vtiger_ws_fieldtype.uitype
				WHERE tabid = ? and vtiger_field.presence in (0,2) and fieldtype=?';
			$qparams = array(getTabid($module), $fieldtype);
		}
	} else {
		$profileList = getCurrentUserProfileList();
		if ($fieldtype=='') {
			$query = 'SELECT columnname, tablename, fieldname, uitype
				FROM vtiger_field
				INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid = vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid = vtiger_field.fieldid
				WHERE vtiger_field.tabid = ? AND vtiger_profile2field.visible = 0 AND vtiger_profile2field.profileid IN ('.generateQuestionMarks($profileList)
					.') AND vtiger_def_org_field.visible = 0 and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid';
			$qparams = array(getTabid($module), $profileList);
		} else {
			$query = 'SELECT columnname, tablename, fieldname, uitype
				FROM vtiger_field
				INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid = vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid = vtiger_field.fieldid
				LEFT JOIN vtiger_ws_fieldtype ON vtiger_field.uitype=vtiger_ws_fieldtype.uitype
				WHERE vtiger_field.tabid = ? AND vtiger_profile2field.visible = 0 AND vtiger_profile2field.profileid IN ('.generateQuestionMarks($profileList)
					.') AND vtiger_def_org_field.visible = 0 and vtiger_field.presence in (0,2) and fieldtype=? GROUP BY vtiger_field.fieldid';
			$qparams = array(getTabid($module), $profileList, $fieldtype);
		}
	}
	$result = $adb->pquery($query, $qparams);
	$noofrows = $adb->num_rows($result);
	$binary_search = GlobalVariable::getVariable('Application_Global_Search_Binary', 0);
	$where = '';
	if ($noofrows==0) {
		$where = ' and false ';
	}
	for ($i=0; $i<$noofrows; $i++) {
		$columnname = $adb->query_result($result, $i, 'columnname');
		$tablename = $adb->query_result($result, $i, 'tablename');
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		$field_uitype = $adb->query_result($result, $i, 'uitype');

		// Search / Lookup customization
		if ($module == 'Contacts' && $columnname == 'accountid') {
			$columnname = 'accountname';
			$tablename = 'vtiger_account';
		}
		if ($module == 'HelpDesk' && $columnname == 'parent_id') {
			$columnname = 'accountname';
			$tablename = 'vtiger_account';
			if (false !== strpos($listquery, $tablename)) {
				if ($where != '') {
					$where .= ' OR ';
				}
				if ($binary_search) {
					$where .= 'LOWER('.$tablename.'.'.$columnname.") LIKE BINARY LOWER('". formatForSqlLike($search_val) ."')";
				} else {
					$where .= $tablename.'.'.$columnname." LIKE '". formatForSqlLike($search_val) ."'";
				}
			}
			$columnname = 'firstname';
			$tablename = 'vtiger_contactdetails';
		}

		//Before form the where condition, check whether the table for the field has been added in the listview query
		if (false !== strpos($listquery, $tablename)) {
			if ($where != '') {
				$where .= ' OR ';
			}
			if ($binary_search) {
				$where .= 'LOWER('.$tablename.'.'.$columnname.") LIKE BINARY LOWER('". formatForSqlLike($search_val) ."')";
			} else {
				if (is_uitype($field_uitype, '_picklist_') && hasMultiLanguageSupport($fieldname)) {
					$where .= '('.$tablename.'.'.$columnname.' IN (select translation_key from vtiger_cbtranslation
						where locale="'.$current_user->language.'" and forpicklist="'.$module.'::'.$fieldname.'" and i18n LIKE "'.formatForSqlLike($search_val).'") OR '
						.$tablename.'.'.$columnname.' LIKE "'. formatForSqlLike($search_val).'")';
				} else {
					$where .= $tablename.'.'.$columnname." LIKE '". formatForSqlLike($search_val) ."'";
				}
			}
		}
	}
	return $where;
}

function getAdvancedSearchCriteriaList($advft_criteria, $advft_criteria_groups, $module = '') {
	global $currentModule, $current_user;
	if (empty($module)) {
		$module = $currentModule;
	}

	$advfilterlist = array();

	$moduleHandler = vtws_getModuleHandlerFromName($module, $current_user);
	$moduleMeta = $moduleHandler->getMeta();
	$moduleFields = $moduleMeta->getModuleFields();
	if (is_array($advft_criteria)) {
		foreach ($advft_criteria as $column_condition) {
			if (empty($column_condition)) {
				continue;
			}

			$adv_filter_column = $column_condition['columnname'];
			$adv_filter_comparator = $column_condition['comparator'];
			$adv_filter_value = $column_condition['value'];
			$adv_filter_column_condition = $column_condition['columncondition'];
			$adv_filter_groupid = $column_condition['groupid'];

			$column_info = explode(':', $adv_filter_column);

			$fieldName = $column_info[2];
			$fieldObj = isset($moduleFields[$fieldName]) ? $moduleFields[$fieldName] : false;
			if (is_object($fieldObj)) {
				$fieldType = $fieldObj->getFieldDataType();

				if ($fieldType == 'currency') {
					// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
					if ($fieldObj->getUIType() == '72') {
						$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
					} else {
						$currencyField = new CurrencyField($adv_filter_value);
						if ($module == 'Potentials' && $fieldName == 'amount') {
							$currencyField->setNumberofDecimals(2);
						}
						$adv_filter_value = $currencyField->getDBInsertedValue();
					}
				}
			}
			$criteria = array();
			$criteria['columnname'] = $adv_filter_column;
			$criteria['comparator'] = $adv_filter_comparator;
			$criteria['value'] = $adv_filter_value;
			$criteria['column_condition'] = $adv_filter_column_condition;

			$advfilterlist[$adv_filter_groupid]['columns'][] = $criteria;
		}
	}
	if (is_array($advft_criteria_groups)) {
		foreach ($advft_criteria_groups as $group_index => $group_condition_info) {
			if (empty($group_condition_info)) {
				continue;
			}
			if (empty($advfilterlist[$group_index])) {
				continue;
			}
			$advfilterlist[$group_index]['condition'] = $group_condition_info['groupcondition'];
			$noOfGroupColumns = count($advfilterlist[$group_index]['columns']);
			if (!empty($advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'])) {
				$advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'] = '';
			}
		}
	}
	$noOfGroups = count($advfilterlist);
	if (!empty($advfilterlist[$noOfGroups]['condition'])) {
		$advfilterlist[$noOfGroups]['condition'] = '';
	}
	return $advfilterlist;
}

function generateAdvancedSearchSql($advfilterlist) {
	global $currentModule;

	$advfiltersql = $advcvsql = '';

	foreach ($advfilterlist as $groupinfo) {
		$groupcondition = (isset($groupinfo['condition']) ? $groupinfo['condition'] : '');
		$groupcolumns = $groupinfo['columns'];

		if (!empty($groupcolumns)) {
			$advfiltergroupsql = '';
			foreach ($groupcolumns as $columninfo) {
				$advorsql = array();
				$fieldcolname = $columninfo['columnname'];
				$comparator = $columninfo['comparator'];
				$value = $columninfo['value'];
				$columncondition = $columninfo['column_condition'];

				$columns = explode(':', $fieldcolname);
				$datatype = (isset($columns[4])) ? $columns[4] : '';

				if ($fieldcolname != '' && $comparator != '') {
					$valuearray = explode(',', trim($value));
					if (isset($valuearray) && !empty($valuearray) && $comparator != 'bw') {
						foreach ($valuearray as $val) {
							$advorsql[] = getAdvancedSearchValue($columns[0], $columns[1], $comparator, trim($val), $datatype);
						}
						//If negative logic filter ('not equal to', 'does not contain') is used, 'and' condition should be applied instead of 'or'
						if ($comparator == 'n' || $comparator == 'k' || $comparator == 'h' || $comparator == 'l') {
							$advorsqls = implode(' and ', $advorsql);
						} else {
							$advorsqls = implode(' or ', $advorsql);
						}
						$advfiltersql = ' ('.$advorsqls.') ';
					} elseif ($comparator == 'bw' && count($valuearray) == 2) {
						$advfiltersql = '('.$columns[0].'.'.$columns[1]." between '".getValidDBInsertDateTimeValue(trim($valuearray[0]))."' and '"
							.getValidDBInsertDateTimeValue(trim($valuearray[1]))."')";
					} else {
						if ($currentModule == 'Documents' && $columns[1]=='folderid') {
							$advfiltersql = 'vtiger_attachmentsfolder.foldername'.getAdvancedSearchComparator($comparator, trim($value), $datatype);
						} elseif ($currentModule == 'Assets') {
							if ($columns[1]=='account') {
								$advfiltersql = 'vtiger_account.accountname'.getAdvancedSearchComparator($comparator, trim($value), $datatype);
							}
							if ($columns[1]=='product') {
								$advfiltersql = 'vtiger_products.productname'.getAdvancedSearchComparator($comparator, trim($value), $datatype);
							}
							if ($columns[1]=='invoiceid') {
								$advfiltersql = 'vtiger_invoice.subject'.getAdvancedSearchComparator($comparator, trim($value), $datatype);
							}
						} else {
							$advfiltersql = getAdvancedSearchValue($columns[0], $columns[1], $comparator, trim($value), $datatype);
						}
					}
					$advfiltergroupsql .= $advfiltersql;
					if (!empty($columncondition)) {
						$advfiltergroupsql .= ' '.$columncondition.' ';
					}
				}
			}

			if (trim($advfiltergroupsql) != '') {
				$advfiltergroupsql = "( $advfiltergroupsql ) ";
				if (!empty($groupcondition)) {
					$advfiltergroupsql .= ' '. $groupcondition . ' ';
				}
				$advcvsql .= $advfiltergroupsql;
			}
		}
	}
	return $advcvsql;
}

function getAdvancedSearchComparator($comparator, $value, $datatype = '') {
	global $adb, $default_charset;
	$value=html_entity_decode(trim($value), ENT_QUOTES, $default_charset);
	$value = $adb->sql_escape_string($value);
	if ($datatype == 'DT' || $datatype == 'D') {
		$value = getValidDBInsertDateTimeValue($value);
	}

	if ($comparator == 'e') {
		if (trim($value) == 'NULL') {
			$rtvalue = ' is NULL';
		} elseif (trim($value) != '') {
			$rtvalue = ' = '.$adb->quote($value);
		} elseif (trim($value) == '' && ($datatype == 'V' || $datatype == 'E')) {
			$rtvalue = ' = '.$adb->quote($value);
		} else {
			$rtvalue = ' is NULL';
		}
	}
	if ($comparator == 'n') {
		if (trim($value) == 'NULL') {
			$rtvalue = ' is NOT NULL';
		} elseif (trim($value) != '') {
			$rtvalue = ' != '.$adb->quote($value);
		} elseif (trim($value) == '' && $datatype == 'V') {
			$rtvalue = ' != '.$adb->quote($value);
		} elseif (trim($value) == '' && $datatype == 'E') {
			$rtvalue = ' != '.$adb->quote($value);
		} else {
			$rtvalue = ' is NOT NULL';
		}
	}
	if ($comparator == 's') {
		if (trim($value) == '' && ($datatype == 'V' || $datatype == 'E')) {
			$rtvalue = " like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " like '". formatForSqlLike($value, 2) ."'";
		}
	}
	if ($comparator == 'ew') {
		if (trim($value) == '' && ($datatype == 'V' || $datatype == 'E')) {
			$rtvalue = " like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " like '". formatForSqlLike($value, 1) ."'";
		}
	}
	if ($comparator == 'c') {
		if (trim($value) == '' && ($datatype == 'V' || $datatype == 'E')) {
			$rtvalue = " like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " like '". formatForSqlLike($value) ."'";
		}
	}
	if ($comparator == 'k') {
		if (trim($value) == '' && ($datatype == 'V' || $datatype == 'E')) {
			$rtvalue = " not like ''";
		} else {
			$rtvalue = " not like '". formatForSqlLike($value) ."'";
		}
	}
	if ($comparator == 'dnsw') {
		if (trim($value) == '' && ($datatype == 'V' || $datatype == 'E')) {
			$rtvalue = " not like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " not like '". formatForSqlLike($value, 2) ."'";
		}
	}
	if ($comparator == 'dnew') {
		if (trim($value) == '' && ($datatype == 'V' || $datatype == 'E')) {
			$rtvalue = " not like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " not like '". formatForSqlLike($value, 1) ."'";
		}
	}
	if ($comparator == 'l') {
		$rtvalue = ' < '.$adb->quote($value);
	}
	if ($comparator == 'g') {
		$rtvalue = ' > '.$adb->quote($value);
	}
	if ($comparator == 'm') {
		$rtvalue = ' <= '.$adb->quote($value);
	}
	if ($comparator == 'h') {
		$rtvalue = ' >= '.$adb->quote($value);
	}
	if ($comparator == 'b') {
		$rtvalue = ' < '.$adb->quote($value);
	}
	if ($comparator == 'a') {
		$rtvalue = ' > '.$adb->quote($value);
	}
	return $rtvalue;
}

function getAdvancedSearchValue($tablename, $fieldname, $comparator, $value, $datatype, $webserviceQL = false) {
	//we have to add the fieldname/tablename.fieldname and the corresponding value (which we want).
	// So that when these LHS field comes then RHS value will be replaced for LHS in the where condition of the query
	global $adb, $currentModule, $current_user;
	//Added for proper check of contact name in advance filter
	if ($tablename == 'vtiger_contactdetails' && $fieldname == 'lastname') {
		$fieldname = 'contactid';
	}
	$fldname = $adb->pquery('select fieldname from vtiger_field where tablename=? and columnname=? and tabid=?', array($tablename, $fieldname, getTabid($currentModule)));
	if (!$fldname || $adb->num_rows($fldname)==0) {
		$fldname = $adb->pquery('select fieldname from vtiger_field where tablename=? and columnname=?', array($tablename, $fieldname));
	}
	if ($fldname && $adb->num_rows($fldname)>0) {
		$fld = $adb->query_result($fldname, 0, 0);
	} else {
		$fld = '';
	}
	$contactid = 'vtiger_contactdetails.lastname';
	if ($currentModule != 'Contacts' && $currentModule != 'Leads' && $currentModule != 'Campaigns') {
		$contactid = getSqlForNameInDisplayFormat(array('lastname'=>'vtiger_contactdetails.lastname', 'firstname'=>'vtiger_contactdetails.firstname'), 'Contacts');
	}
	$change_table_field = array(
		'product_id'=>'vtiger_products.productname',
		'contactid'=>$contactid,
		'contact_id'=>$contactid,
		'accountid'=>'',//in cvadvfilter accountname is stored for Contact, Potential, Quotes, SO, Invoice
		'account_id'=>'',//Same like accountid. No need to change
		'vendorid'=>'vtiger_vendor.vendorname',
		'vendor_id'=>'vtiger_vendor.vendorname',
		'potentialid'=>'vtiger_potential.potentialname',
		'vtiger_account.parentid'=>'vtiger_account2.accountname',
		'quoteid'=>'vtiger_quotes.subject',
		'salesorderid'=>'vtiger_salesorder.subject',
		'campaignid'=>'vtiger_campaign.campaignname',
		'vtiger_contactdetails.reportsto' => getSqlForNameInDisplayFormat(
			array('lastname' => 'vtiger_contactdetails2.lastname', 'firstname' => 'vtiger_contactdetails2.firstname'),
			'Contacts'
		),
		'vtiger_pricebook.currency_id'=>'vtiger_currency_info.currency_name',
	);
	if ($fieldname == 'smownerid' || $fieldname == 'modifiedby') {
		if ($webserviceQL) {
			$value = $fld.getAdvancedSearchComparator($comparator, $value, $datatype);
		} else {
			if ($fieldname == 'smownerid') {
				$tableNameSuffix = '';
			} elseif ($fieldname == 'modifiedby') {
				$tableNameSuffix = '2';
			}
			$temp_value = "( trim(vtiger_users$tableNameSuffix.ename)".getAdvancedSearchComparator($comparator, $value, $datatype);
			$temp_value.= " OR vtiger_groups$tableNameSuffix.groupname".getAdvancedSearchComparator($comparator, $value, $datatype);
			$value = $temp_value . ')';
		}
	} elseif ($fieldname == 'inventorymanager') {
		$value = $tablename.'.'.$fieldname.getAdvancedSearchComparator($comparator, getUserId_Ol($value), $datatype);
	} elseif (!empty($change_table_field[$fieldname])) { //Added to handle special cases
		$val = $change_table_field[$fieldname].getAdvancedSearchComparator($comparator, $value, $datatype);
		if (is_numeric($value) && in_array($fieldname, array('contactid', 'contact_id', 'potentialid', 'vendorid', 'vendor_id', 'campaignid'))) {
			$val = "$val OR $tablename.$fieldname=$value";
		}
		$value = $val;
	} elseif (!empty($change_table_field[$tablename.'.'.$fieldname])) { //Added to handle special cases
		$tmp_value = '';
		if ((($comparator=='e' || $comparator=='s' || $comparator=='c') && trim($value) == '') || (($comparator == 'n' || $comparator == 'k') && trim($value) != '')) {
			$tmp_value = $change_table_field[$tablename.'.'.$fieldname].' IS NULL or ';
		}
		$value = $tmp_value.$change_table_field[$tablename.'.'.$fieldname].getAdvancedSearchComparator($comparator, $value, $datatype);
	} else {
		$field_uitype = getUItype($currentModule, $fieldname);
		// For checkbox type values, we have to convert yes/no to 1/0 to get the values
		if ($field_uitype == 56) {
			if (strtolower($value) == 'yes') {
				$value = 1;
			} elseif (strtolower($value) ==  'no') {
				$value = 0;
			}
		}
		if ($comparator == 'e' && (trim($value) == 'NULL' || trim($value) == '')) {
			$value = '('.$tablename.'.'.$fieldname.' IS NULL OR '.$tablename.'.'.$fieldname.' = \'\')';
		} else {
			if ($webserviceQL) {
				$value = $fld.getAdvancedSearchComparator($comparator, $value, $datatype);
			} else {
				if (is_uitype($field_uitype, '_picklist_') && hasMultiLanguageSupport($fieldname)) {
					$value = $tablename.'.'.$fieldname.' IN (select translation_key from vtiger_cbtranslation
						where locale="'.$current_user->language.'" and forpicklist="'.$currentModule.'::'.$fld
						.'" and i18n '.getAdvancedSearchComparator($comparator, $value, $datatype).')'
						.(in_array($comparator, array('n', 'k', 'dnsw', 'dnew')) ? ' AND ' : ' OR ')
						.$tablename.'.'.$fieldname.getAdvancedSearchComparator($comparator, $value, $datatype);
				} else {
					$value = $tablename.'.'.$fieldname.getAdvancedSearchComparator($comparator, $value, $datatype);
				}
			}
		}
	}
	return $value;
}

/**
 * Function to get the Tags where condition
 * @param  string $search_val -- entered search string value
 * @param  string $current_user_id     -- current user id
 * @return string $where      -- where condition with the list of crmids, will like vtiger_crmentity.crmid in (1,3,4,etc.,)
 */
function getTagWhere($search_val, $current_user_id) {
	require_once 'include/freetag/freetag.class.php';

	$freetag_obj = new freetag();
	$crmid_array = $freetag_obj->get_objects_with_tag_all($search_val, $current_user_id);

	$where = ' vtiger_crmentity.crmid IN (';
	if (count($crmid_array) > 0) {
		foreach ($crmid_array as $crmid) {
			$where .= $crmid.',';
		}
		$where = trim($where, ',').')';
	} else {
		$where .= '0)'; // if there are no records we need to add some condition or search will return all the values
	}
	return $where;
}

/**
 * This function will return the script to set the start data and end date for the standard selection criteria
 * @return string
 */
function getCriteriaJS($formName) {
	$todayDateTime = new DateTimeField(date('Y-m-d H:i:s'));
	$bigbang = new DateTimeField(date('Y-m-d', mktime(0, 0, 0, '01', '01', '2000')));
	$priorToToday = new DateTimeField(date('Y-m-d'));

	$tomorrow = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+1, date('Y')));
	$tomorrowDateTime = new DateTimeField($tomorrow.' '. date('H:i:s'));

	$yesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));
	$yesterdayDateTime = new DateTimeField($yesterday.' '. date('H:i:s'));

	$currentmonth0 = date('Y-m-d', mktime(0, 0, 0, date('m'), '01', date('Y')));
	$currentMonthStartDateTime = new DateTimeField($currentmonth0.' '. date('H:i:s'));
	$currentmonth1 = date('Y-m-t');
	$currentMonthEndDateTime = new DateTimeField($currentmonth1.' '. date('H:i:s'));

	$lastmonth0 = date('Y-m-d', mktime(0, 0, 0, date('m')-1, '01', date('Y')));
	$lastMonthStartDateTime = new DateTimeField($lastmonth0.' '. date('H:i:s'));
	$lastmonth1 = date('Y-m-t', strtotime('-1 Month'));
	$lastMonthEndDateTime = new DateTimeField($lastmonth1.' '. date('H:i:s'));

	$nextmonth0 = date('Y-m-d', mktime(0, 0, 0, date('m')+1, '01', date('Y')));
	$nextMonthStartDateTime = new DateTimeField($nextmonth0.' '. date('H:i:s'));
	$nextmonth1 = date('Y-m-t', strtotime('+1 Month'));
	$nextMonthEndDateTime = new DateTimeField($nextmonth1.' '. date('H:i:s'));

	$lastweek0 = date('Y-m-d', strtotime('-2 week Sunday'));
	$lastWeekStartDateTime = new DateTimeField($lastweek0.' '. date('H:i:s'));
	$lastweek1 = date('Y-m-d', strtotime('-1 week Saturday'));
	$lastWeekEndDateTime = new DateTimeField($lastweek1.' '. date('H:i:s'));

	$thisweek0 = date('Y-m-d', strtotime('-1 week Sunday'));
	$thisWeekStartDateTime = new DateTimeField($thisweek0.' '. date('H:i:s'));
	$thisweek1 = date('Y-m-d', strtotime('this Saturday'));
	$thisWeekEndDateTime = new DateTimeField($thisweek1.' '. date('H:i:s'));

	$nextweek0 = date('Y-m-d', strtotime('this Sunday'));
	$nextWeekStartDateTime = new DateTimeField($nextweek0.' '. date('H:i:s'));
	$nextweek1 = date('Y-m-d', strtotime('+1 week Saturday'));
	$nextWeekEndDateTime = new DateTimeField($nextweek1.' '. date('H:i:s'));

	$next7days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+6, date('Y')));
	$next7DaysDateTime = new DateTimeField($next7days.' '. date('H:i:s'));

	$next30days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+29, date('Y')));
	$next30DaysDateTime = new DateTimeField($next30days.' '. date('H:i:s'));

	$next60days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+59, date('Y')));
	$next60DaysDateTime = new DateTimeField($next60days.' '. date('H:i:s'));

	$next90days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+89, date('Y')));
	$next90DaysDateTime = new DateTimeField($next90days.' '. date('H:i:s'));

	$next120days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+119, date('Y')));
	$next120DaysDateTime = new DateTimeField($next120days.' '. date('H:i:s'));

	$last7days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-6, date('Y')));
	$last7DaysDateTime = new DateTimeField($last7days.' '. date('H:i:s'));

	$last14days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-13, date('Y')));
	$last14DaysDateTime = new DateTimeField($last14days.' '. date('H:i:s'));

	$last30days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-29, date('Y')));
	$last30DaysDateTime = new DateTimeField($last30days.' '. date('H:i:s'));

	$last60days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-59, date('Y')));
	$last60DaysDateTime = new DateTimeField($last60days.' '. date('H:i:s'));

	$last90days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-89, date('Y')));
	$last90DaysDateTime = new DateTimeField($last90days.' '. date('H:i:s'));

	$last120days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-119, date('Y')));
	$last120DaysDateTime = new DateTimeField($last120days.' '. date('H:i:s'));

	$currentFY0 = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')));
	$currentFYStartDateTime = new DateTimeField($currentFY0.' '. date('H:i:s'));
	$currentFY1 = date('Y-m-t', mktime(0, 0, 0, '12', date('d'), date('Y')));
	$currentFYEndDateTime = new DateTimeField($currentFY1.' '. date('H:i:s'));

	$lastFY0 = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')-1));
	$lastFYStartDateTime = new DateTimeField($lastFY0.' '. date('H:i:s'));
	$lastFY1 = date('Y-m-t', mktime(0, 0, 0, '12', date('d'), date('Y')-1));
	$lastFYEndDateTime = new DateTimeField($lastFY1.' '. date('H:i:s'));

	$nextFY0 = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')+1));
	$nextFYStartDateTime = new DateTimeField($nextFY0.' '. date('H:i:s'));
	$nextFY1 = date('Y-m-t', mktime(0, 0, 0, '12', date('d'), date('Y')+1));
	$nextFYEndDateTime = new DateTimeField($nextFY1.' '. date('H:i:s'));

	if (date('m') <= 3) {
		$cFq = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')));
		$cFqStartDateTime = new DateTimeField($cFq.' '. date('H:i:s'));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '03', '31', date('Y')));
		$cFqEndDateTime = new DateTimeField($cFq1.' '. date('H:i:s'));

		$nFq = date('Y-m-d', mktime(0, 0, 0, '04', '01', date('Y')));
		$nFqStartDateTime = new DateTimeField($nFq.' '. date('H:i:s'));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '06', '30', date('Y')));
		$nFqEndDateTime = new DateTimeField($nFq1.' '. date('H:i:s'));

		$pFq = date('Y-m-d', mktime(0, 0, 0, '10', '01', date('Y')-1));
		$pFqStartDateTime = new DateTimeField($pFq.' '. date('H:i:s'));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')-1));
		$pFqEndDateTime = new DateTimeField($pFq1.' '. date('H:i:s'));
	} elseif (date('m') > 3 && date('m') <= 6) {
		$pFq = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')));
		$pFqStartDateTime = new DateTimeField($pFq.' '. date('H:i:s'));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '03', '31', date('Y')));
		$pFqEndDateTime = new DateTimeField($pFq1.' '. date('H:i:s'));

		$cFq = date('Y-m-d', mktime(0, 0, 0, '04', '01', date('Y')));
		$cFqStartDateTime = new DateTimeField($cFq.' '. date('H:i:s'));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '06', '30', date('Y')));
		$cFqEndDateTime = new DateTimeField($cFq1.' '. date('H:i:s'));

		$nFq = date('Y-m-d', mktime(0, 0, 0, '07', '01', date('Y')));
		$nFqStartDateTime = new DateTimeField($nFq.' '. date('H:i:s'));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '09', '30', date('Y')));
		$nFqEndDateTime = new DateTimeField($nFq1.' '. date('H:i:s'));
	} elseif (date('m') > 6 && date('m') <= 9) {
		$nFq = date('Y-m-d', mktime(0, 0, 0, '10', '01', date('Y')));
		$nFqStartDateTime = new DateTimeField($nFq.' '. date('H:i:s'));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')));
		$nFqEndDateTime = new DateTimeField($nFq1.' '. date('H:i:s'));

		$pFq = date('Y-m-d', mktime(0, 0, 0, '04', '01', date('Y')));
		$pFqStartDateTime = new DateTimeField($pFq.' '. date('H:i:s'));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '06', '30', date('Y')));
		$pFqEndDateTime = new DateTimeField($pFq1.' '. date('H:i:s'));

		$cFq = date('Y-m-d', mktime(0, 0, 0, '07', '01', date('Y')));
		$cFqStartDateTime = new DateTimeField($cFq.' '. date('H:i:s'));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '09', '30', date('Y')));
		$cFqEndDateTime = new DateTimeField($cFq1.' '. date('H:i:s'));
	} elseif (date('m') > 9 && date('m') <= 12) {
		$nFq = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')+1));
		$nFqStartDateTime = new DateTimeField($nFq.' '. date('H:i:s'));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '03', '31', date('Y')+1));
		$nFqEndDateTime = new DateTimeField($nFq1.' '. date('H:i:s'));

		$pFq = date('Y-m-d', mktime(0, 0, 0, '07', '01', date('Y')));
		$pFqStartDateTime = new DateTimeField($pFq.' '. date('H:i:s'));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '09', '30', date('Y')));
		$pFqEndDateTime = new DateTimeField($pFq1.' '. date('H:i:s'));

		$cFq = date('Y-m-d', mktime(0, 0, 0, '10', '01', date('Y')));
		$cFqStartDateTime = new DateTimeField($cFq.' '. date('H:i:s'));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')));
		$cFqEndDateTime = new DateTimeField($cFq1.' '. date('H:i:s'));
	}

	return '<script type="text/javaScript">
		function showDateRange( type ) {
			if (type!="custom") {
				document.'.$formName.'.startdate.readOnly=true
				document.'.$formName.'.enddate.readOnly=true
				getObj("jscal_trigger_date_start").style.visibility="hidden"
				getObj("jscal_trigger_date_end").style.visibility="hidden"
			} else {
				document.'.$formName.'.startdate.readOnly=false
				document.'.$formName.'.enddate.readOnly=false
				getObj("jscal_trigger_date_start").style.visibility="visible"
				getObj("jscal_trigger_date_end").style.visibility="visible"
			}
			if( type == "today" ) {
				document.'.$formName.'.startdate.value = "'.$todayDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$todayDateTime->getDisplayDate().'";
			} else if( type == "yesterday" ) {
				document.'.$formName.'.startdate.value = "'.$yesterdayDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$yesterdayDateTime->getDisplayDate().'";
			} else if( type == "tomorrow" ) {
				document.'.$formName.'.startdate.value = "'.$tomorrowDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$tomorrowDateTime->getDisplayDate().'";
			} else if( type == "thisweek" ) {
				document.'.$formName.'.startdate.value = "'.$thisWeekStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$thisWeekEndDateTime->getDisplayDate().'";
			} else if( type == "lastweek" ) {
				document.'.$formName.'.startdate.value = "'.$lastWeekStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$lastWeekEndDateTime->getDisplayDate().'";
			} else if( type == "nextweek" ) {
				document.'.$formName.'.startdate.value = "'.$nextWeekStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$nextWeekEndDateTime->getDisplayDate().'";
			} else if( type == "thismonth" ) {
				document.'.$formName.'.startdate.value = "'.$currentMonthStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$currentMonthEndDateTime->getDisplayDate().'";
			} else if( type == "lastmonth" ) {
				document.'.$formName.'.startdate.value = "'.$lastMonthStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$lastMonthEndDateTime->getDisplayDate().'";
			} else if( type == "priorToToday" ) {
				document.'.$formName.'.startdate.value = "'.$bigbang->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$yesterdayDateTime->getDisplayDate().'";
			} else if( type == "nextmonth" ) {
				document.'.$formName.'.startdate.value = "'.$nextMonthStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$nextMonthEndDateTime->getDisplayDate().'";
			} else if( type == "next7days" ) {
				document.'.$formName.'.startdate.value = "'.$todayDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$next7DaysDateTime->getDisplayDate().'";
			} else if( type == "next30days" ) {
				document.'.$formName.'.startdate.value = "'.$todayDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$next30DaysDateTime->getDisplayDate().'";
			} else if( type == "next60days" ) {
				document.'.$formName.'.startdate.value = "'.$todayDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$next60DaysDateTime->getDisplayDate().'";
			} else if( type == "next90days" ) {
				document.'.$formName.'.startdate.value = "'.$todayDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$next90DaysDateTime->getDisplayDate().'";
			} else if( type == "next120days" ) {
				document.'.$formName.'.startdate.value = "'.$todayDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$next120DaysDateTime->getDisplayDate().'";
			} else if( type == "last7days" ) {
				document.'.$formName.'.startdate.value = "'.$last7DaysDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$todayDateTime->getDisplayDate().'";
			} else if( type == "last14days" ) {
				document.'.$formName.'.startdate.value = "'.$last14DaysDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value =  "'.$todayDateTime->getDisplayDate().'";
			} else if( type == "last30days" ) {
				document.'.$formName.'.startdate.value = "'.$last30DaysDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$todayDateTime->getDisplayDate().'";
			} else if( type == "last60days" ) {
				document.'.$formName.'.startdate.value = "'.$last60DaysDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$todayDateTime->getDisplayDate().'";
			} else if( type == "last90days" ) {
				document.'.$formName.'.startdate.value = "'.$last90DaysDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$todayDateTime->getDisplayDate().'";
			} else if( type == "last120days" ) {
				document.'.$formName.'.startdate.value = "'.$last120DaysDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$todayDateTime->getDisplayDate().'";
			} else if( type == "thisfy" ) {
				document.'.$formName.'.startdate.value = "'.$currentFYStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$currentFYEndDateTime->getDisplayDate().'";
			} else if( type == "prevfy" ) {
				document.'.$formName.'.startdate.value = "'.$lastFYStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$lastFYEndDateTime->getDisplayDate().'";
			} else if( type == "nextfy" ) {
				document.'.$formName.'.startdate.value = "'.$nextFYStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$nextFYEndDateTime->getDisplayDate().'";
			} else if( type == "nextfq" ) {
				document.'.$formName.'.startdate.value = "'.$nFqStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$nFqEndDateTime->getDisplayDate().'";
			} else if( type == "prevfq" ) {
				document.'.$formName.'.startdate.value = "'.$pFqStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$pFqEndDateTime->getDisplayDate().'";
			} else if( type == "thisfq" ) {
				document.'.$formName.'.startdate.value = "'.$cFqStartDateTime->getDisplayDate().'";
				document.'.$formName.'.enddate.value = "'.$cFqEndDateTime->getDisplayDate().'";
			} else {
				document.'.$formName.'.startdate.value = "";
				document.'.$formName.'.enddate.value = "";
			}
		}
	</script>';
}

function getDateforStdFilterBytype($type) {
	$today = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
	$tomorrow  = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+1, date('Y')));
	$yesterday  = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));
	$bigbang = date('Y-m-d', mktime(0, 0, 0, '01', '01', '2000'));

	$currentmonth0 = date('Y-m-d', mktime(0, 0, 0, date('m'), '01', date('Y')));
	$currentmonth1 = date('Y-m-t');
	$lastmonth0 = date('Y-m-d', mktime(0, 0, 0, date('m')-1, '01', date('Y')));
	$lastmonth1 = date('Y-m-t', strtotime('-1 Month'));
	$nextmonth0 = date('Y-m-d', mktime(0, 0, 0, date('m')+1, '01', date('Y')));
	$nextmonth1 = date('Y-m-t', strtotime('+1 Month'));

	$lastweek0 = date('Y-m-d', strtotime('-2 week Sunday'));
	$lastweek1 = date('Y-m-d', strtotime('-1 week Saturday'));

	$thisweek0 = date('Y-m-d', strtotime('-1 week Sunday'));
	$thisweek1 = date('Y-m-d', strtotime('this Saturday'));

	$nextweek0 = date('Y-m-d', strtotime('this Sunday'));
	$nextweek1 = date('Y-m-d', strtotime('+1 week Saturday'));

	$next7days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+6, date('Y')));
	$next30days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+29, date('Y')));
	$next60days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+59, date('Y')));
	$next90days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+89, date('Y')));
	$next120days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+119, date('Y')));

	$last7days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-6, date('Y')));
	$last14days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-13, date('Y')));
	$last30days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-29, date('Y')));
	$last60days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-59, date('Y')));
	$last90days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-89, date('Y')));
	$last120days = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-119, date('Y')));

	$currentFY0 = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')));
	$currentFY1 = date('Y-m-t', mktime(0, 0, 0, '12', date('d'), date('Y')));
	$lastFY0 = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')-1));
	$lastFY1 = date('Y-m-t', mktime(0, 0, 0, '12', date('d'), date('Y')-1));
	$nextFY0 = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')+1));
	$nextFY1 = date('Y-m-t', mktime(0, 0, 0, '12', date('d'), date('Y')+1));

	if (date('m') <= 3) {
		$cFq = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '03', '31', date('Y')));
		$nFq = date('Y-m-d', mktime(0, 0, 0, '04', '01', date('Y')));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '06', '30', date('Y')));
		$pFq = date('Y-m-d', mktime(0, 0, 0, '10', '01', date('Y')-1));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')-1));
	} elseif (date('m') > 3 && date('m') <= 6) {
		$pFq = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '03', '31', date('Y')));
		$cFq = date('Y-m-d', mktime(0, 0, 0, '04', '01', date('Y')));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '06', '30', date('Y')));
		$nFq = date('Y-m-d', mktime(0, 0, 0, '07', '01', date('Y')));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '09', '30', date('Y')));
	} elseif (date('m') > 6 && date('m') <= 9) {
		$nFq = date('Y-m-d', mktime(0, 0, 0, '10', '01', date('Y')));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')));
		$pFq = date('Y-m-d', mktime(0, 0, 0, '04', '01', date('Y')));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '06', '30', date('Y')));
		$cFq = date('Y-m-d', mktime(0, 0, 0, '07', '01', date('Y')));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '09', '30', date('Y')));
	} elseif (date('m') > 9 && date('m') <= 12) {
		$nFq = date('Y-m-d', mktime(0, 0, 0, '01', '01', date('Y')+1));
		$nFq1 = date('Y-m-d', mktime(0, 0, 0, '03', '31', date('Y')+1));
		$pFq = date('Y-m-d', mktime(0, 0, 0, '07', '01', date('Y')));
		$pFq1 = date('Y-m-d', mktime(0, 0, 0, '09', '30', date('Y')));
		$cFq = date('Y-m-d', mktime(0, 0, 0, '10', '01', date('Y')));
		$cFq1 = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')));
	}

	if ($type == 'today') {
		$datevalue[0] = $today;
		$datevalue[1] = $today;
	} elseif ($type == 'yesterday') {
		$datevalue[0] = $yesterday;
		$datevalue[1] = $yesterday;
	} elseif ($type == 'tomorrow') {
		$datevalue[0] = $tomorrow;
		$datevalue[1] = $tomorrow;
	} elseif ($type == 'thisweek') {
		$datevalue[0] = $thisweek0;
		$datevalue[1] = $thisweek1;
	} elseif ($type == 'lastweek') {
		$datevalue[0] = $lastweek0;
		$datevalue[1] = $lastweek1;
	} elseif ($type == 'nextweek') {
		$datevalue[0] = $nextweek0;
		$datevalue[1] = $nextweek1;
	} elseif ($type == 'thismonth') {
		$datevalue[0] =$currentmonth0;
		$datevalue[1] = $currentmonth1;
	} elseif ($type == 'lastmonth') {
		$datevalue[0] = $lastmonth0;
		$datevalue[1] = $lastmonth1;
	} elseif ($type == 'nextmonth') {
		$datevalue[0] = $nextmonth0;
		$datevalue[1] = $nextmonth1;
	} elseif ($type == 'priorToToday') {
		$datevalue[0] = $bigbang;
		$datevalue[1] = $today;
	} elseif ($type == 'next7days') {
		$datevalue[0] = $today;
		$datevalue[1] = $next7days;
	} elseif ($type == 'next30days') {
		$datevalue[0] =$today;
		$datevalue[1] =$next30days;
	} elseif ($type == 'next60days') {
		$datevalue[0] = $today;
		$datevalue[1] = $next60days;
	} elseif ($type == 'next90days') {
		$datevalue[0] = $today;
		$datevalue[1] = $next90days;
	} elseif ($type == 'next120days') {
		$datevalue[0] = $today;
		$datevalue[1] = $next120days;
	} elseif ($type == 'last7days') {
		$datevalue[0] = $last7days;
		$datevalue[1] = $today;
	} elseif ($type == 'last14days') {
		$datevalue[0] = $last14days;
		$datevalue[1] = $today;
	} elseif ($type == 'last30days') {
		$datevalue[0] = $last30days;
		$datevalue[1] =  $today;
	} elseif ($type == 'last60days') {
		$datevalue[0] = $last60days;
		$datevalue[1] = $today;
	} elseif ($type == 'last90days') {
		$datevalue[0] = $last90days;
		$datevalue[1] = $today;
	} elseif ($type == 'last120days') {
		$datevalue[0] = $last120days;
		$datevalue[1] = $today;
	} elseif ($type == 'thisfy') {
		$datevalue[0] = $currentFY0;
		$datevalue[1] = $currentFY1;
	} elseif ($type == 'prevfy') {
		$datevalue[0] = $lastFY0;
		$datevalue[1] = $lastFY1;
	} elseif ($type == 'nextfy') {
		$datevalue[0] = $nextFY0;
		$datevalue[1] = $nextFY1;
	} elseif ($type == 'nextfq') {
		$datevalue[0] = $nFq;
		$datevalue[1] = $nFq1;
	} elseif ($type == 'prevfq') {
		$datevalue[0] = $pFq;
		$datevalue[1] = $pFq1;
	} elseif ($type == 'thisfq') {
		$datevalue[0] = $cFq;
		$datevalue[1] = $cFq1;
	} else {
		$datevalue[0] = '';
		$datevalue[1] = '';
	}
	return $datevalue;
}
?>
