<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/CustomFieldUtil.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/utils.php';
require_once 'modules/PickList/PickListUtils.php';

global $mod_strings,$app_strings,$log,$theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$smarty=new vtigerCRM_Smarty;

$subMode = isset($_REQUEST['sub_mode']) ? vtlib_purify($_REQUEST['sub_mode']) : '';
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('JS_DATEFORMAT', parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$duplicate = 'no';
if ($subMode == 'updateFieldProperties') {
	updateFieldProperties();
} elseif ($subMode == 'deleteCustomField') {
	deleteCustomField();
} elseif ($subMode == 'changeOrder') {
	changeFieldOrder();
} elseif ($subMode == 'addBlock') {
	$duplicate = addblock();
} elseif ($subMode == 'deleteCustomBlock') {
	deleteBlock();
} elseif ($subMode == 'addCustomField') {
	$duplicate = addCustomField();
} elseif ($subMode == 'movehiddenfields' || $subMode == 'showhiddenfields') {
	show_move_hiddenfields($subMode);
} elseif ($subMode == 'changeRelatedInfoOrder') {
	changeRelatedListOrder();
} elseif ($subMode == 'deleteRelatedList') {
	deleteRelatedList();
} elseif ($subMode == 'createRelatedList') {
	createRelatedList();
}

$module_array=getCustomFieldSupportedModules();

$cfimagecombo = array(
	$image_path.'text.gif',
	$image_path.'number.gif',
	$image_path.'percent.gif',
	$image_path.'currency.gif',
	$image_path.'date.gif',
	$image_path.'email.gif',
	$image_path.'phone.gif',
	$image_path.'picklist.gif',
	$image_path.'url.gif',
	$image_path.'checkbox.gif',
	$image_path.'text.gif',
	$image_path.'picklist.gif',
	$image_path.'time.PNG'
);

$cftextcombo = array(
	$mod_strings['Text'],
	$mod_strings['Number'],
	$mod_strings['Percent'],
	$mod_strings['Currency'],
	$mod_strings['Date'],
	$mod_strings['Email'],
	$mod_strings['Phone'],
	$mod_strings['PickList'],
	$mod_strings['LBL_URL'],
	$mod_strings['LBL_CHECK_BOX'],
	$mod_strings['LBL_TEXT_AREA'],
	$mod_strings['LBL_MULTISELECT_COMBO'],
	$mod_strings['Time']
);

$smarty->assign('MODULES', $module_array);
$smarty->assign('CFTEXTCOMBO', $cftextcombo);
$smarty->assign('CFIMAGECOMBO', $cfimagecombo);

if (!empty($_REQUEST['formodule'])) {
	$fld_module = vtlib_purify($_REQUEST['formodule']);
} elseif ($_REQUEST['fld_module'] != '') {
	$fld_module = vtlib_purify($_REQUEST['fld_module']);
} else {
	$fld_module = 'Accounts';
}

$block_array = getModuleBlocks($fld_module);
$cfentries = getFieldListEntries($fld_module);
$cfentries = insertDetailViewBlockWidgets($cfentries, $fld_module);
$smarty->assign('BLOCKS', $block_array);
$smarty->assign('MODULE', $fld_module);
$smarty->assign('CFENTRIES', $cfentries);
$rellistinfo = getRelatedListInfo($fld_module);
$smarty->assign('RELATEDLIST', $rellistinfo);
$pickListResult=getAllowedPicklistModules();
$nonRelatableModules = array('PBXManager','SMSNotifier','cbupdater','GlobalVariable','Emails','ModComments');
$smsRelatableModules = array('Accounts','Contacts','Leads');
$entityrelmods=array();
foreach ($pickListResult as $pValue) {
	if (!in_array($pValue, $nonRelatableModules) || (in_array($fld_module, $smsRelatableModules) && $pValue=='SMSNotifier')) {
		$entityrelmods[$pValue] = getTranslatedString($pValue, $pValue);
	}
}
uasort($entityrelmods, function ($a, $b) {
	return (strtolower($a[0]) < strtolower($b[0])) ? -1 : 1;
});
$smarty->assign('entityrelmods', $entityrelmods);
$relmods = array();
foreach ($rellistinfo as $relmod) {
	if (empty($relmod['name'])) {
		$relmods[$relmod['id']]=$relmod['label'];
	} else {
		$relmods[$relmod['name']]=$relmod['label'];
	}
}
$notRelatedModules = array_diff_key($entityrelmods, $relmods);
$smarty->assign('NotRelatedModules', $notRelatedModules);

$blockrelmods = array();
$fld_tabid = getTabid($fld_module);
$brmrs = $adb->pquery(
	'SELECT vtiger_tab.name
	FROM vtiger_blocks
	INNER JOIN vtiger_relatedlists ON vtiger_blocks.isrelatedlist=vtiger_relatedlists.relation_id
	INNER JOIN vtiger_tab ON vtiger_relatedlists.related_tabid = vtiger_tab.tabid
	WHERE vtiger_relatedlists.tabid = ?',
	array($fld_tabid)
);
while ($rl = $adb->fetch_array($brmrs)) {
	$blockrelmods[$rl['name']] = 1;
}
$notBlockRelatedModules = array_diff_key($relmods, $blockrelmods);
$smarty->assign('NotBlockRelatedModules', $notBlockRelatedModules);

$curmodsinrel_result = $adb->pquery('SELECT fieldid,relmodule FROM vtiger_fieldmodulerel WHERE module=?', array($fld_module));
$curmodsinrel = array();
while ($row = $adb->fetch_array($curmodsinrel_result)) {
	$curmodsinrel[$row['fieldid']][] = $row['relmodule'];
}
$smarty->assign('curmodsinrel', $curmodsinrel);

if ((isset($_REQUEST['duplicate']) && $_REQUEST['duplicate'] == 'yes') || $duplicate == 'yes') {
	echo 'ERROR';
	exit;
}
if ($duplicate == 'LENGTH_ERROR') {
	echo 'LENGTH_ERROR';
	exit;
}
$mode = isset($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '';
$smarty->assign('MODE', $mode);

if (!isset($_REQUEST['ajax']) || $_REQUEST['ajax'] != 'true') {
	$smarty->display('Settings/LayoutBlockList.tpl');
} elseif ($_REQUEST['ajax'] == 'true'
	&& ($subMode == 'getRelatedInfoOrder' || $subMode == 'changeRelatedInfoOrder' || $subMode == 'createRelatedList' || $subMode == 'deleteRelatedList')
) {
	$smarty->display('Settings/OrderRelatedList.tpl');
} else {
	$smarty->display('Settings/LayoutBlockEntries.tpl');
}

function InStrCount($String, $Find, $CaseSensitive = false) {
	global $log;
	$i=0;
	$x=0;
	$substring = '';
	while (strlen($String)>=$i) {
		unset($substring);
		if ($CaseSensitive) {
			$Find=strtolower($Find);
			$String=strtolower($String);
		}
		$substring=substr($String, $i, strlen($Find));
		if ($substring==$Find) {
			$x++;
		}
		$i++;
	}
	return $x;
}

/**
 * Function to get customfield entries
 * @param string $module - Module name
 * return array  $cflist - customfield entries
 */
function getFieldListEntries($module) {
	$tabid = getTabid($module);
	global $adb, $smarty, $current_user, $dbconfig;

	$dbQuery = 'select vtiger_blocks.*,vtiger_tab.presence as tabpresence
		from vtiger_blocks
		inner join vtiger_tab on vtiger_tab.tabid = vtiger_blocks.tabid
		where vtiger_blocks.tabid=?  and vtiger_tab.presence = 0 order by sequence';
	$result = $adb->pquery($dbQuery, array($tabid));
	$row = $adb->fetch_array($result);

	$focus = CRMEntity::getInstance($module);

	$nonEditableUiTypes = array('4','70');

	// To get reference field names
	require_once 'include/Webservices/Utils.php';
	$handler = vtws_getModuleHandlerFromName($module, $current_user);

	$meta = $handler->getMeta();
	$referenceFieldNames = array_keys($meta->getReferenceFieldDetails());

	$cflist=array();
	$i=0;
	if ($row!='') {
		do {
			if ($row['blocklabel'] == 'LBL_CUSTOM_INFORMATION') {
				$smarty->assign('CUSTOMSECTIONID', $row['blockid']);
			}
			if ($row['blocklabel'] == 'LBL_RELATED_PRODUCTS') {
				$smarty->assign('RELPRODUCTSECTIONID', $row['blockid']);
			} else {
				$smarty->assign('RELPRODUCTSECTIONID', '');
			}
			if ($row['blocklabel'] == 'LBL_COMMENTS' || $row['blocklabel'] == 'LBL_COMMENT_INFORMATION') {
				$smarty->assign('COMMENTSECTIONID', $row['blockid']);
			} else {
				$smarty->assign('COMMENTSECTIONID', 0);
			}
			if ($row['blocklabel'] == 'LBL_TICKET_RESOLUTION') {
				$smarty->assign('SOLUTIONBLOCKID', $row['blockid']);
			} else {
				$smarty->assign('SOLUTIONBLOCKID', 0);
			}
			if ($row['blocklabel'] == '') {
				$row['blocklabel'] = '{{'.getTranslatedString('Not Labeled').'}}';
			}
			$cflist[$i]['tabpresence']= $row['tabpresence'];
			$cflist[$i]['module'] = $module;
			$i18nMod = $module;
			if ($row['isrelatedlist']>0) {
				$brmrs = $adb->query('SELECT vtiger_tab.name
					FROM vtiger_blocks
					INNER JOIN vtiger_relatedlists ON vtiger_blocks.isrelatedlist=vtiger_relatedlists.relation_id
					INNER JOIN vtiger_tab ON vtiger_relatedlists.related_tabid = vtiger_tab.tabid
					LIMIT 1');
				if ($brmrs && $adb->num_rows($brmrs)>0) {
					$i18nMod = $adb->query_result($brmrs, 0, 0);
				}
			}
			$cflist[$i]['blocklabel']=getTranslatedString($row['blocklabel'], $i18nMod);
			$cflist[$i]['blockid']=$row['blockid'];
			$cflist[$i]['display_status']=$row['display_status'];
			$cflist[$i]['tabid']=$tabid;
			$cflist[$i]['blockselect']=$row['blockid'];
			$cflist[$i]['sequence'] = $row['sequence'];
			$cflist[$i]['iscustom'] = $row['iscustom'];
			$cflist[$i]['isrelatedlist'] = $row['isrelatedlist'];

			if ($module!='Invoices' && $module!='Quotes' && $module!='SalesOrder' && $module!='Invoice') {
				$sql_field='select * from vtiger_field where block=? and vtiger_field.displaytype IN (1,2,4) order by sequence';
				$sql_field_params = array($row['blockid']);
			} else {
				$sql_field="select *
					from  vtiger_field
					where block=? and (vtiger_field.fieldlabel!='Total' and vtiger_field.fieldlabel!='Sub Total' and vtiger_field.fieldlabel!='Tax') and
						vtiger_field.displaytype IN (1,2,4) order by sequence";
				$sql_field_params = array($row['blockid']);
			}

			$result_field = $adb->pquery($sql_field, $sql_field_params);
			$row_field= $adb->fetch_array($result_field);
			$cf_element = array();
			$cf_hidden_element = array();
			if ($row_field!='') {
				$count=0;
				$hiddencount=0;
				do {
					$fieldid = $row_field['fieldid'];
					$presence = $row_field['presence'];
					$fieldname = $row_field['fieldname'];
					$customfieldflag=InStrCount($row_field['fieldname'], 'cf_', true);
					$quickcreate = $row_field['quickcreate'];
					$massedit = $row_field['masseditable'];
					$typeofdata = $row_field['typeofdata'];
					$displaytype = $row_field['displaytype'];
					$uitype = $row_field['uitype'];
					$fld_type_name = getCustomFieldTypeName($row_field['uitype']);
					$defaultValue = $row_field['defaultvalue'];
					if (!empty($defaultValue) && ($uitype == '5' || $uitype == '6' || $uitype == '23')) {
						$defaultValue = getValidDisplayDate($defaultValue);
					}
					$fieldinfors = $adb->pquery(
						'select character_maximum_length from information_schema.columns where table_name=? and column_name=? and table_schema=?',
						array($row_field['tablename'], $row_field['columnname'], $dbconfig['db_name'])
					);
					if ($fieldinfors && $adb->num_rows($fieldinfors)>0) {
						$fieldsize = $adb->query_result($fieldinfors, 0, 'character_maximum_length');
					} else {
						$fieldsize = '';
					}
					$fieldlabel = getTranslatedString($row_field['fieldlabel'], $module);
					$defaultPermitted = true;
					$strictlyMandatory = false;
					if ((isset($focus->mandatory_fields) && !empty($focus->mandatory_fields) && in_array($fieldname, $focus->mandatory_fields))
						|| (in_array($uitype, $nonEditableUiTypes) || $displaytype == 2)
					) {
						$strictlyMandatory = true;
						$defaultPermitted = false;
					}
					if (in_array($fieldname, $referenceFieldNames)) {
						$defaultPermitted = false;
					}
					$visibility = getFieldInfo($fieldname, $typeofdata, $quickcreate, $massedit, $presence, $strictlyMandatory, $customfieldflag, $displaytype, $uitype);

					$allValues = array();
					if (in_array($uitype, array('15','16','33'))) {
						$allValues = getAllPickListValues($fieldname);
					}
					if ($uitype == '26') {
						$defaultPermitted = true;
						$res=$adb->pquery('select foldername,folderid from vtiger_attachmentsfolder order by foldername', array());
						for ($f=0; $f<$adb->num_rows($res); $f++) {
							$fid=$adb->query_result($res, $f, 'folderid');
							$allValues[$fid]=$adb->query_result($res, $f, 'foldername');
						}
					}

					if ($presence == 0 || $presence == 2) {
						$cf_element[$count]['fieldselect']=$fieldid;
						$cf_element[$count]['blockid']=$row['blockid'];
						$cf_element[$count]['tabid']=$tabid;
						$cf_element[$count]['no']=$count;
						$cf_element[$count]['label']=$fieldlabel;
						$cf_element[$count]['fieldlabel'] = $row_field['fieldlabel'];
						$cf_element[$count]['type']=$fld_type_name;
						$cf_element[$count]['typeofdata']=$typeofdata;
						$cf_element[$count]['uitype']=$uitype;
						$cf_element[$count]['columnname']=$row_field['columnname'];
						$cf_element[$count]['fieldsize']=$fieldsize;
						$cf_element[$count]['defaultvalue']= array('permitted' => $defaultPermitted, 'value' => $defaultValue, '_allvalues' => $allValues);
						$cf_element[$count]['colspec']= CustomView::getFilterFieldDefinition($fieldid, $module);
						$cf_element[$count] = array_merge($cf_element[$count], $visibility);

						$count++;
					} else {
						$cf_hidden_element[$hiddencount]['fieldselect']=$fieldid;
						$cf_hidden_element[$hiddencount]['blockid']=$row['blockid'];
						$cf_hidden_element[$hiddencount]['tabid']=$tabid;
						$cf_hidden_element[$hiddencount]['no']=$hiddencount;
						$cf_hidden_element[$hiddencount]['label']=$fieldlabel;
						$cf_hidden_element[$hiddencount]['fieldlabel'] = $row_field['fieldlabel'];
						$cf_hidden_element[$hiddencount]['type']=$fld_type_name;
						$cf_hidden_element[$hiddencount]['typeofdata']=$typeofdata;
						$cf_hidden_element[$hiddencount]['uitype']=$uitype;
						$cf_hidden_element[$hiddencount]['columnname']=$row_field['columnname'];
						$cf_hidden_element[$hiddencount]['fieldsize']=$fieldsize;
						$cf_hidden_element[$hiddencount]['defaultvalue']= array('permitted' => $defaultPermitted, 'value' => $defaultValue, '_allvalues' => $allValues);
						$cf_hidden_element[$hiddencount]['colspec']=CustomView::getFilterFieldDefinition($fieldid, $module);
						$cf_hidden_element[$hiddencount] = array_merge($cf_hidden_element[$hiddencount], $visibility);

						$hiddencount++;
					}
				} while ($row_field = $adb->fetch_array($result_field));

				$cflist[$i]['no']=$count;
				$cflist[$i]['hidden_count'] = $hiddencount;
			} else {
				$cflist[$i]['no']= 0;
			}

			$query_fields_not_in_block ='select fieldid,fieldlabel,block from vtiger_field ' .
				'inner join vtiger_blocks on vtiger_field.block=vtiger_blocks.blockid ' .
				'where vtiger_field.block != ? and vtiger_blocks.blocklabel not in ("LBL_TICKET_RESOLUTION","LBL_COMMENTS","LBL_COMMENT_INFORMATION") ' .
				'AND vtiger_field.tabid = ? and vtiger_field.displaytype IN (1,2,4) order by vtiger_field.sequence';

			$params =array($row['blockid'],$tabid);
			$fields = $adb->pquery($query_fields_not_in_block, $params);
			$row_field= $adb->fetch_array($fields);

			$movefields = array();
			$cflist[$i]['movefieldcount'] = 0;
			if ($row_field != '') {
				$movefieldcount = 0;
				do {
					$movefields[$movefieldcount]['fieldid'] =  $row_field['fieldid'];
					$movefields[$movefieldcount]['fieldlabel'] =  getTranslatedString($row_field['fieldlabel'], $module);
					$movefieldcount++;
				} while ($row_field = $adb->fetch_array($fields));
				$cflist[$i]['movefieldcount'] = $movefieldcount;
			}

			$cflist[$i]['field']= $cf_element;
			$cflist[$i]['hiddenfield']= $cf_hidden_element;
			$cflist[$i]['movefield'] = $movefields;

			$cflist[$i]['hascustomtable'] = $focus->customFieldTable;
			unset($cf_element, $cf_hidden_element, $movefields);
			$i++;
		} while ($row = $adb->fetch_array($result));
	}
	return $cflist;
}

/* inserts Detail View Widget Blocks into the given array */
function insertDetailViewBlockWidgets($cfentries, $fld_module) {
	$tabid = getTabid($fld_module);
	$dvb = Vtiger_Link::getAllByType($tabid, array('DETAILVIEWWIDGET'));
	if (count($dvb['DETAILVIEWWIDGET'])>0) {
		$dvb = $dvb['DETAILVIEWWIDGET'];
		$retarr = array();
		$totalcnt = count($cfentries);
		$idx = 0;
		for ($cnt = 1; $cnt <= $totalcnt; $cnt++) {
			$retarr[$idx++] = $cfentries[$cnt-1];
			foreach ($dvb as $CUSTOM_LINK_DETAILVIEWWIDGET) {
				if (preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl, $matches)
					&& (($cnt==1 && $CUSTOM_LINK_DETAILVIEWWIDGET->sequence <= 1)
						|| ($CUSTOM_LINK_DETAILVIEWWIDGET->sequence == $cnt)
						|| ($cnt==$totalcnt && $CUSTOM_LINK_DETAILVIEWWIDGET->sequence >= $cnt))
				) {
					list($void, $widgetControllerClass, $widgetControllerClassFile) = explode(':', $matches[0]);
					$widgetControllerClass = substr($widgetControllerClass, 2);
					if (!class_exists($widgetControllerClass)) {
						checkFileAccessForInclusion($widgetControllerClassFile);
						include_once $widgetControllerClassFile;
					}
					$lbl = '';
					if (class_exists($widgetControllerClass)) {
						$widgetControllerInstance = new $widgetControllerClass;
						if (property_exists($widgetControllerClass, 'isSortable')) {
							$isSortable = $widgetControllerInstance->isSortable;
						} else {
							$isSortable = true;
						}
						if ($isSortable) {
							$widgetInstance = $widgetControllerInstance->getWidget($CUSTOM_LINK_DETAILVIEWWIDGET->linklabel);
							if ($widgetInstance) {
								$lbl = $widgetInstance->title();
							} else {
								$lbl = 'DetailViewBlock_'.$CUSTOM_LINK_DETAILVIEWWIDGET->linkid;
							}
						}
					}
					$retarr[$idx++] = array(
						'DVB'=>$CUSTOM_LINK_DETAILVIEWWIDGET->linkid,
						'label'=>$lbl,
						'tabid'=>$tabid
					);
				}
			}
		}
	} else {
		$retarr = $cfentries;
	}
	return $retarr;
}

/* function to get the modules supports Custom Fields
*/
function getCustomFieldSupportedModules() {
	global $adb;
	$sql = 'select distinct vtiger_field.tabid,name
		from vtiger_field
		inner join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid
		where vtiger_field.tabid not in(9,10,16,15,8,29)';
	$result = $adb->query($sql);
	$modulelist = array();
	while ($moduleinfo=$adb->fetch_array($result)) {
		$modulelist[$moduleinfo['name']] = $moduleinfo['name'];
	}
	return $modulelist;
}

function getModuleBlocks($module) {
	global $adb;
	$tabid = getTabid($module);
	$blockquery = 'select blocklabel,blockid from vtiger_blocks where tabid = ?';
	$blockres = $adb->pquery($blockquery, array($tabid));
	while ($blockinfo = $adb->fetch_array($blockres)) {
		$blocklist[$blockinfo['blockid']] = getTranslatedString($blockinfo['blocklabel'], $module);
	}
	return $blocklist;
}

function changeFieldOrder() {
	global $adb, $smarty;
	if (!empty($_REQUEST['what_to_do'])) {
		if ($_REQUEST['what_to_do']=='block_down') {
			$blockid = vtlib_purify($_REQUEST['blockid']);
			if (substr($blockid, 0, 3)=='dvb') { // detail view block
				$adb->pquery('update vtiger_businessactions set sequence=sequence+1 where businessactionsid=?', array(substr($blockid, 3)));
			} else {  // normal block
				$sql='select sequence from vtiger_blocks where blockid=?';
				$result = $adb->pquery($sql, array($blockid));
				$row= $adb->fetch_array($result);
				$current_sequence=$row['sequence'];

				$sql_next='select * from vtiger_blocks where sequence > ? and tabid=? limit 0,1';
				$result_next = $adb->pquery($sql_next, array($current_sequence,  vtlib_purify($_REQUEST['tabid'])));
				$row_next= $adb->fetch_array($result_next);
				$next_sequence=$row_next['sequence'];
				$next_id=$row_next['blockid'];

				$adb->pquery('update vtiger_blocks set sequence=? where blockid=?', array($next_sequence, $blockid));
				$adb->pquery('update vtiger_blocks set sequence=? where blockid=?', array($current_sequence,$next_id));
			}
		}

		if ($_REQUEST['what_to_do']=='block_up') {
			$blockid = vtlib_purify($_REQUEST['blockid']);
			if (substr($blockid, 0, 3)=='dvb') { // detail view block
				$adb->pquery('update vtiger_businessactions set sequence=if (sequence-1<0,0,sequence-1) where businessactionsid=?', array(substr($blockid, 3)));
			} else {  // normal block
				$result = $adb->pquery('select * from vtiger_blocks where blockid=?', array($blockid));
				$row= $adb->fetch_array($result);
				$current_sequence=$row['sequence'];

				$sql_previous='select * from vtiger_blocks where sequence < ? and tabid=? order by sequence desc limit 0,1';
				$result_previous = $adb->pquery($sql_previous, array($current_sequence, vtlib_purify($_REQUEST['tabid'])));
				$row_previous= $adb->fetch_array($result_previous);
				$previous_sequence=$row_previous['sequence'];
				$previous_id=$row_previous['blockid'];

				$adb->pquery('update vtiger_blocks set sequence=? where blockid=?', array($previous_sequence,$blockid));
				$adb->pquery('update vtiger_blocks set sequence=? where blockid=?', array($current_sequence,$previous_id));
			}
		}

		if ($_REQUEST['what_to_do']=='down' || $_REQUEST['what_to_do']=='Right') {
			$result = $adb->pquery('select * from vtiger_field where fieldid=? and vtiger_field.presence in (0,2)', array(vtlib_purify($_REQUEST['fieldid'])));
			$row= $adb->fetch_array($result);
			$current_sequence=$row['sequence'];
			if ($_REQUEST['what_to_do']=='down') {
				$sql_next='select * from vtiger_field where sequence > ? and block = ? and vtiger_field.presence in (0,2) order by sequence limit 1,1';
			} else {
				$sql_next='select * from vtiger_field where sequence > ? and block = ? and vtiger_field.presence in (0,2) order by sequence limit 0,1';
			}
			$sql_next_params = array($current_sequence, vtlib_purify($_REQUEST['blockid']));

			$result_next = $adb->pquery($sql_next, $sql_next_params);
			$row_next= $adb->fetch_array($result_next);
			$next_sequence=$row_next['sequence'];
			$next_id=$row_next['fieldid'];

			$adb->pquery('update vtiger_field  set sequence=? where fieldid=?', array($next_sequence,  vtlib_purify($_REQUEST['fieldid'])));
			$adb->pquery('update vtiger_field  set sequence=? where fieldid=?', array($current_sequence,$next_id));
			$smarty->assign('COLORID', vtlib_purify($_REQUEST['fieldid']));
		}

		if ($_REQUEST['what_to_do']=='up' || $_REQUEST['what_to_do']=='Left') {
			$result = $adb->pquery('select * from vtiger_field where fieldid=? and vtiger_field.presence in (0,2)', array(vtlib_purify($_REQUEST['fieldid'])));
			$row= $adb->fetch_array($result);
			$current_sequence=$row['sequence'];

			if ($_REQUEST['what_to_do']=='up') {
				$sql_previous='select * from vtiger_field where sequence < ? and block=? and vtiger_field.presence in (0,2) order by sequence desc limit 1,1';
			} else {
				$sql_previous='select * from vtiger_field where sequence < ? and block=? and vtiger_field.presence in (0,2) order by sequence desc limit 0,1';
			}
			$sql_prev_params = array($current_sequence,  vtlib_purify($_REQUEST['blockid']));

			$result_previous = $adb->pquery($sql_previous, $sql_prev_params);
			$row_previous= $adb->fetch_array($result_previous);
			$previous_sequence=$row_previous['sequence'];
			$previous_id=$row_previous['fieldid'];

			$adb->pquery('update vtiger_field set sequence=? where fieldid=?', array($previous_sequence,  vtlib_purify($_REQUEST['fieldid'])));
			$adb->pquery('update vtiger_field set sequence=? where fieldid=?', array($current_sequence,$previous_id));
			$smarty->assign('COLORID', vtlib_purify($_REQUEST['fieldid']));
		}

		if ($_REQUEST['what_to_do']=='show') {
			$adb->pquery("update vtiger_blocks set display_status='1' where blockid=?", array(vtlib_purify($_REQUEST['blockid'])));
		}

		if ($_REQUEST['what_to_do']=='hide') {
			$adb->pquery("update vtiger_blocks set display_status='0' where blockid=?", array(vtlib_purify($_REQUEST['blockid'])));
		}
	}
}

function getFieldInfo($fieldname, $typeofdata, $quickcreate, $massedit, $presence, $strictlyMandatory, $customfieldflag, $displaytype, $uitype) {
	$fieldtype =  explode('~', $typeofdata);
	if ($strictlyMandatory) {//fields without which the CRM Record will be inconsistent
		$mandatory = '0';
	} elseif ($fieldtype[1] == 'M') {//fields which are made mandatory
		$mandatory = '2';
	} else {
		$mandatory = '1'; //fields not mandatory
	}
	if ($uitype == 4 || $displaytype == 2) {
		$mandatory = '3';
	}

	$visibility = array();
	$visibility['mandatory']	= $mandatory;
	$visibility['quickcreate']	= $quickcreate;
	$visibility['presence']		= $presence;
	$visibility['massedit']		= $massedit;
	$visibility['displaytype']	= $displaytype;
	$visibility['customfieldflag'] = $customfieldflag;
	$visibility['fieldtype'] = $fieldtype[1];
	return $visibility;
}

function updateFieldProperties() {
	global $adb;
	$fieldid = vtlib_purify($_REQUEST['fieldid']);
	$req_sql = "select * from vtiger_field where fieldid = ? and fieldname not in('salutationtype') and vtiger_field.presence in (0,2)";
	$req_result = $adb->pquery($req_sql, array($fieldid));

	$typeofdata = $adb->query_result($req_result, 0, 'typeofdata');
	$tabid = $adb->query_result($req_result, 0, 'tabid');
	$fieldname = $adb->query_result($req_result, 0, 'fieldname');
	$uitype = $adb->query_result($req_result, 0, 'uitype');
	$oldquickcreate = $adb->query_result($req_result, 0, 'quickcreate');
	$oldmassedit = $adb->query_result($req_result, 0, 'masseditable');
	$oldpresence = $adb->query_result($req_result, 0, 'presence');

	$cal_uitype = vtlib_purify($_REQUEST['uitype']);
	$longfield_check = isset($_REQUEST['longfield']) ? vtlib_purify($_REQUEST['longfield']) : '';
	if ($cal_uitype == 19 && $longfield_check == 'false') {
		$adb->pquery('UPDATE vtiger_field SET uitype=? WHERE fieldid=?', array(21, $fieldid));
	}
	if ($cal_uitype == 21 && $longfield_check == 'true') {
		$adb->pquery('UPDATE vtiger_field SET uitype=? WHERE fieldid=?', array(19, $fieldid));
	}

	if (!empty($_REQUEST['fld_module'])) {
		$fld_module = vtlib_purify($_REQUEST['fld_module']);
	} else {
		$fld_module = getTabModuleName($tabid);
	}

	$focus = CRMEntity::getInstance($fld_module);

	$fieldtype =  explode('~', $typeofdata);
	$mandatory_checked= vtlib_purify($_REQUEST['ismandatory']);
	$quickcreate_checked = vtlib_purify($_REQUEST['quickcreate']);
	$presence_check = vtlib_purify($_REQUEST['isPresent']);
	$massedit_check = vtlib_purify($_REQUEST['massedit']);
	$defaultvalue = vtlib_purify($_REQUEST['defaultvalue']);
	$dependentmodules = isset($_REQUEST['dependentmoduleselected']) ? vtlib_purify($_REQUEST['dependentmoduleselected']) : null;

	if (!empty($dependentmodules)) {
		$newdependetmodules = explode(',', $dependentmodules);
		$result = $adb->pquery('SELECT relmodule FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=?', array($fieldid, $fld_module));
		$olddependetmodules = array();
		for ($i=0; $i<$adb->num_rows($result); $i++) {
			$olddependetmodules[] = $adb->query_result($result, $i, 'relmodule');
		}

		foreach ($newdependetmodules as $module) {
			if (!in_array($module, $olddependetmodules)) {
				$parentmodule = Vtiger_Module::getInstance($fld_module);
				$relationfield = Vtiger_Field::getInstance($fieldname, $parentmodule);
				$relationfield->setRelatedModules($module);
				$relatedModule = Vtiger_Module::getInstance($module);
				$relatedModule->setRelatedList($parentmodule, $fld_module, array('ADD'), 'get_dependents_list');
			}
		}

		foreach ($olddependetmodules as $module) {
			if (!in_array($module, $newdependetmodules)) {
				$parentmodule = Vtiger_Module::getInstance($fld_module);
				$relationfield = Vtiger_Field::getInstance($fieldname, $parentmodule);
				$relationfield->unsetRelatedModules($module);
				$relatedmodule = Vtiger_Module::getInstance($module);
				$relatedmodule->unsetRelatedList($parentmodule, $fld_module, 'get_dependents_list');
			}
		}
	}

	if (!empty($defaultvalue)) {
		if ($uitype == 56) {
			if ($defaultvalue == 'on' || $defaultvalue == '1') {
				$defaultvalue = '1';
			} elseif ($defaultvalue == 'off' || $defaultvalue == '0') {
				$defaultvalue = '0';
			} else {
				$defaultvalue = '';
			}
		} elseif ($uitype == 5 || $uitype == 6 || $uitype == 23) {
			$defaultvalue = getValidDBInsertDateValue($defaultvalue);
		}
	}

	if (isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields)) && in_array($fieldname, $focus->mandatory_fields)) {
		$fieldtype[1] = 'M';
	} elseif ($mandatory_checked == 'true' || $mandatory_checked == '') {
		$fieldtype[1] = 'M';
	} else {
		$fieldtype[1] = 'O';
	}
	$datatype = implode('~', $fieldtype);
	$maxseq = 0;
	if ($oldquickcreate != 3) {
		if ($quickcreate_checked == 'true' || $quickcreate_checked == '') {
			$qcdata = 2;
			$quickcreateseq_Query = 'select coalesce(max(quickcreatesequence), 0) as maxseq from vtiger_field where tabid=?';
			$res = $adb->pquery($quickcreateseq_Query, array($tabid));
			$maxseq = $adb->query_result($res, 0, 'maxseq');
		} else {
			$qcdata = 1;
		}
	}
	if ($oldpresence != 3) {
		if ($presence_check == 'true' || $presence_check == '') {
			$presence = 2;
		} else {
			$presence = 1;
		}
	} else {
		$presence =1;
	}

	if ($oldmassedit != 3) {
		if ($massedit_check == 'true' || $massedit_check == '') {
			$massedit = 1;
		} else {
			$massedit = 2;
		}
	} else {
		$massedit=1;
	}

	if (isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields))) {
		$fieldname_list = implode(',', $focus->mandatory_fields);
	} else {
		$fieldname_list = '';
	}

	$mandatory_query = 'update vtiger_field set typeofdata=? where fieldid=? and fieldname not in (?) AND displaytype != 2';
	$mandatory_params = array($datatype,$fieldid,$fieldname_list);
	$adb->pquery($mandatory_query, $mandatory_params);

	if (!empty($qcdata)) {
		$quickcreate_query = 'update vtiger_field set quickcreate = ? ,quickcreatesequence = ? where fieldid = ? and quickcreate not in (0,3) AND displaytype != 2';
		$quickcreate_params = array($qcdata,$maxseq+1,$fieldid);
		$adb->pquery($quickcreate_query, $quickcreate_params);
	}

	$presence_query = 'update vtiger_field set presence = ? where fieldid = ? and presence not in (0,3) and quickcreate != 0';
	$quickcreate_params = array($presence,$fieldid);
	$adb->pquery($presence_query, $quickcreate_params);

	$massedit_query = 'update vtiger_field set masseditable = ? where fieldid = ? and masseditable not in (0,3) AND displaytype != 2';
	$massedit_params = array($massedit,$fieldid);
	$adb->pquery($massedit_query, $massedit_params);

	$defaultvalue_query = 'update vtiger_field set defaultvalue=? where fieldid = ? and fieldname not in (?) AND displaytype != 2';
	$defaultvalue_params = array($defaultvalue,$fieldid,$fieldname_list);
	$adb->pquery($defaultvalue_query, $defaultvalue_params);
}

function deleteCustomField() {
	global $adb;

	$fld_module = vtlib_purify($_REQUEST['fld_module']);
	$id = vtlib_purify($_REQUEST['fld_id']);
	$colName = vtlib_purify($_REQUEST['colName']);
	$uitype = vtlib_purify($_REQUEST['uitype']);

	$fieldquery = 'select * from vtiger_field where fieldid = ?';
	$res = $adb->pquery($fieldquery, array($id));

	$typeofdata = $adb->query_result($res, 0, 'typeofdata');
	$fieldname = $adb->query_result($res, 0, 'fieldname');
	$oldfieldlabel = $adb->query_result($res, 0, 'fieldlabel');
	$tablename = $adb->query_result($res, 0, 'tablename');
	$columnname = $adb->query_result($res, 0, 'columnname');
	$fieldtype =  explode('~', $typeofdata);

	//Deleting the CustomField from the Custom Field Table
	$query='delete from vtiger_field where fieldid = ? and vtiger_field.presence in (0,2)';
	$adb->pquery($query, array($id));

	//Deleting from vtiger_profile2field table
	$query='delete from vtiger_profile2field where fieldid=?';
	$adb->pquery($query, array($id));

	//Deleting from vtiger_def_org_field table
	$query='delete from vtiger_def_org_field where fieldid=?';
	$adb->pquery($query, array($id));

	$focus = CRMEntity::getInstance($fld_module);

	$deletecolumnname =$tablename .':'. $columnname .':'.$fieldname.':'.$fld_module. '_' .str_replace(' ', '_', $oldfieldlabel).':'.$fieldtype[0];
	$column_cvstdfilter = 	$tablename .':'. $columnname .':'.$fieldname.':'.$fld_module. '_' .str_replace(' ', '_', $oldfieldlabel);
	$select_columnname = $tablename.':'.$columnname .':'.$fld_module. '_' . str_replace(' ', '_', $oldfieldlabel).':'.$fieldname.':'.$fieldtype[0];
	$reportsummary_column = $tablename.':'.$columnname.':'.str_replace(' ', '_', $oldfieldlabel);

	$dbquery = 'alter table '. $adb->sql_escape_string($focus->customFieldTable[0]).' drop column '. $adb->sql_escape_string($colName);
	$adb->pquery($dbquery, array());

	//To remove customfield entry from vtiger_field table
	$dbquery = 'delete from vtiger_field where columnname= ? and fieldid=? and vtiger_field.presence in (0,2)';
	$adb->pquery($dbquery, array($colName, $id));
	//we have to remove the entries in customview and report related tables which have this field ($colName)
	$adb->pquery('delete from vtiger_cvcolumnlist where columnname = ? ', array($deletecolumnname));
	$adb->pquery('delete from vtiger_cvstdfilter where columnname = ?', array($column_cvstdfilter));
	$adb->pquery('delete from vtiger_cvadvfilter where columnname = ?', array($deletecolumnname));
	$adb->pquery('delete from vtiger_selectcolumn where columnname = ?', array($select_columnname));
	$adb->pquery('delete from vtiger_relcriteria where columnname = ?', array($select_columnname));
	$adb->pquery('delete from vtiger_reportsortcol where columnname = ?', array($select_columnname));
	$adb->pquery('delete from vtiger_reportdatefilter where datecolumnname = ?', array($column_cvstdfilter));
	$adb->pquery('delete from vtiger_reportsummary where columnname like ?', array('%'.$reportsummary_column.'%'));

	//Deleting from convert lead mapping vtiger_table- Jaguar
	if ($fld_module=='Leads') {
		$deletequery = 'delete from vtiger_convertleadmapping where leadfid=?';
		$adb->pquery($deletequery, array($id));
	} elseif ($fld_module=='Accounts' || $fld_module=='Contacts' || $fld_module=='Potentials') {
		$map_del_id = array('Accounts'=>'accountfid','Contacts'=>'contactfid','Potentials'=>'potentialfid');
		$map_del_q = 'update vtiger_convertleadmapping set '.$map_del_id[$fld_module].'=0 where '.$map_del_id[$fld_module].'=?';
		$adb->pquery($map_del_q, array($id));
	}

	//HANDLE HERE - we have to remove the table for other picklist type values which are text area and multiselect combo box
	if ($uitype == 15) {
		$deltablequery = 'drop table vtiger_'.$adb->sql_escape_string($colName);
		$adb->pquery($deltablequery, array());

		$deltablequery_seq = 'drop table vtiger_'.$adb->sql_escape_string($colName).'_seq';
		$adb->pquery($deltablequery_seq, array());
		//Remove picklist dependencies
		$adb->pquery('DELETE FROM vtiger_picklist_dependency WHERE vtiger_picklist_dependency.targetfield = ?', array($colName));
	}
	if ($uitype == 10) {
		$adb->pquery('DELETE FROM vtiger_fieldmodulerel WHERE fieldid=?', array($id));
	}
}

function addblock() {
	global $adb;
	$fldmodule = vtlib_purify($_REQUEST['fld_module']);

	$newblocklabel = trim(vtlib_purify($_REQUEST['blocklabel']));
	$after_block = vtlib_purify($_REQUEST['after_blockid']);

	$tabid = getTabid($fldmodule);
	$flag = 0;
	$dup_check_query = $adb->pquery('SELECT blocklabel from vtiger_blocks WHERE tabid = ?', array($tabid));
	$norows = $adb->num_rows($dup_check_query);
	for ($i=0; $i<$norows; $i++) {
		$blklbl = $adb->query_result($dup_check_query, $i, 'blocklabel');
		$blklbltran = getTranslatedString($blklbl, $fldmodule);
		if (strtolower($blklbltran) == strtolower($newblocklabel)) {
			$flag = 1;
			return 'yes';
		}
	}
	$length = strlen($newblocklabel);
	if ($length > 50) {
		$flag = 1;
		return 'LENGTH_ERROR';
	}

	if ($flag!=1) {
		$related_module = vtlib_purify($_REQUEST['relblock']);
		if ($related_module=='no') {
			$relatedlistid = 0;
		} else {
			if (is_numeric($related_module)) {
				$rlrs = $adb->pquery(
					'select relation_id,label from vtiger_relatedlists where relation_id=?',
					array($related_module)
				);
			} else {
				$related_moduleid = getTabid($related_module);
				$rlrs = $adb->pquery(
					'select relation_id,label from vtiger_relatedlists where tabid=? and related_tabid=?',
					array($tabid,$related_moduleid)
				);
			}
			if ($rlrs && $adb->num_rows($rlrs)>0) {
				$relatedlistid = $adb->query_result($rlrs, 0, 'relation_id');
				$newblocklabel = $adb->query_result($rlrs, 0, 'label');
			} else {
				$relatedlistid = 0;
			}
		}
		$sql_seq='select sequence from vtiger_blocks where blockid=?';
		$res_seq= $adb->pquery($sql_seq, array($after_block));
		$row_seq=$adb->fetch_array($res_seq);
		$block_sequence=$row_seq['sequence'];
		$newblock_sequence=$block_sequence+1;

		$sql_up='update vtiger_blocks set sequence=sequence+1 where tabid=? and sequence > ?';
		$adb->pquery($sql_up, array($tabid,$block_sequence));

		$max_blockid=$adb->getUniqueID('vtiger_blocks');
		$iscustom = 1;
		$sql='INSERT INTO vtiger_blocks (tabid, blockid, sequence, blocklabel,iscustom,isrelatedlist) values (?,?,?,?,?,?)';
		$params = array($tabid,$max_blockid,$newblock_sequence,$newblocklabel,$iscustom,$relatedlistid);
		$adb->pquery($sql, $params);
	}
}

function deleteBlock() {
	global $adb;
	$blockid = vtlib_purify($_REQUEST['blockid']);
	// move any hidden fields to another block
	$adb->pquery(
		'update vtiger_field set vtiger_field.block = (select vtiger_blocks.blockid from vtiger_blocks
		where vtiger_blocks.blockid!=? and tabid=(select vtiger_blocks.tabid from vtiger_blocks where vtiger_blocks.blockid=?) limit 1)
		where vtiger_field.block=?',
		array($blockid,$blockid,$blockid)
	);
	$adb->pquery('delete from vtiger_blocks where blockid = ? and iscustom = 1', array($blockid));
}

function addCustomField() {
	global $adb;

	$fldmodule = vtlib_purify($_REQUEST['fld_module']);
	$fldlabel = vtlib_purify(trim($_REQUEST['fldLabel']));
	$fldType = vtlib_purify($_REQUEST['fieldType']);
	$mode = isset($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '';
	$blockid = vtlib_purify($_REQUEST['blockid']);

	$tabid = getTabid($fldmodule);
	$checkresult=$adb->pquery('select * from vtiger_field where tabid=? and fieldlabel=?', array($tabid, $fldlabel));
	if ($adb->num_rows($checkresult) > 0) {
		return 'yes';
	} else {
		$max_fieldid = $adb->getUniqueID('vtiger_field');
		$columnName = 'cf_'.$max_fieldid;
		$custfld_fieldid = $max_fieldid;
		//Assigning the vtiger_table Name
		if ($fldmodule != '') {
			$focus = CRMEntity::getInstance($fldmodule);
			if (isset($focus->customFieldTable)) {
				$tableName=$focus->customFieldTable[0];
			} else {
				$tableName= 'vtiger_'.strtolower($fldmodule).'cf';
			}
		}
		//Assigning the uitype
		$fldlength = vtlib_purify($_REQUEST['fldLength']);
		$uitype='';
		$fldPickList='';
		if (isset($_REQUEST['fldDecimal']) && $_REQUEST['fldDecimal'] != '') {
			$decimal = vtlib_purify($_REQUEST['fldDecimal']);
		} else {
			$decimal=0;
		}
		$type='';
		$uichekdata='';
		if ($fldType == 'Text') {
			$uichekdata='V~O~LE~'.$fldlength;
			$uitype = 1;
			$type = 'C('.$fldlength.') default ()'; // adodb type
		} elseif ($fldType == 'Number') {
			$uitype = 7;
			//this may sound ridiculous passing decimal but that is the way adodb wants
			$dbfldlength = $fldlength + $decimal + 1;
			$type='N('.$dbfldlength.'.'.$decimal.')';	// adodb type
			$uichekdata='NN~O~'.$fldlength .','.$decimal;
		} elseif ($fldType == 'Percent') {
			$uitype = 9;
			$type='N(5.2)'; //adodb type
			$uichekdata='N~O~2~2';
		} elseif ($fldType == 'Currency') {
			$uitype = 71;
			if ($decimal<2) {
				$decimal=2;
			}
			$dbfldlength = $fldlength + $decimal + 1;
			$type='N('.$dbfldlength.'.'.$decimal.')'; //adodb type
			$uichekdata='N~O~'.$fldlength .','.$decimal;
		} elseif ($fldType == 'Date') {
			$uichekdata='D~O';
			$uitype = 5;
			$type = 'D'; // adodb type
		} elseif ($fldType == 'Datetime') {
			$uichekdata='DT~O';
			$uitype = 50;
			$type = 'T'; // adodb type
		} elseif ($fldType == 'Email') {
			$uitype = 13;
			$type = 'C(50) default () '; //adodb type
			$uichekdata='E~O';
		} elseif ($fldType == 'Time') {
			$uitype = 14;
			$type = 'TIME';
			$uichekdata='T~O';
		} elseif ($fldType == 'Phone') {
			$uitype = 11;
			$type = 'C(30) default () '; //adodb type
			$uichekdata='V~O';
		} elseif ($fldType == 'Picklist') {
			$uitype = 15;
			$type = 'C(255) default () '; //adodb type
			$uichekdata='V~O';
		} elseif ($fldType == 'URL') {
			$uitype = 17;
			$type = 'C(255) default () '; //adodb type
			$uichekdata='V~O';
		} elseif ($fldType == 'Checkbox') {
			$uitype = 56;
			$type = 'C(3) default 0'; //adodb type
			$uichekdata='C~O';
		} elseif ($fldType == 'TextArea') {
			$uitype = 21;
			$type = 'X'; //adodb type
			$uichekdata='V~O';
		} elseif ($fldType == 'MultiSelectCombo') {
			$uitype = 33;
			$type = 'X'; //adodb type
			$uichekdata='V~O';
		} elseif ($fldType == 'Skype') {
			$uitype = 85;
			$type = 'C(255) default () '; //adodb type
			$uichekdata='V~O';
		} elseif ($fldType == 'Relation') {
			$uitype = 10;
			$type = 'I(11) '; //adodb type
			$uichekdata='I~O';
		} elseif ($fldType == 'Image') {
			$uitype = 69;
			$type = 'C(255) '; //adodb type
			$uichekdata='V~O';
		}

		if (is_numeric($blockid) && empty($_REQUEST['fieldid'])) {
			$rdoadd = $adb->alterTable($tableName, $columnName.' '.$type, 'Add_Column');
			if ($rdoadd==1) {
				if (substr($adb->database->ErrorMsg(), 0, 18) == 'Row size too large') {
					echo 'ROWSIZEERROR::' . getTranslatedString('ROWSIZEERROR', 'Settings');
				} else {
					echo 'ADDFIELDERROR::' . getTranslatedString('ADDFIELDERROR', 'Settings');
				}
				die();
			}
			$res = $adb->pquery('select coalesce(max(sequence), 0) as maxsequence from vtiger_field where block = ?', array($blockid));
			$max_seq = $adb->query_result($res, 0, 'maxsequence');
			if ($fldmodule == 'Quotes' || $fldmodule == 'PurchaseOrder' || $fldmodule == 'SalesOrder' || $fldmodule == 'Invoice') {
				$quickcreate = 3;
			} else {
				$quickcreate = 1;
			}
			$query = 'insert into vtiger_field
				(tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, defaultvalue, maximumlength, sequence,
					block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable)
				values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
			$qparams = array(
				$tabid, $custfld_fieldid, $columnName, $tableName, 2, $uitype, $columnName, $fldlabel,
				0, 2, '', 100, $max_seq+1, $blockid, 1, $uichekdata, $quickcreate, 0, 'BAS', 1
			);
			$adb->pquery($query, $qparams);
			//Inserting values into vtiger_profile2field tables
			$sql1_result = $adb->pquery('select * from vtiger_profile', array());
			$sql1_num = $adb->num_rows($sql1_result);
			$sql2 = 'insert into vtiger_profile2field values(?,?,?,?,?,?)';
			for ($i=0; $i<$sql1_num; $i++) {
				$profileid = $adb->query_result($sql1_result, $i, 'profileid');
				$adb->pquery($sql2, array($profileid, $tabid, $custfld_fieldid, 0, 0, 'B'));
			}

			//Inserting values into def_org tables
			$adb->pquery('insert into vtiger_def_org_field values(?,?,?,?)', array($tabid, $custfld_fieldid, 0, 0));

			if ($fldType == 'Relation') {
				$moduleInstance = Vtiger_Module::getInstance($tabid);
				$field = Vtiger_Field::getInstance($custfld_fieldid, $moduleInstance);
				if ($field) {
					$moduleNames = explode(';', trim($_REQUEST['relationmodules'], ';'));
					$field->setRelatedModules($moduleNames);
					foreach ($moduleNames as $mod) {
						$modrel = Vtiger_Module::getInstance($mod);
						$modrel->setRelatedList($moduleInstance, $fldmodule, array('ADD'), 'get_dependents_list', $custfld_fieldid, '1:N');
					}
				}
			}
			if ($fldType == 'Picklist' || $fldType == 'MultiSelectCombo') {
				$columnName = $adb->sql_escape_string($columnName);
				// Creating the PickList Table and Populating Values
				if (empty($_REQUEST['fieldid'])) {
					$qur = 'CREATE TABLE vtiger_'.$columnName.' (
						'.$columnName.'id int(19) NOT NULL auto_increment,
						'.$columnName." varchar(200) NOT NULL,
						presence int(1) NOT NULL default '1',
						picklist_valueid int(19) NOT NULL default '0',
						PRIMARY KEY  (".$columnName.'id)
					)';
					$adb->pquery($qur, array());
				}

				//Adding a  new picklist value in the picklist table
				if ($mode != 'edit') {
					$picklistid = $adb->getUniqueID('vtiger_picklist');
					$adb->pquery('insert into vtiger_picklist values(?,?,1)', array($picklistid, $columnName));
				}
				$rs = $adb->pquery('select picklistid from vtiger_picklist where name=?', array($columnName));
				$picklistid = $adb->query_result($rs, 0, 'picklistid');
				$pickArray = array();
				$fldPickList = vtlib_purify($_REQUEST['fldPickList']);
				$pickArray = explode("\n", $fldPickList);
				$count = count($pickArray);
				for ($i = 0; $i < $count; $i++) {
					$pickArray[$i] = trim($pickArray[$i]);
					if ($pickArray[$i] != '') {
						$picklistcount=0;
						$sql ="select $columnName from vtiger_$columnName";
						$rs = $adb->pquery($sql, array());
						$numrow = $adb->num_rows($rs);
						for ($x=0; $x < $numrow; $x++) {
							$picklistvalues = $adb->query_result($rs, $x, $columnName);
							if ($pickArray[$i] == $picklistvalues) {
								$picklistcount++;
							}
						}
						if ($picklistcount == 0) {
							$picklist_valueid = getUniquePicklistID();
							$query = 'insert into vtiger_'.$columnName.' values(?,?,?,?)';
							$adb->pquery($query, array($adb->getUniqueID('vtiger_'.$columnName),$pickArray[$i],1,$picklist_valueid));
						}
						$sql = "select picklist_valueid from vtiger_$columnName where $columnName=?";
						$rs = $adb->pquery($sql, array($pickArray[$i]));
						$pick_valueid = $adb->query_result($rs, 0, 'picklist_valueid');
						$sql = "insert into vtiger_role2picklist select roleid,$pick_valueid,$picklistid,$i from vtiger_role";
						$adb->pquery($sql, array());
					}
				}
			}
		}
	}
}

function show_move_hiddenfields($submode) {
	global $adb;
	$selected_fields = vtlib_purify($_REQUEST['selected']);
	$selected = trim($selected_fields, ':');
	$sel_arr = array();
	$sel_arr = explode(':', $selected);
	$sequence = $adb->pquery(
		'select coalesce(max(sequence), 0) as maxseq from vtiger_field where block=? and tabid=?',
		array(vtlib_purify($_REQUEST['blockid']), vtlib_purify($_REQUEST['tabid']))
	);
	$max = $adb->query_result($sequence, 0, 'maxseq');
	$max_seq = $max + 1;

	if ($submode == 'showhiddenfields') {
		for ($i=0; $i< count($sel_arr); $i++) {
			$adb->pquery(
				'update vtiger_field set presence=2,sequence=? where block=? and fieldid=?',
				array($max_seq, vtlib_purify($_REQUEST['blockid']),$sel_arr[$i])
			);
			$max_seq++;
		}
	} else {
		for ($i=0; $i< count($sel_arr); $i++) {
			$adb->pquery(
				'update vtiger_field set sequence=?, block=? where fieldid=?',
				array($max_seq, vtlib_purify($_REQUEST['blockid']),$sel_arr[$i])
			);
			$max_seq++;
		}
	}
}

function getRelatedListInfo($module) {
	global $adb;
	$tabid = getTabid($module);
	$related_query = 'select *
		from vtiger_relatedlists
		left join vtiger_tab on vtiger_relatedlists.related_tabid = vtiger_tab.tabid and vtiger_tab.presence = 0
		where vtiger_relatedlists.tabid = ? order by sequence';
	$relinfo = $adb->pquery($related_query, array($tabid));
	$noofrows = $adb->num_rows($relinfo);
	$res = array();
	for ($i=0; $i<$noofrows; $i++) {
		$res[$i]['name'] = $adb->query_result($relinfo, $i, 'name');
		$res[$i]['sequence'] = $adb->query_result($relinfo, $i, 'sequence');
		$label = $adb->query_result($relinfo, $i, 'label');
		$relatedModule = getTabname($adb->query_result($relinfo, $i, 'related_tabid'));
		$res[$i]['label'] = (empty($relatedModule) ? getTranslatedString($label, $module) : getTranslatedString($label, $relatedModule));
		$res[$i]['presence'] = $adb->query_result($relinfo, $i, 'presence');
		$res[$i]['tabid'] = $tabid;
		$res[$i]['id'] = $adb->query_result($relinfo, $i, 'relation_id');
	}
	return $res;
}

function deleteRelatedList() {
	global $adb;
	$tabid = vtlib_purify($_REQUEST['tabid']);
	$sequence = vtlib_purify($_REQUEST['sequence']);
	$relationid = vtlib_purify($_REQUEST['id']);
	$adb->pquery('delete from vtiger_relatedlists where relation_id=?', array($relationid));
	$adb->pquery('update vtiger_relatedlists set sequence=sequence-1 where sequence>? and tabid=?', array($sequence,$tabid));
}

function createRelatedList() {
	$module = vtlib_purify($_REQUEST['fld_module']);
	$tabmod = Vtiger_Module::getInstance($module);
	$rmodule = vtlib_purify($_REQUEST['relwithmod']);
	$relmod = Vtiger_Module::getInstance($rmodule);
	$actions = array('ADD','SELECT');
	switch ($rmodule) {
		case 'Documents':
			$funcname = 'get_attachments';
			break;
		case 'cbCalendar':
			$funcname = 'get_activities';
			$actions = array('ADD');
			break;
		default:
			$funcname = 'get_related_list';
			break;
	}
	$tabmod->setRelatedList($relmod, $rmodule, $actions, $funcname);
}

function changeRelatedListOrder() {
	global $adb;
	$tabid = vtlib_purify($_REQUEST['tabid']);
	$what_todo = vtlib_purify($_REQUEST['what_to_do']);
	if (!empty($what_todo)) {
		if ($what_todo == 'move_up') {
			$currentsequence = vtlib_purify($_REQUEST['sequence']);

			$previous_relation = $adb->pquery(
				'select relation_id,sequence from vtiger_relatedlists where sequence < ? and tabid = ? order by sequence desc limit 0,1',
				array($currentsequence,$tabid)
			);
			$previous_sequence = $adb->query_result($previous_relation, 0, 'sequence');
			$previous_relationid = $adb->query_result($previous_relation, 0, 'relation_id');

			$adb->pquery(
				'update vtiger_relatedlists set sequence = ? where relation_id = ? and tabid = ?',
				array($previous_sequence, vtlib_purify($_REQUEST['id']),$tabid)
			);
			$adb->pquery('update vtiger_relatedlists set sequence = ? where tabid = ? and relation_id = ?', array($currentsequence,$tabid,$previous_relationid));
		} elseif ($what_todo == 'move_down') {
			$currentsequence = vtlib_purify($_REQUEST['sequence']);

			$next_relation = $adb->pquery(
				'select relation_id,sequence from vtiger_relatedlists where sequence > ? and tabid = ? order by sequence limit 0,1',
				array($currentsequence,$tabid)
			);
			$next_sequence = $adb->query_result($next_relation, 0, 'sequence');
			$next_relationid = $adb->query_result($next_relation, 0, 'relation_id');

			$adb->pquery('update vtiger_relatedlists set sequence = ? where relation_id = ? and tabid = ?', array($next_sequence,  vtlib_purify($_REQUEST['id']),$tabid));
			$adb->pquery('update vtiger_relatedlists set sequence = ? where tabid = ? and relation_id = ?', array($currentsequence,$tabid,$next_relationid));
		}
	}
}
?>
