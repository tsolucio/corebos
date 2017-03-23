<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
require('Smarty/libs/Smarty.class.php');
include_once 'include/smarty/function.process_widget.php';
class vtigerCRM_Smarty extends Smarty{

	/** Cache the tag cloud display information for re-use */
	static $_tagcloud_display_cache = array();

	static function lookupTagCloudView($userid) {
		if(!isset(self::$_tagcloud_display_cache[$userid])) {
			self::$_tagcloud_display_cache[$userid] = getTagCloudView($userid);
		}
		return self::$_tagcloud_display_cache[$userid];
	}
	/** END */

	/** This function sets the smarty directory path for the member variables */
	function __construct()
	{
		global $current_user, $currentModule;

		parent::__construct();
		$this->setTemplateDir('Smarty/templates');
		$this->setCompileDir('Smarty/templates_c');
		$this->setConfigDir('Smarty/configs');
		$this->setCacheDir('Smarty/cache');
		$this->setPluginsDir('Smarty/libs/plugins');

		//$this->caching = true;
		$CALENDAR_DISPLAY = GlobalVariable::getVariable('Application_Display_Mini_Calendar',1,$currentModule);
		$CALENDAR_DISPLAY = empty($CALENDAR_DISPLAY) ? 'false' : 'true';
		$WORLD_CLOCK_DISPLAY = GlobalVariable::getVariable('Application_Display_World_Clock',1,$currentModule);
		$WORLD_CLOCK_DISPLAY = empty($WORLD_CLOCK_DISPLAY) ? 'false' : 'true';
		$CALCULATOR_DISPLAY = GlobalVariable::getVariable('Application_Display_Calculator',1,$currentModule);
		$CALCULATOR_DISPLAY = empty($CALCULATOR_DISPLAY) ? 'false' : 'true';
		$this->assign('CALENDAR_DISPLAY', $CALENDAR_DISPLAY);
		$this->assign('WORLD_CLOCK_DISPLAY', $WORLD_CLOCK_DISPLAY);
		$this->assign('CALCULATOR_DISPLAY', $CALCULATOR_DISPLAY);
		$this->assign('CURRENT_USER_ID',(isset($current_user) ? $current_user->id : 0));
		$this->assign('PRELOAD_JSCALENDAR', GlobalVariable::getVariable('preload_jscalendar','true',$currentModule));

		// Query For TagCloud only when required
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'DetailView') {
			//Added to provide User based Tagcloud
			$this->assign('TAG_CLOUD_DISPLAY', self::lookupTagCloudView($current_user->id) );
		}
		$this->loadFilter('output', 'trimwhitespace');
		$this->registerPlugin('function', 'process_widget', 'smarty_function_process_widget');
	}
}

?>
