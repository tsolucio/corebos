<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/Login.php';
include_once dirname(__FILE__) . '/../../Mobile.Config.php';
 
class Mobile_WS_LoginAndFetchModules extends Mobile_WS_Login {
	
	function postProcess(Mobile_API_Response $response) {
		$current_user = $this->getActiveUser();
		
		if ($current_user) {
			$result = $response->getResult();
			$result['modules'] = $this->getListing($current_user);
			$response->setResult($result);
		}
	}
		
	function getListing($user) {
		function useSortBySettings($a, $b) {
			global $displayed_modules;
			$posA = $displayed_modules[$a['name']];
			$posB = $displayed_modules[$b['name']];
			if ($posA == $posB) {
				return 0;
			}
			return ($posA < $posB) ? -1 : 1;
		}
		//settings information
		global $displayed_modules, $current_language,$app_strings;
		$modulewsids = Mobile_WS_Utils::getEntityModuleWSIds();
		
		// Disallow modules
		unset($modulewsids['Users']);
		
		include_once dirname(__FILE__) . '/../../Mobile.Config.php';

		$CRM_Version = Mobile::config('crm_version');
		if ($CRM_Version!='5.2.1') {
			//we use this class only for privilege purposes on types
			$listresult = vtws_listtypes(null,$user, 'en_us');
		}
		else {
			$listresult = vtws_listtypes($user);
		}
		
		$listing = array();
		foreach($listresult['types'] as $index => $modulename) {
			if(!isset($modulewsids[$modulename])) {
				continue;
			}
			if ((in_array($modulename, $displayed_modules))) {
				$listing[] = array(
					'id'   => $modulewsids[$modulename],
					'name' => $modulename,
					'isEntity' => $listresult['information'][$modulename]['isEntity'],
					'label' => $app_strings[$modulename],
					'singular' => $app_strings['SINGLE_'.$modulename]
				);
			}
		}
		//make sure the active modules are displayed in the order of the $displayed_modules settings entry in MobileSettings.config.php
		$displayed_modules = array_flip($displayed_modules);
		usort($listing, 'useSortBySettings');
		return $listing;
	}
}
?>