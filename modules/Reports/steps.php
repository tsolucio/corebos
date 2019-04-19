<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'include/utils/utils.php';
require_once 'modules/Reports/Reports.php';

header('Content-Type: application/json');

if (isset($_REQUEST['step']) && !empty($_REQUEST['step'])) {
	$step = vtlib_purify($_REQUEST['step']);
	if (!empty($_REQUEST['record'])) {
		$recordid = vtlib_purify($_REQUEST['record']);
		// CREATE NEW REPORT OBJECT FROM REPORT ID
		$oReport = new Reports($recordid);
		$primarymodule = $oReport->primodule;
		if ($oReport->cbreporttype=='crosstabsql') {
			$rs = $adb->pquery('select moreinfo from vtiger_report where reportid=?', array($recordid));
			$minfo = $adb->query_result($rs, 0, 0);
			$minfo = unserialize($minfo);
		}
	} else {
		$oReport = new Reports();
		$primarymodule = vtlib_purify($_REQUEST['primarymodule']);
		$oReport->primodule = $primarymodule;
	}

	if ($step == 3) {
		$secondarymodule = '';
		if (isset($recordid)) {
			$secondarymodule = '';

			// GET SECONDARY MODULES
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$permission = $get_secondmodules[0];
			$secondarymodule = $get_secondmodules[1];

			$oReport->secmodule = $secondarymodule;
			$reporttype = $oReport->reporttype;
		} else {
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$reporttype = "tabular";
			$permission = $get_secondmodules[0];
			$secondarymodule = $get_secondmodules[1];
		}

		if (isPermitted($primarymodule, 'index') == 'yes' && $permission!=false) {
			$response = array(
				'permission'=>1,
				'primarymodule' => $primarymodule,
				'selectedreporttype'=>$reporttype,
				'secondarymodule'=>$secondarymodule
			);
			echo json_encode($response);
		} else {
			echo json_encode(array("permission"=>0,"primarymodule" => $primarymodule,"secondarymodule"=>$secondarymodule));
		}
	} //  ======== STEP 4 ==========
	elseif ($step == 4) {
		if (isset($_REQUEST['cbreporttype']) && ($_REQUEST['cbreporttype']=='external' || $_REQUEST['cbreporttype']=='directsql')) {
			echo json_encode(array('permission'=>1));
			die();
		}
		if (isset($recordid)) {
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$reporttype = "tabular";
			$secondarymodule = $get_secondmodules[1];
			$permission = $get_secondmodules[0];

			$oReport->secmodule = $secondarymodule;
			$BLOCK1 = getPrimaryColumnsHTML($primarymodule, $secondarymodule);
			$AGGFIELDS = $BLOCK1;
			$SECMODFIELDS = (array)getSecondaryColumnsHTML($secondarymodule);
			$BLOCK1 = array_merge((array)$BLOCK1, $SECMODFIELDS);

			$BLOCK2 = $oReport->getSelectedColumnsList($recordid);
			if ($permission == false) {
				echo json_encode(array("permission"=>0));
			} else {
				if (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype']=='crosstabsql') {
					echo json_encode(array(
						'permission'=>1,
						'BLOCK1' => $SECMODFIELDS,
						'BLOCK2' => $BLOCK2,
						'AGGFIELDS' => $AGGFIELDS,
						'pivotfield'=> $minfo['pivotfield'],
						'aggfield' => $minfo['pivotfield'],
						'crosstabaggfunction' => $minfo['crosstabaggfunction'],
					));
				} else {
					echo json_encode(array("permission"=>1,"BLOCK1"=>$BLOCK1,"BLOCK2"=>$BLOCK2));
				}
			}
		} else {
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$permission = $get_secondmodules[0];
			$secondarymodule = $get_secondmodules[1];
			$BLOCK1 = getPrimaryColumnsHTML($primarymodule, $secondarymodule);
			$AGGFIELDS = $BLOCK1;
			$SECMODFIELDS = (array)getSecondaryColumnsHTML($secondarymodule);
			$BLOCK1 = array_merge((array)$BLOCK1, $SECMODFIELDS);
			if ($permission == false) {
				echo json_encode(array('permission'=>0));
			} else {
				if (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype']=='crosstabsql') {
					echo json_encode(array('permission'=>1,'BLOCK1'=>$SECMODFIELDS,'AGGFIELDS'=>$AGGFIELDS));
				} else {
					echo json_encode(array('permission'=>1,'BLOCK1'=>$BLOCK1));
				}
			}
		}
	} elseif ($step == 5) {
		if (isset($recordid)) {
			$secondarymodule = '';
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$secondarymodule = $get_secondmodules[1];
			$permission = $get_secondmodules[0];

			$oReport->secmodule = $secondarymodule;
			$BLOCK1 = $oReport->sgetColumntoTotalSelected($oReport->primodule, $oReport->secmodule, $recordid);
			if (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype']=='crosstabsql') {
				echo json_encode(array(
					'aggfield' => $minfo['aggfield'],
					'crosstabaggfunction' => $minfo['crosstabaggfunction'],
				));
			} else {
				echo json_encode(array("BLOCK1"=>$BLOCK1));
			}
		} else {
			$oReport = new Reports();
			$secondarymodule = array();
			$ogReport = new Reports();
			if (!empty($ogReport->related_modules[$primarymodule])) {
				foreach ($ogReport->related_modules[$primarymodule] as $key => $value) {
					if (isset($_REQUEST['secondarymodule_'.$value])) {
						$secondarymodule[] = vtlib_purify($_REQUEST['secondarymodule_'.$value]);
					}
				}
			}
			$BLOCK1 = $oReport->sgetColumntoTotal($primarymodule, $secondarymodule);
			echo json_encode(array("BLOCK1"=>$BLOCK1));
		}
	} elseif ($step == 6) {
		require_once 'modules/CustomView/CustomView.php';

		if (isset($recordid)) {
			//added to fix the ticket #5117
			global $current_user;

			$oReport->getSelectedStandardCriteria($recordid);
			$oReport->getAdvancedFilterList($recordid);
			$secondarymodule = '';
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$secondarymodule = $get_secondmodules[1];
			if ($secondarymodule!='') {
				$oReport->secmodule = $secondarymodule;
			}

			$BLOCK1 = getPrimaryStdFilterHTML($oReport->primodule, $oReport->stdselectedcolumn);
			$BLOCK1 =array_merge((array)$BLOCK1, (array)getSecondaryStdFilterHTML($oReport->secmodule, $oReport->stdselectedcolumn));
			//added to fix the ticket #5117
			$selectedcolumnvalue = '"'. $oReport->stdselectedcolumn . '"';
			if (!is_admin($current_user) && isset($oReport->stdselectedcolumn) && strpos($BLOCK1, $selectedcolumnvalue) === false) {
				$BLOCK1 = array_merge((array)$BLOCK1, array("selected"=>true,"value"=>"Not Accessible","label"=>$app_strings['LBL_NOT_ACCESSIBLE']));
			}

			$BLOCKCRITERIA = $oReport->getSelectedStdFilterCriteria($oReport->stdselectedfilter);
			// ADV FILTERS
			$COLUMNS_BLOCK = getPrimaryColumns_AdvFilterHTML($oReport->primodule);
			$COLUMNS_BLOCK =array_merge((array)$COLUMNS_BLOCK, (array)getSecondaryColumns_AdvFilterHTML($oReport->secmodule));
			$FILTER_OPTION = Reports::getAdvCriteriaHTML();
			$rel_fields = getRelatedFieldColumns();

			echo json_encode(
				array(
					'BLOCKJS'=>$BLOCK1,
					'BLOCKCRITERIA'=>$BLOCKCRITERIA,
					'STARTDATE'=>$oReport->startdate,
					'ENDDATE'=>$oReport->enddate,
					'COLUMNS_BLOCK'=>$COLUMNS_BLOCK,
					'FOPTION'=>$FILTER_OPTION,
					'REL_FIELDS'=>json_encode($rel_fields),
					'CRITERIA_GROUPS' => $oReport->advft_criteria
				)
			);
		} else {
			$BLOCK1 = getPrimaryStdFilterHTML($primarymodule);
			// ADV FILTERS
			$COLUMNS_BLOCK = getPrimaryColumns_AdvFilterHTML($primarymodule);

			if (!empty($oReport->related_modules[$primarymodule])) {
				foreach ($oReport->related_modules[$primarymodule] as $key => $value) {
					if (isset($_REQUEST['secondarymodule_'.$value])) {
						$BLOCK1 = array_merge((array)$BLOCK1, (array)getSecondaryStdFilterHTML($_REQUEST["secondarymodule_".$value]));
						$COLUMNS_BLOCK = array_merge((array)$COLUMNS_BLOCK, (array)getSecondaryColumns_AdvFilterHTML($_REQUEST["secondarymodule_".$value]));
					}
				}
			}

			$secondarymodule = '';
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$secondarymodule = $get_secondmodules[1];
			if ($secondarymodule!='') {
				$oReport->secmodule = $secondarymodule;
			}

			$BLOCKCRITERIA = $oReport->getSelectedStdFilterCriteria();
			$rel_fields = getRelatedFieldColumns();
			echo json_encode(
				array(
				'BLOCKJS'=>$BLOCK1,
				'BLOCKCRITERIA'=>$BLOCKCRITERIA,
				'COLUMNS_BLOCK'=>$COLUMNS_BLOCK,
				'REL_FIELDS'=>json_encode($rel_fields)
				)
			);
		}
	} elseif ($step == 7) {
		$result = array();
		$roleid = $current_user->column_fields['roleid'];
		$user_array = getAllUserName();
		asort($user_array);
		$userIdStr = "";
		$userNameStr = "";
		$m=0;
		foreach ($user_array as $userid => $username) {
			if ($userid != $current_user->id) {
				if ($m != 0) {
					$userIdStr .= ",";
					$userNameStr .= ",";
				}
				$userIdStr .= $userid;
				$userNameStr .="'".addslashes(decode_html($username))."'";
				$m++;
			}
		}

		$user_groups = getAllGroupName();
		asort($user_groups);
		$groupIdStr = "";
		$groupNameStr = "";
		$l=0;
		foreach ($user_groups as $grpid => $groupname) {
			if ($l != 0) {
				$groupIdStr .= ",";
				$groupNameStr .= ",";
			}
			$groupIdStr .= $grpid;
			$groupNameStr .= "'".addslashes(decode_html($groupname))."'";
			$l++;
		}

		$result['GROUPNAMESTR'] = $groupNameStr;
		$result['USERNAMESTR'] = $userNameStr;
		$result['GROUPIDSTR'] = $groupIdStr;
		$result['USERIDSTR'] = $userIdStr;
		if (isset($recordid)) {
			$result['VISIBLECRITERIA'] = getVisibleCriteria($recordid);
			$result['MEMBER'] = getShareInfo($recordid, false);
		} else {
			$result['VISIBLECRITERIA'] = getVisibleCriteria();
		}
		echo json_encode($result);
	} elseif ($step == 'grouping') {
		if (isset($recordid)) {
			$list_array = $oReport->getSelctedSortingColumns($recordid);

			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$permission = $get_secondmodules[0];
			$secondarymodule = $get_secondmodules[1];
			$oReport->secmodule = $secondarymodule;

			$BLOCK1 = getPrimaryColumns_GroupingHTML($primarymodule, (isset($list_array[0]) ? $list_array[0] : ''));
			$BLOCK1 = array_merge((array)$BLOCK1, (array)getSecondaryColumns_GroupingHTML($oReport->secmodule, (isset($list_array[0]) ? $list_array[0] : '')));
			$GROUPBYTIME1 = getGroupByTimeDiv(1, $recordid);

			$BLOCK2 = getPrimaryColumns_GroupingHTML($primarymodule, (isset($list_array[1]) ? $list_array[1] : ''));
			$BLOCK2 = array_merge((array)$BLOCK2, (array)getSecondaryColumns_GroupingHTML($oReport->secmodule, (isset($list_array[1]) ? $list_array[1] : '')));
			$GROUPBYTIME2 = getGroupByTimeDiv(2, $recordid);

			$BLOCK3 = getPrimaryColumns_GroupingHTML($primarymodule, (isset($list_array[2]) ? $list_array[2] : ''));
			$BLOCK3 = array_merge((array)$BLOCK3, (array)getSecondaryColumns_GroupingHTML($oReport->secmodule, (isset($list_array[2]) ? $list_array[2] : '')));
			$GROUPBYTIME3 = getGroupByTimeDiv(3, $recordid);

			$sortorder = $oReport->ascdescorder;
			$ASCDESC1 = getOrderGrouping((isset($sortorder[0]) ? $sortorder[0] : ''));
			$ASCDESC2 = getOrderGrouping((isset($sortorder[1]) ? $sortorder[1] : ''));
			$ASCDESC3 = getOrderGrouping((isset($sortorder[2]) ? $sortorder[2] : ''));
			header('Content-Type: application/json');
			echo json_encode(
				array(
					'BLOCK1' => $BLOCK1,
					'BLOCK2' => $BLOCK2,
					'BLOCK3' => $BLOCK3,
					'ORDER1' => $ASCDESC1,
					'ORDER2' => $ASCDESC2,
					'ORDER3' => $ASCDESC3,
					'GRBYTIME1' => $GROUPBYTIME1,
					'GRBYTIME2' => $GROUPBYTIME2,
					'GRBYTIME3' => $GROUPBYTIME3
				)
			);
		} else {
			$get_secondmodules = get_Secondmodules($oReport, $primarymodule);
			$permission = $get_secondmodules[0];
			$secondarymodule = $get_secondmodules[1];
			$oReport->secmodule = $secondarymodule;

			$BLOCK1 = getPrimaryColumns_GroupingHTML($primarymodule);
			if (!empty($oReport->secmodule)) {
				$BLOCK1 = array_merge((array)$BLOCK1, (array)getSecondaryColumns_GroupingHTML($oReport->secmodule, ''));
			}
			$ASCDESC = getOrderGrouping();
			$GROUPBYTIME1 = getGroupByTimeDiv(1);
			$GROUPBYTIME2 = getGroupByTimeDiv(2);
			$GROUPBYTIME3 = getGroupByTimeDiv(3);

			echo json_encode(
				array(
					'BLOCK1' => $BLOCK1,
					'BLOCK2' => $BLOCK1,
					'BLOCK3' => $BLOCK1,
					'ORDER1' => $ASCDESC,
					'ORDER2' => $ASCDESC,
					'ORDER3' => $ASCDESC,
					'GRBYTIME1' => $GROUPBYTIME1,
					'GRBYTIME2' => $GROUPBYTIME2,
					'GRBYTIME3' => $GROUPBYTIME3
				)
			);
		}
	}
}

/**
 * @param  $oReport
 * @param  $primarymodule
 * @return array
 */
function get_Secondmodules($oReport, $primarymodule) {
	$permission = true;
	$secondarymodules =array();
	if (!empty($oReport->related_modules[$primarymodule])) {
		foreach ($oReport->related_modules[$primarymodule] as $value) {
			if (isset($_REQUEST['secondarymodule_'.$value])) {
				$secondarymodules []= $_REQUEST['secondarymodule_'.$value];
				$oReport->getSecModuleColumnsList($_REQUEST['secondarymodule_'.$value]);
				if (!isPermitted($_REQUEST['secondarymodule_'.$value], 'index')== 'yes' && !empty($_REQUEST['secondarymodule_'.$value])) {
					$permission = false;
				}
			}
		}
	}
	$secondarymodule = implode(':', $secondarymodules);
	return array($permission,$secondarymodule);
}

/** Function to formulate the fields for the primary modules
 *  This function accepts the module name as an argument and generates
 *  the fields for the primary module as an HTML Combo values
 */
function getPrimaryColumnsHTML($module, $secondmodule) {
	$id_added=false;
	$ogReport = new Reports();
	$ogReport->getPriModuleColumnsList($module);
	$ogReport->getSecModuleColumnsList($secondmodule);

	$block_listed = array();
	$modules_list = array();
	foreach ($ogReport->module_list[$module] as $value) {
		$modules_optgroup = array();
		if (isset($ogReport->pri_module_columnslist[$module][$value]) && !isset($block_listed[$value])) {
			$block_listed[$value] = true;
			if ($id_added==false) {
				$v = "vtiger_crmentity:crmid:".$module."_ID:crmid:I";
				$label = getTranslatedString($module.' ID', $module);
				$modules_optgroup[] = array("value"=>$v,"label"=>$label);
				$id_added=true;
			}
			foreach ($ogReport->pri_module_columnslist[$module][$value] as $field => $fieldlabel) {
				$modules_optgroup[] = array('value'=>$field, 'label'=>getTranslatedString($fieldlabel, $module));
			}
			$modules_list[] = array(
				'label' => getTranslatedString($module, $module).' '.getTranslatedString($value, $module),
				'options' => $modules_optgroup,
				'class' => 'select'
			);
		}
	}
	return $modules_list;
}

/** Function to formulate the vtiger_fields for the secondary modules
 *  This function accepts the module name
 *  as arguments and generates the vtiger_fields for the secondary module as
 *  a HTML Combo values
 */
function getSecondaryColumnsHTML($module) {
	$module_columslist = array();
	if ($module != '') {
		$ogReport = new Reports();
		$ogReport->getSecModuleColumnsList($module);

		$secmodule = explode(':', $module);
		for ($i=0; $i<count($secmodule); $i++) {
			if (vtlib_isModuleActive($secmodule[$i])) {
				$i18nModule = getTranslatedString($secmodule[$i], $secmodule[$i]);
				$block_listed = array();
				foreach ($ogReport->module_list[$secmodule[$i]] as $value) {
					if (isset($ogReport->sec_module_columnslist[$secmodule[$i]][$value]) && !isset($block_listed[$value])) {
						$block_listed[$value] = true;
						$optgroup = array();
						foreach ($ogReport->sec_module_columnslist[$secmodule[$i]][$value] as $field => $fieldlabel) {
							$optgroup[] = array('value'=>$field, 'label'=>getTranslatedString($fieldlabel, $secmodule[$i]));
						}
						$module_columslist[] = array(
							'label'   => $i18nModule.' '.getTranslatedString($value, $secmodule[$i]),
							'options' => $optgroup,
							'class'   => 'select'
						);
					}
				}
			}
		}
	}
	return $module_columslist;
}

/** Function to get the combo values for the Primary module Columns
 *  @param $module(module name) :: Type String
 *  @param $selected (<selected or ''>) :: Type String
 *  This function generates the combo values for the columns  for the given module
 *  and return a HTML string
 */
function getPrimaryColumns_GroupingHTML($module, $selected = "") {
	global $oReport, $current_language;
	$id_added=false;
	$mod_strings = return_module_language($current_language, $module);

	$block_listed = array();
	$selected = decode_html($selected);

	$oReport->getPriModuleColumnsList($module);
	$list = array();
	foreach ($oReport->module_list[$module] as $value) {
		if (isset($oReport->pri_module_columnslist[$module][$value]) && !isset($block_listed[$value])) {
			$block_listed[$value] = true;

			$optgroup = array(
				"label" => getTranslatedString($module, $module)." ".getTranslatedString($value, $module),
				"class" => "select",
				"style" => "border:none"
			);

			if ($id_added==false) {
				$option = array(
					"value" => "vtiger_crmentity:crmid:".$module."_ID:crmid:I",
					"label" => getTranslatedString($module, $module).' '.getTranslatedString('ID', $module)
				);

				if ($selected == "vtiger_crmentity:crmid:".$module."_ID:crmid:I") {
					$option["selected"] = true;
				}
				$optgroup["options"][] = $option;

				$id_added=true;
			}
			foreach ($oReport->pri_module_columnslist[$module][$value] as $field => $fieldlabel) {
				$option = array("value" => $field);

				if (isset($mod_strings[$fieldlabel])) {
					$option["label"] = $mod_strings[$fieldlabel];
				} else {
					$option["label"] = $fieldlabel;
				}

				if ($selected == decode_html($field)) {
					$option["selected"] = true;
				}

				$optgroup["options"][] = $option;
			}
			$list[] = $optgroup;
		}
	}
	return $list;
}

/**
 * Function to get the combo values for the Secondary module Columns
 *  @param $module(module name) :: Type String
 *  @param $selected (<selected or ''>) :: Type String
 *  This function generates the combo values for the columns for the given module
 *  and return a HTML string
 */
function getSecondaryColumns_GroupingHTML($module, $selected = "") {
	global $oReport, $current_language;
	$oReport->getPriModuleColumnsList($module);

	$selected = decode_html($selected);
	$list = array();
	if ($module != "") {
		$secmodule = explode(":", $module);
		foreach ($secmodule as $mod) {
			if (vtlib_isModuleActive($mod)) {
				$i18nModule = getTranslatedString($mod, $mod);
				$mod_strings = return_module_language($current_language, $mod);
				$block_listed = array();
				foreach ($oReport->module_list[$mod] as $value) {
					if (isset($oReport->sec_module_columnslist[$mod][$value]) && empty($block_listed[$value])) {
						$block_listed[$value] = true;

						$optgroup = array(
							"label" => $i18nModule." ".getTranslatedString($value, $mod),
							"class" => "select",
							"style" => "border:none"
						);

						foreach ($oReport->sec_module_columnslist[$mod][$value] as $field => $fieldlabel) {
							$option = array("value" => $field);

							if (isset($mod_strings[$fieldlabel])) {
								$option["label"] = $mod_strings[$fieldlabel];
							} else {
								$option["label"] = $fieldlabel;
							}

							if ($selected == decode_html($field)) {
								$option["selected"] = true;
							}

							$optgroup["options"][] = $option;
						}
						$list[] = $optgroup;
					}
				}
			}
		}
	}
	return $list;
}

/**
 * @param  int $sortid
 * @param  string $reportid
 * @return array
 */
function getGroupByTimeDiv($sortid, $reportid = '') {
	require_once 'include/utils/CommonUtils.php';
	global $adb, $mod_strings;
	$query = 'select * from vtiger_reportgroupbycolumn where reportid=? and sortid=?';
	$result = $adb->pquery($query, array($reportid,$sortid));
	$rows = $adb->num_rows($result);

	$options = array(
		array("value" => "None","label" => $mod_strings['LBL_NONE']),
		array("value" => "Year","label" => $mod_strings['LBL_YEAR']),
		array("value" => "Month","label" => $mod_strings['LBL_MONTH']),
		array("value" => "Quarter","label" => $mod_strings['LBL_QUARTER']),
		array("value" => "Day","label" => $mod_strings['LBL_DAY'])
	);

	if ($rows > 0) {
		$displaystyle = 'inline';
		$selected_groupby = $adb->query_result($result, 0, 'dategroupbycriteria');

		foreach ($options as $key => $option) {
			if (strtolower($option["value"]) == strtolower($selected_groupby)) {
				$options[$key]["selected"] = true;
			}
		}
	} else {
		$displaystyle = 'none';
		$options[0]['selected'] = true;
	}

	return array('display'=>$displaystyle, 'options' => $options);
}

/**
 * [getOrderGrouping description]
 * @param  {String} $sortorder Order type
 * @return {Array} returns an array of options
 */
function getOrderGrouping($sortorder = "") {
	global $app_strings;

	$ascending = array("value"=>"Ascending","label"=>$app_strings['Ascending']);
	$descending = array("value"=>"Descending","label"=>$app_strings['Descending']);
	$selected = array("selected"=>true);

	$ascending_order = array(
		array_merge($ascending, $selected),
		$descending
	);

	$descending_order = array(
		$ascending,
		array_merge($descending, $selected)
	);

	if ($sortorder != "Descending") {
		$ASCDESC = $ascending_order;
	} else {
		$ASCDESC = $descending_order;
	}

	return $ASCDESC;
}

/**
 * Function to get the HTML strings for the primarymodule standard filters
 * @param $module : Type String
 * @param $selected : Type String(optional)
 *  This Returns a HTML combo srings
 */
function getPrimaryStdFilterHTML($module, $selected = "") {
	$ogReport = new Reports();
	$ogReport->oCustomView=new CustomView();
	$result = $ogReport->oCustomView->getStdCriteriaByModule($module);
	$filters = array();
	if (isset($result)) {
		foreach ($result as $key => $value) {
			if ($key == $selected) {
				$filters[] = array("selected"=>true,"value"=>$key,"label"=>getTranslatedString($module, $module)." - ".getTranslatedString($value, $module));
			} else {
				$filters[] = array("value"=>$key,"label"=>getTranslatedString($module, $module)." - ".getTranslatedString($value, $module));
			}
		}
	}
	return $filters;
}

/**
 * Function to get the HTML strings for the secondary standard filters
 * @param $module : Type String
 * @param $selected : Type String(optional)
 *  This Returns a HTML combo srings for the secondary modules
 */
function getSecondaryStdFilterHTML($module, $selected = "") {
	global $current_language;
	$ogReport = new Reports();
	$ogReport->oCustomView=new CustomView();
	$filters = array();
	if ($module != "") {
		$secmodule = explode(":", $module);
		foreach ($secmodule as $mod) {
			$result = $ogReport->oCustomView->getStdCriteriaByModule($mod);
			if (isset($result)) {
				$mod_strings = return_module_language($current_language, $mod);
				$i18nModule = getTranslatedString($mod, $mod);
				foreach ($result as $key => $value) {
					if (isset($mod_strings[$value])) {
						if ($key == $selected) {
							$filters[] = array("selected"=>true,"value"=>$key, "label"=>$i18nModule." - ".getTranslatedString($value, $mod));
						} else {
							$filters[] = array("value"=>$key, "label"=>$i18nModule." - ".getTranslatedString($value, $mod));
						}
					} else {
						if ($key == $selected) {
							$filters[] = array("selected"=>true, "value"=>$key, "label"=>$i18nModule." - ".$value);
						} else {
							$filters[] = array("value"=>$key, "label"=>$i18nModule." - ".$value);
						}
					}
				}
			}
		}
	}
	return $filters;
}

/**
 *  Function to get primary columns for an advanced filter
 *  This function accepts The module as an argument
 *  This generate columns of the primary modules for the advanced filter
 *  It returns a HTML string of combo values
 */
function getPrimaryColumns_AdvFilterHTML($module, $selected = "") {
	$selected = decode_html($selected);
	$block_listed = array();
	$ogReport = new Reports();
	$filters = array();
	$ogReport->getPriModuleColumnsList($module);
	foreach ($ogReport->module_list[$module] as $value) {
		if (isset($ogReport->pri_module_columnslist[$module][$value]) && empty($block_listed[$value])) {
			$block_listed[$value] = true;
			$optgroup = array(
				"label"=>getTranslatedString($module, $module)." ".getTranslatedString($value, $module),
				"class"=>"select",
				"style"=>"border:none");
			foreach ($ogReport->pri_module_columnslist[$module][$value] as $field => $fieldlabel) {
				$field = decode_html($field);
				$fldlbl = str_replace(array("\n","\r"), '', getTranslatedString($fieldlabel, $module));
				$option = array("value"=>$field,"label"=>$fldlbl);
				if ($selected == $field) {
					$option["selected"] = true;
				}
				$optgroup['options'][] = $option;
			}
			$filters[] = $optgroup;
		}
	}
	return $filters;
}

/** Function to get Secondary columns for an advanced filter
 *  This function accepts The module as an argument
 *  This generate columns of the secondary module for the advanced filter
 *  It returns a HTML string of combo values
 */
function getSecondaryColumns_AdvFilterHTML($module, $selected = "") {
	$ogReport = new Reports();
	$filters = array();
	if ($module != '') {
		$ogReport->getSecModuleColumnsList($module);
		$secmodule = explode(":", $module);
		for ($i=0; $i < count($secmodule); $i++) {
			if (vtlib_isModuleActive($secmodule[$i])) {
				$block_listed = array();
				$i18nModule = getTranslatedString($secmodule[$i], $secmodule[$i]);
				foreach ($ogReport->module_list[$secmodule[$i]] as $value) {
					if (isset($ogReport->sec_module_columnslist[$secmodule[$i]][$value]) && empty($block_listed[$value])) {
						$block_listed[$value] = true;
						$optgroup = array(
							"label"=>$i18nModule." ".getTranslatedString($value, $secmodule[$i]),
							"class"=>"select",
							"style"=>"border:none"
						);
						foreach ($ogReport->sec_module_columnslist[$secmodule[$i]][$value] as $field => $fieldlabel) {
							$fldlbl = str_replace(array("\n","\r"), '', getTranslatedString($fieldlabel, $secmodule[$i]));
							$field = decode_html($field);

							$option = array("value"=>$field,"label"=>$fldlbl);
							if ($selected == $field) {
								$option["selected"] = true;
							}
							$optgroup['options'][] = $option;
						}
						$filters[] = $optgroup;
					}
				}
			}
		}
	}
	return $filters;
}

/**
 * [getRelatedColumns description]
 * @param  string $selected [description]
 * @return [type]           [description]
 */
function getRelatedColumns($selected = "") {
	$ogReport = new Reports();
	$rel_colums = array();
	$rel_fields = $ogReport->adv_rel_fields;
	if ($selected!='All') {
		$selected = explode(":", $selected);
	}
	$related_fields = array();
	foreach ($rel_fields as $i => $index) {
		foreach ($index as $value) {
			$fieldarray = explode("::", $value);
			$rel_colums = array("value"=>$fieldarray[0], "label"=>$fieldarray[1]);
		}
		$related_fields[$i] = $rel_colums;
	}
	if (!empty($selected) && $selected[4]!='') {
		return $related_fields[$selected[4]];
	} elseif ($selected=='All') {
		return $related_fields;
	} else {
		return ;
	}
}

/**
 * [getRelatedColumns description]
 * @param  string $selected [description]
 * @return [type]           [description]
 */
function getRelatedFieldColumns() {
	global $oReport;
	$ogReport = new Reports();
	$ogReport->getPriModuleColumnsList($oReport->primodule);
	$ogReport->getSecModuleColumnsList($oReport->secmodule);
	return $ogReport->adv_rel_fields;
}
?>
