<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/*
 * File containing methods to proceed with the ui validation for all the forms
 *
 */
/**
 * Get field validation information
 */
function getDBValidationData($tablearray, $tabid='') {
	if($tabid != '') {
		global $adb, $mod_strings, $default_charset;
		$fieldModuleName = getTabModuleName($tabid);
		$fieldres = $adb->pquery(
			"SELECT fieldlabel,fieldname,typeofdata FROM vtiger_field
			WHERE displaytype IN (1,3) AND presence in (0,2) AND tabid=?", Array($tabid));
		$fieldinfos = Array();
		while($fieldrow = $adb->fetch_array($fieldres)) {
			$fieldlabel = getTranslatedString(html_entity_decode($fieldrow['fieldlabel'], ENT_QUOTES, $default_charset), $fieldModuleName);
			$fieldname = $fieldrow['fieldname'];
			$typeofdata= $fieldrow['typeofdata'];
			$fieldinfos[$fieldname] = Array($fieldlabel => $typeofdata);
		}
		return $fieldinfos;
	} else {
		return array();
	}
}
?>
