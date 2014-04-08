<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/Request.php';
include_once dirname(__FILE__) . '/Viewer.php';
include_once dirname(__FILE__) . '/ConfigFileReader.php';
include_once dirname(__FILE__) . '/config.php';

/**
 * Main controller of actions
 */
class ConfigEditor_Controller {	
	
	/**
	 * Get Viewer for displaying UI
	 */
	protected function getViewer() {
		return new ConfigEditor_Viewer();
	}
	
	/**
	 * Get Configuration file reader
	 */
	protected function getReader() {
		global $__ConfigEditor_Config;		
		$configFile = $__ConfigEditor_Config['edit.filepath'];
		if (file_exists($configFile)) {
			if (is_writeable($configFile)) {
				return new ConfigFileReader(
					$configFile, 
					$__ConfigEditor_Config['allow.editing.variables'], // What variables to view
					$__ConfigEditor_Config['allow.editing.variables']  // What variables to edit
				);
			} else {
				return null;
			}
		}
		return false;
	}
	
	/**
	 * Perform logged in user check and allow only administrators
	 */
	protected function authCheck() {
		global $current_user;
		if (is_admin($current_user)) return;
		
		$viewer = $this->getViewer();
		$viewer->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
		exit;
	}
	
	/**
	 * Core processing method
	 */
	function process(ConfigEditor_Request $request) {
		$this->authCheck();
		$type = $request->get('type');		
		if ($type == 'save') {
			$this->processSave($request);
		} else {
			$this->processDefault($request);
		}
	}
	
	/**
	 * Default action
	 */
	protected function processDefault($request) {
		global $currentModule;
		
		$configReader = $this->getReader();
		$viewer = $this->getViewer();

		if (is_null($configReader)) {
			$viewer->assign('WARNING', 'Configuration file is not writeable!');
		} else if ($configReader === false) {
			$viewer->assign('WARNING', 'Configuration file not found!');
		} else {
			$viewer->assign('CONFIGREADER', $configReader);
		}
		$viewer->display(vtlib_getModuleTemplate($currentModule, 'index.tpl'));
	}
	
	/**
	 * Save action
	 */
	protected function processSave($request) {
		$configReader = $this->getReader();

		if ($configReader) {
			$reqvalues = $request->values();
			foreach($reqvalues as $k => $v) {
				if (preg_match("/key_([^ ]+)/", $k, $m)) {
					$configReader->setVariableValue($m[1], $v);
				}
			}
			$configReader->save();
		}
		header('Location: index.php?module=ConfigEditor&action=index');
	}
}

$controller = new ConfigEditor_Controller();
$controller->process(new ConfigEditor_Request($_REQUEST));

?>