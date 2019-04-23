<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
if (isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	$reportid = vtlib_purify($_REQUEST['record']);
	$oReport = new Reports($reportid);
	$oReport->getAdvancedFilterList($reportid);

	$oRep = new Reports();
	$secondarymodule = '';
	$secondarymodules =array();

	if (!empty($oRep->related_modules[$oReport->primodule])) {
		foreach ($oRep->related_modules[$oReport->primodule] as $value) {
			if (isset($_REQUEST['secondarymodule_'.$value])) {
				$secondarymodules []= $_REQUEST['secondarymodule_'.$value];
			}
		}
	}
	$secondarymodule = implode(':', $secondarymodules);

	if ($secondarymodule!='') {
		$oReport->secmodule = $secondarymodule;
	}

	$COLUMNS_BLOCK = getPrimaryColumns_AdvFilterHTML($oReport->primodule);
	$COLUMNS_BLOCK .= getSecondaryColumns_AdvFilterHTML($oReport->secmodule);
	$report_std_filter->assign("COLUMNS_BLOCK", $COLUMNS_BLOCK);
	$FILTER_OPTION = Reports::getAdvCriteriaHTML();
	$report_std_filter->assign("FOPTION", $FILTER_OPTION);
	$rel_fields = getRelatedFieldColumns();
	$report_std_filter->assign("REL_FIELDS", json_encode($rel_fields));
	$report_std_filter->assign("CRITERIA_GROUPS", $oReport->advft_criteria);
} else {
	$primarymodule = $_REQUEST["primarymodule"];
	$COLUMNS_BLOCK = getPrimaryColumns_AdvFilterHTML($primarymodule);
	$ogReport = new Reports();
	if (!empty($ogReport->related_modules[$primarymodule])) {
		foreach ($ogReport->related_modules[$primarymodule] as $value) {
			//$BLOCK1 .= getSecondaryColumnsHTML($_REQUEST["secondarymodule_".$value]);
			$COLUMNS_BLOCK .= getSecondaryColumns_AdvFilterHTML($_REQUEST["secondarymodule_".$value]);
		}
	}
	$report_std_filter->assign("COLUMNS_BLOCK", $COLUMNS_BLOCK);
	$rel_fields = getRelatedFieldColumns();
	$report_std_filter->assign("REL_FIELDS", json_encode($rel_fields));
}

/** Function to get primary columns for an advanced filter
 *  This function accepts The module as an argument
 *  This generate columns of the primary modules for the advanced filter
 *  It returns a HTML string of combo values
 */
function getPrimaryColumns_AdvFilterHTML($module, $selected = "") {
	global $ogReport;
	$selected = decode_html($selected);
	$block_listed = array();
	$i18nModule = getTranslatedString($module, $module);
	foreach ($ogReport->module_list[$module] as $value) {
		if (isset($ogReport->pri_module_columnslist[$module][$value]) && !$block_listed[$value]) {
			$block_listed[$value] = true;
			$shtml .= "<optgroup label=\"".$i18nModule." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
			foreach ($ogReport->pri_module_columnslist[$module][$value] as $field => $fieldlabel) {
				$field = decode_html($field);
				$fldlbl = str_replace(array("\n","\r"), '', getTranslatedString($fieldlabel, $module));
				$shtml .= '<option '.($selected == $field ? 'selected' : '').' value="'.$field."\">$fldlbl</option>";
			}
		}
	}
	return $shtml;
}

/** Function to get Secondary columns for an advanced filter
 *  This function accepts The module as an argument
 *  This generate columns of the secondary module for the advanced filter
 *  It returns a HTML string of combo values
 */
function getSecondaryColumns_AdvFilterHTML($module, $selected = "") {
	global $ogReport;
	if ($module != '') {
		$secmodule = explode(":", $module);
		for ($i=0; $i < count($secmodule); $i++) {
			if (vtlib_isModuleActive($secmodule[$i])) {
				$block_listed = array();
				$i18nModule = getTranslatedString($secmodule[$i], $secmodule[$i]);
				foreach ($ogReport->module_list[$secmodule[$i]] as $value) {
					if (isset($ogReport->sec_module_columnslist[$secmodule[$i]][$value]) && !$block_listed[$value]) {
						$block_listed[$value] = true;
						$shtml .= "<optgroup label=\"".$i18nModule." ".getTranslatedString($value, $secmodule[$i])."\" class=\"select\" style=\"border:none\">";
						foreach ($ogReport->sec_module_columnslist[$secmodule[$i]][$value] as $field => $fieldlabel) {
							$fldlbl = str_replace(array("\n","\r"), '', getTranslatedString($fieldlabel, $secmodule[$i]));
							$field = decode_html($field);
							$shtml .= '<option '.($selected == $field ? 'selected' : '').' value="'.$field."\">$fldlbl</option>";
						}
					}
				}
			}
		}
	}
	return $shtml;
}

function getRelatedColumns($selected = '') {
	global $ogReport;
	$rel_fields = $ogReport->adv_rel_fields;
	if ($selected!='All') {
		$selected = explode(":", $selected);
	}
	$related_fields = array();
	foreach ($rel_fields as $i => $index) {
		$shtml='';
		foreach ($index as $value) {
			$fieldarray = explode("::", $value);
			$shtml .= "<option value=\"".$fieldarray[0]."\">".$fieldarray[1]."</option>";
		}
		$related_fields[$i] = $shtml;
	}
	if (!empty($selected) && $selected[4]!='') {
		return $related_fields[$selected[4]];
	} elseif ($selected=='All') {
		return $related_fields;
	} else {
		return ;
	}
}

function getRelatedFieldColumns($selected = '') {
	global $ogReport;
	return $ogReport->adv_rel_fields;
}
?>