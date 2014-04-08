<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include('ConfigurationUtils.php');
$modules = ConfigurationUtils::getEntityModule();
$moduleLabels = array();
foreach($modules as $module) {
	$moduleLabels[$module] = getTranslatedString($module, $module);
}

$trueFalseArray = array(
	'true' => getTranslatedString('LBL_TRUE','ConfigEditor'),
	'false' => getTranslatedString('LBL_FALSE','ConfigEditor')
);

$__ConfigEditor_Config = array(
	
	'edit.filepath' => dirname(__FILE__) . '/../../config.inc.php',

	/* CONFIGURE:
	 * List the configuration variables that user can set.
	 * By setting it to array() lets allows editing of all variables but it is not RECOMMENDED
	 */
	'allow.editing.variables' => array(
		'CALENDAR_DISPLAY' => array('label'=>getTranslatedString('LBL_MINI_CALENDAR_DISPLAY','ConfigEditor'),'values'=>$trueFalseArray),
		'WORLD_CLOCK_DISPLAY' => array('label'=> getTranslatedString('LBL_WORLD_CLOCK_DISPLAY','ConfigEditor'),'values'=>$trueFalseArray),
		'CALCULATOR_DISPLAY' => array('label' => getTranslatedString('LBL_CALCULATOR_DISPLAY','ConfigEditor') , 'values'=>$trueFalseArray),
		'USE_RTE' => array('label'=>getTranslatedString('LBL_USE_RTE','ConfigEditor'), 'values'=>$trueFalseArray),
		'HELPDESK_SUPPORT_EMAIL_ID'=>array('label'=>getTranslatedString('LBL_HELPDESK_SUPPORT_EMAILID','ConfigEditor'),'values'=>array()),
		'HELPDESK_SUPPORT_NAME' => array('label' => getTranslatedString('LBL_HELPDESK_SUPPORT_NAME','ConfigEditor'),'values'=>array()),
		'upload_maxsize' => array('label'=>getTranslatedString('LBL_MAX_UPLOAD_SIZE','ConfigEditor'),'values'=>array()),
		'history_max_viewed' => array('label'=>getTranslatedString('LBL_MAX_HISTORY_VIEWED','ConfigEditor'),'values'=>array()),
		'default_module' => array('label'=>getTranslatedString('LBL_DEFAULT_MODULE','ConfigEditor'),'values'=>$moduleLabels),
		'listview_max_textlength' => array('label' => getTranslatedString('LBL_MAX_TEXT_LENGTH_IN_LISTVIEW','ConfigEditor'), 'values' => array() ),
		'list_max_entries_per_page' => array('label' => getTranslatedString('LBL_MAX_ENTRIES_PER_PAGE_IN_LISTVIEW','ConfigEditor'), 'values'=> array()),
	)
	
);
?>