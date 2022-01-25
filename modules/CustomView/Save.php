<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/logging.php';
require_once 'include/utils/utils.php';
require_once 'include/Webservices/upsert.php';
global $adb, $log, $current_user;

function cvGetNewViewName() {
	global $adb;
	$orgviewname = vtlib_purify($_REQUEST['viewName']);
	$viewname = $orgviewname;
	$cvmodule = vtlib_purify($_REQUEST['cvmodule']);
	$finished = false;
	$i = 1;
	while (!$finished) {
		$rscv = $adb->pquery('select 1 from vtiger_customview where entitytype=? and viewname=?', array($cvmodule, $viewname));
		if ($rscv && $adb->num_rows($rscv)==1) {
			$viewname = $orgviewname . '_' . $i;
			$i++;
		} else {
			$finished = true;
		}
	}
	return $viewname;
}

$cvid = (int) vtlib_purify($_REQUEST['record']);
if (!empty($_REQUEST['newsave'])) {
	unset($cvid);
	$_REQUEST['viewName'] = cvGetNewViewName();
	$_REQUEST['setStatus'] = CV_STATUS_PENDING;
}
$cvmodule = vtlib_purify($_REQUEST['cvmodule']);
$return_action = vtlib_purify($_REQUEST['return_action']);
if ($cvmodule != '') {
	$cv_tabid = getTabid($cvmodule);
	$viewname = isset($_REQUEST['viewName']) ? vtlib_purify($_REQUEST['viewName']) : '';
	$permit_all = isset($_REQUEST['permit_all']) ? vtlib_purify($_REQUEST['permit_all']) : 'false';
	if ($default_charset != 'UTF-8') {
		$viewname = htmlentities($viewname);
	}

	//setStatus=0(Default);1(Private);2(Pending);3(Public).
	//If status is Private ie. 1, only the user created the customview can see it
	//If status is Pending ie. 2, on approval by the admin, the status will become Public ie. 3 and a user can see the customviews created by him and his sub-ordinates.
	if (isset($_REQUEST['setStatus']) && $_REQUEST['setStatus'] != '' && $_REQUEST['setStatus'] != '1') {
		$status = $_REQUEST['setStatus'];
	} elseif (isset($_REQUEST['setStatus']) && $_REQUEST['setStatus'] != '' && $_REQUEST['setStatus'] == '1') {
		$status = CV_STATUS_PENDING;
	} else {
		$status = CV_STATUS_PRIVATE;
	}

	if (empty($_REQUEST['newsave']) && $status != CV_STATUS_PRIVATE) {
		$status = CV_STATUS_PENDING;
	}
	$userid = $current_user->id;

	if (isset($_REQUEST['setDefault'])) {
		$setdefault = 1;
	} else {
		$setdefault = 0;
	}

	if (isset($_REQUEST['setMetrics'])) {
		$setmetrics = 1;
	} else {
		$setmetrics = 0;
	}

	//this will cause only the chosen fields to be added to the vtiger_cvcolumnlist table
	$allKeys = array_keys($_REQUEST);

	//<<<<<<<columns>>>>>>>>>>
	for ($i=0; $i<count($allKeys); $i++) {
		$string = substr($allKeys[$i], 0, 6);
		if ($string == 'column' && $_REQUEST[$allKeys[$i]] != '') {
			// will cause only the chosen fields to be added to the vtiger_cvcolumnlist table
			$columnslist[] = $_REQUEST[$allKeys[$i]];
		}
	}
	//<<<<<<<columns>>>>>>>>>


	//<<<<<<<standardfilters>>>>>>>>>
	$std_filter_list = array();
	$stdfiltercolumn = isset($_REQUEST['stdDateFilterField']) ? $_REQUEST['stdDateFilterField'] : '';
	$std_filter_list['columnname'] = $stdfiltercolumn;
	$stdcriteria = isset($_REQUEST['stdDateFilter']) ? $_REQUEST['stdDateFilter'] : '';
	$std_filter_list['stdfilter'] = $stdcriteria;
	$startdate = isset($_REQUEST['startdate']) ? $_REQUEST['startdate'] : '';
	$enddate = isset($_REQUEST['enddate']) ?  $_REQUEST['enddate'] : '';
	if (empty($startdate) && empty($enddate)) {
		unset($std_filter_list);
	} else {
		$dbCurrentDateTime = new DateTimeField(date('Y-m-d H:i:s'));
		$startDateTime = new DateTimeField($startdate.' '. $dbCurrentDateTime->getDisplayTime());
		$endDateTime = new DateTimeField($enddate.' '. $dbCurrentDateTime->getDisplayTime());
		$std_filter_list['startdate'] = $startDateTime->getDBInsertDateValue();
		$std_filter_list['enddate'] = $endDateTime->getDBInsertDateValue();
	}
	//<<<<<<<standardfilters>>>>>>>>>

	//<<<<<<<advancedfilter>>>>>>>>>
	$advft_criteria = isset($_REQUEST['advft_criteria']) ? $_REQUEST['advft_criteria'] : '';
	$advft_criteria = json_decode($advft_criteria, true);

	$advft_criteria_groups = isset($_REQUEST['advft_criteria_groups']) ? $_REQUEST['advft_criteria_groups'] : '';
	$advft_criteria_groups = json_decode($advft_criteria_groups, true);
	//<<<<<<<advancedfilter>>>>>>>>

	$moduleHandler = vtws_getModuleHandlerFromName($cvmodule, $current_user);
	$moduleMeta = $moduleHandler->getMeta();
	$moduleFields = $moduleMeta->getModuleFields();
	if (empty($cvid)) {
		$genCVid = $adb->getUniqueID('vtiger_customview');
		if ($genCVid != '') {
			$customviewsql = 'INSERT INTO vtiger_customview(cvid, viewname, setdefault, setmetrics, entitytype, status, userid) VALUES (?,?,?,?,?,?,?)';
			$customviewparams = array($genCVid, $viewname, 0, $setmetrics, $cvmodule, $status, $userid);
			$customviewresult = $adb->pquery($customviewsql, $customviewparams);

			if ($setdefault == 1) {
				$sql_result = $adb->pquery('SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?', array($current_user->id, $cv_tabid));
				if ($adb->num_rows($sql_result) > 0) {
					$updatedefaultsql = 'UPDATE vtiger_user_module_preferences SET default_cvid = ? WHERE userid = ? and tabid = ?';
					$updatedefaultresult = $adb->pquery($updatedefaultsql, array($genCVid, $current_user->id, $cv_tabid));
				} else {
					$insertdefaultsql = 'INSERT INTO vtiger_user_module_preferences(userid, tabid, default_cvid) values (?,?,?)';
					$insertdefaultresult = $adb->pquery($insertdefaultsql, array($userid, $cv_tabid, $genCVid));
				}
			} else {
				$sql_result = $adb->pquery('SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?', array($current_user->id, $cv_tabid));
				if ($adb->num_rows($sql_result) > 0) {
					$deletedefaultsql = 'DELETE FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?';
					$deletedefaultresult = $adb->pquery($deletedefaultsql, array($current_user->id, $cv_tabid));
				}
			}

			if ($customviewresult && isset($columnslist)) {
				for ($i=0; $i<count($columnslist); $i++) {
					$columnsql = 'INSERT INTO vtiger_cvcolumnlist (cvid, columnindex, columnname) VALUES (?,?,?)';
					$columnparams = array($genCVid, $i, htmlentities($columnslist[$i]));
					$columnresult = $adb->pquery($columnsql, $columnparams);
				}
				if (!empty($std_filter_list['columnname'])) {
					$stdfiltersql = 'INSERT INTO vtiger_cvstdfilter(cvid,columnname,stdfilter,startdate,enddate) VALUES (?,?,?,?,?)';
					$stdfilterparams = array(
						$genCVid,
						$std_filter_list['columnname'],
						$std_filter_list['stdfilter'],
						$adb->formatDate($std_filter_list['startdate'], true),
						$adb->formatDate($std_filter_list['enddate'], true),
					);
					$stdfilterresult = $adb->pquery($stdfiltersql, $stdfilterparams);
				}

				if (is_array($advft_criteria)) {
					foreach ($advft_criteria as $column_index => $column_condition) {
						if (empty($column_condition)) {
							continue;
						}
						$adv_filter_column = htmlentities($column_condition['columnname']);
						$adv_filter_comparator = $column_condition['comparator'];
						$adv_filter_value = $column_condition['value'];
						$adv_filter_column_condition = $column_condition['columncondition'];
						$adv_filter_groupid = $column_condition['groupid'];

						$column_info = explode(':', $adv_filter_column);

						$fieldName = $column_info[2];
						if (!empty($moduleFields[$fieldName])) {
							$fieldObj = $moduleFields[$fieldName];
						} else {
							$minfo = explode('_', $column_info[3]);
							$rfhandler = vtws_getModuleHandlerFromName($minfo[0], $current_user);
							$rfmeta = $rfhandler->getMeta();
							$rffields = $rfmeta->getModuleFields();
							$fieldObj = $rffields[$fieldName];
						}
						$fieldType = $fieldObj->getFieldDataType();

						if ($fieldType == 'currency' || $fieldType == 'double') {
							$flduitype = $fieldObj->getUIType();
							if ($flduitype == '72' || $flduitype == 9 || $flduitype ==7) {
								$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
							} else {
								$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value);
							}
						}

						$temp_val = explode(',', $adv_filter_value);
						if (($fieldType == 'date' || ($fieldType == 'time' && $fieldName != 'time_start' && $fieldName != 'time_end') || ($fieldType == 'datetime'))
							&& ($fieldType != '' && $adv_filter_value != '')
						) {
							$val = array();
							for ($x=0; $x<count($temp_val); $x++) {
								// if date and time given then we have to convert the date and leave the time as it is
								// if date only given then time value will be empty
								if (trim($temp_val[$x]) != '') {
									$date = new DateTimeField(trim($temp_val[$x]));
									if ($fieldType == 'date' && $fieldObj->getUIType() != '6') {
										$val[$x] = DateTimeField::convertToDBFormat(
											trim($temp_val[$x])
										);
									} elseif ($fieldType == 'datetime' || $fieldObj->getUIType() == '6') {
										$val[$x] = $date->getDBInsertDateTimeValue();
									} else {
										$val[$x] = $date->getDBInsertTimeValue();
									}
								}
							}
							$adv_filter_value = implode(',', $val);
						}

						$irelcriteriasql = 'INSERT INTO vtiger_cvadvfilter(cvid,columnindex,columnname,comparator,value,groupid,column_condition) values (?,?,?,?,?,?,?)';
						$irelcriteriaresult = $adb->pquery(
							$irelcriteriasql,
							array(
								$genCVid,
								$column_index,
								$adv_filter_column,
								$adv_filter_comparator,
								$adv_filter_value,
								$adv_filter_groupid,
								$adv_filter_column_condition,
							)
						);

						// Update the condition expression for the group to which the condition column belongs
						$groupConditionExpression = '';
						if (!empty($advft_criteria_groups[$adv_filter_groupid]['conditionexpression'])) {
							$groupConditionExpression = $advft_criteria_groups[$adv_filter_groupid]['conditionexpression'];
						}
						$groupConditionExpression = $groupConditionExpression .' '. $column_index .' '. $adv_filter_column_condition;
						$advft_criteria_groups[$adv_filter_groupid]['conditionexpression'] = $groupConditionExpression;
					}
				}

				if (is_array($advft_criteria_groups)) {
					foreach ($advft_criteria_groups as $group_index => $group_condition_info) {
						if (empty($group_condition_info)) {
							continue;
						}
						if (empty($group_condition_info['conditionexpression'])) {
							continue; // Case when the group doesn't have any column criteria
						}

						$irelcriteriagroupsql = 'insert into vtiger_cvadvfilter_grouping(groupid,cvid,group_condition,condition_expression) values (?,?,?,?)';
						$irelcriteriagroupresult = $adb->pquery(
							$irelcriteriagroupsql,
							array(
								$group_index,
								$genCVid,
								htmlentities($group_condition_info['groupcondition']),
								htmlentities($group_condition_info['conditionexpression']),
							)
						);
					}
				}
			}
			$cvid = $genCVid;
		}
	} else {
		if (is_admin($current_user) || $current_user->id) {
			if ($permit_all === 'true') {
				$viewname = 'All';
				$status = 0;
			}
			$updatecvsql = 'UPDATE vtiger_customview SET viewname = ?, setmetrics = ?, status = ? WHERE cvid = ?';
			$updatecvparams = array($viewname, $setmetrics, $status, $cvid);
			$updatecvresult = $adb->pquery($updatecvsql, $updatecvparams);

			if ($setdefault == 1) {
				$sql_result = $adb->pquery('SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?', array($current_user->id, $cv_tabid));
				if ($adb->num_rows($sql_result) > 0) {
					$updatedefaultsql = 'UPDATE vtiger_user_module_preferences SET default_cvid = ? WHERE userid = ? and tabid = ?';
					$updatedefaultresult = $adb->pquery($updatedefaultsql, array($cvid, $current_user->id, $cv_tabid));
				} else {
					$insertdefaultsql = 'INSERT INTO vtiger_user_module_preferences(userid, tabid, default_cvid) values (?,?,?)';
					$insertdefaultresult = $adb->pquery($insertdefaultsql, array($userid, $cv_tabid, $cvid));
				}
			} else {
				$sql_result = $adb->pquery('SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?', array($current_user->id, $cv_tabid));
				if ($adb->num_rows($sql_result) > 0) {
					$deletedefaultsql = 'DELETE FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?';
					$deletedefaultresult = $adb->pquery($deletedefaultsql, array($current_user->id, $cv_tabid));
				}
			}

			$deletesql = 'DELETE FROM vtiger_cvcolumnlist WHERE cvid = ?';
			$deleteresult = $adb->pquery($deletesql, array($cvid));

			$deletesql = 'DELETE FROM vtiger_cvstdfilter WHERE cvid = ?';
			$deleteresult = $adb->pquery($deletesql, array($cvid));

			$deletesql = 'DELETE FROM vtiger_cvadvfilter WHERE cvid = ?';
			$deleteresult = $adb->pquery($deletesql, array($cvid));

			$deletesql = 'DELETE FROM vtiger_cvadvfilter_grouping WHERE cvid = ?';
			$deleteresult = $adb->pquery($deletesql, array($cvid));


			$genCVid = $cvid;
			if ($updatecvresult && isset($columnslist)) {
				for ($i=0; $i<count($columnslist); $i++) {
					$columnsql = 'INSERT INTO vtiger_cvcolumnlist (cvid, columnindex, columnname) VALUES (?,?,?)';
					$columnparams = array($genCVid, $i, htmlentities($columnslist[$i]));
					$columnresult = $adb->pquery($columnsql, $columnparams);
				}
				if (!empty($std_filter_list['columnname'])) {
					$stdfiltersql = 'INSERT INTO vtiger_cvstdfilter (cvid,columnname,stdfilter,startdate,enddate) VALUES (?,?,?,?,?)';
					$stdfilterparams = array(
						$genCVid,
						$std_filter_list['columnname'],
						$std_filter_list['stdfilter'],
						$adb->formatDate($std_filter_list['startdate'], true),
						$adb->formatDate($std_filter_list['enddate'], true),
					);
					$stdfilterresult = $adb->pquery($stdfiltersql, $stdfilterparams);
				}
				if (is_array($advft_criteria)) {
					foreach ($advft_criteria as $column_index => $column_condition) {
						if (empty($column_condition)) {
							continue;
						}

						$adv_filter_column = htmlentities($column_condition['columnname']);
						$adv_filter_comparator = $column_condition['comparator'];
						$adv_filter_value = $column_condition['value'];
						$adv_filter_column_condition = $column_condition['columncondition'];
						$adv_filter_groupid = $column_condition['groupid'];

						$column_info = explode(':', $adv_filter_column);

						$fieldName = $column_info[2];
						if (!empty($moduleFields[$fieldName])) {
							$fieldObj = $moduleFields[$fieldName];
						} else {
							$minfo = explode('_', $column_info[3]);
							$rfhandler = vtws_getModuleHandlerFromName($minfo[0], $current_user);
							$rfmeta = $rfhandler->getMeta();
							$rffields = $rfmeta->getModuleFields();
							$fieldObj = $rffields[$fieldName];
						}
						$fieldType = $fieldObj->getFieldDataType();

						if ($fieldType == 'currency' || $fieldType == 'double') {
							// Some currency fields like Unit Price, Total, Sub-total etc of Inventory modules and normal numbers do not need currency conversion
							$flduitype = $fieldObj->getUIType();
							if ($flduitype == '72' || $flduitype == 9 || $flduitype ==7) {
								$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
							} else {
								$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value);
							}
						}

						$temp_val = explode(',', $adv_filter_value);
						if (($fieldType == 'date' || ($fieldType == 'time' && $fieldName != 'time_start' && $fieldName != 'time_end') || ($fieldType == 'datetime'))
							&& ($fieldType != '' && $adv_filter_value != '')
						) {
							$val = array();
							for ($x=0; $x<count($temp_val); $x++) {
								//if date and time given then we have to convert the date and
								//leave the time as it is, if date only given then temp_time
								//value will be empty
								if (trim($temp_val[$x]) != '') {
									$date = new DateTimeField(trim($temp_val[$x]));
									if ($fieldType == 'date' && $fieldObj->getUIType() != '6') {
										$val[$x] = DateTimeField::convertToDBFormat(
											trim($temp_val[$x])
										);
									} elseif ($fieldType == 'datetime' || $fieldObj->getUIType() == '6') {
										$val[$x] = $date->getDBInsertDateTimeValue();
									} else {
										$val[$x] = $date->getDBInsertTimeValue();
									}
								}
							}
							$adv_filter_value = implode(',', $val);
						}

						$irelcriteriasql = 'INSERT INTO vtiger_cvadvfilter(cvid,columnindex,columnname,comparator,value,groupid,column_condition) values (?,?,?,?,?,?,?)';
						$irelcriteriaresult = $adb->pquery(
							$irelcriteriasql,
							array(
								$genCVid,
								$column_index,
								$adv_filter_column,
								$adv_filter_comparator,
								$adv_filter_value,
								$adv_filter_groupid,
								$adv_filter_column_condition,
							)
						);

						// Update the condition expression for the group to which the condition column belongs
						$groupConditionExpression = '';
						if (!empty($advft_criteria_groups[$adv_filter_groupid]['conditionexpression'])) {
							$groupConditionExpression = $advft_criteria_groups[$adv_filter_groupid]['conditionexpression'];
						}
						$groupConditionExpression = $groupConditionExpression .' '. $column_index .' '. $adv_filter_column_condition;
						$advft_criteria_groups[$adv_filter_groupid]['conditionexpression'] = $groupConditionExpression;
					}
				}
				if (is_array($advft_criteria_groups)) {
					foreach ($advft_criteria_groups as $group_index => $group_condition_info) {
						if (empty($group_condition_info)) {
							continue;
						}
						if (empty($group_condition_info['conditionexpression'])) {
							continue; // Case when the group doesn't have any column criteria
						}

						$irelcriteriagroupsql = 'insert into vtiger_cvadvfilter_grouping(groupid,cvid,group_condition,condition_expression) values (?,?,?,?)';
						$irelcriteriagroupresult = $adb->pquery(
							$irelcriteriagroupsql,
							array(
								$group_index,
								$genCVid,
								htmlentities($group_condition_info['groupcondition']),
								htmlentities($group_condition_info['conditionexpression']),
							)
						);
					}
				}
			}
		}
	}
	if ($status == CV_STATUS_PENDING) {
		$setpublic = 1;
	} else {
		$setpublic = 0;
	}
	$roleid = $current_user->roleid;
	$subrole = implode('|##|', getRoleSubordinates($roleid));
	$default_values =  array(
		'cvid' => $cvid,
		'cvcreate' => '0',
		'cvretrieve' => '1',
		'cvupdate' => '1',
		'cvdelete' => '1',
		'cvdefault' => $setdefault,
		'cvapprove' =>'0',
		'setpublic' => $setpublic,
		'mandatory' => '0',
		'module_list' => $cvmodule,
		'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
		'cvrole' => $subrole
	);
	$searchOn = 'cvid';
	$updatedfields = 'cvdefault,setpublic';
	vtws_upsert('cbCVManagement', $default_values, $searchOn, $updatedfields, $current_user);
}

header('Location: index.php?action='.urlencode($return_action).'&module='.urlencode($cvmodule).'&viewname='.urlencode($cvid));
?>