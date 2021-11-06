<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
include_once __DIR__ . '/../api/ws/ListModuleRecords.php';
include_once __DIR__ . '/models/SearchFilter.php';

class crmtogo_UI_GlobalSearch extends crmtogo_WS_ListModuleRecords {

	public function cachedModule($moduleName) {
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach ($modules as $module) {
			if ($module->name() == $moduleName) {
				return $module;
			}
		}
		return false;
	}

	/** For search capability */
	public function cachedSearchFields($module) {
		$cachekey = "_MODULE.{$module}.SEARCHFIELDS";
		return $this->sessionGet($cachekey, false);
	}

	public function getSearchFilterModel($module, $search) {
		$searchFilter = false;
		if (!empty($search)) {
			$criterias = array('search' => $search, 'fieldnames' => $this->cachedSearchFields($module));
			return crmtogo_UI_SearchFilterModel::modelWithCriterias($module, $criterias);
		}
		return $searchFilter;
	}

	public function process(crmtogo_API_Request $request) {
		$db = PearDatabase::getInstance();
		$displayed_modules= $this->getUserConfigModuleSettings();
		$searchmodule = array ();
		foreach ($displayed_modules as $modulename => $moduleconfig) {
			if ($moduleconfig['active'] == 1) {
				$searchmodule[] = $modulename;
			}
		}
		$wsResponse = parent::process($request);
		$response = false;
		if ($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$current_user = $this->getActiveUser();
			$response = false;
			$query_string = trim($request->get('query_string'));
			if (isset($query_string) && $query_string != '') {
				// limit search to modules enabled for crmtogo
				$search_onlyin = $request->get('search_onlyin');
				if (!empty($search_onlyin)) {
					$search_onlyin = explode(',', $search_onlyin);
					//prevent manipulations
					if (array_values($search_onlyin) != array_values($displayed_modules)) {
						//do standard search
						$search_onlyin = $displayed_modules;
					}
				} else {
					$search_onlyin = $searchmodule;
				}
				$object_array = getSearchModulesCommon($search_onlyin);
				$search_val = $query_string;
				$i = 0;
				$moduleRecordCount = array();
				foreach ($object_array as $module => $object_name) {
					checkFileAccessForInclusion("modules/$module/$module.php");
					require_once "modules/$module/$module.php";
					if (isPermitted($module, 'index') == 'yes') {
						$listquery = getListQuery($module);
						$oCustomView = '';
						$oCustomView = new CustomView($module);
						//Instead of getting current customview id, use cvid of All so that all entities will be found
						//$viewid = $oCustomView->getViewId($module);
						$cv_res = $db->pquery("select cvid from vtiger_customview where viewname='All' and entitytype=?", array($module));
						$viewid = $db->query_result($cv_res, 0, 'cvid');

						$listquery = $oCustomView->getModifiedCvListQuery($viewid, $listquery, $module);

						//This is for Global search
						$where = crmtogo_WS_Utils::getUnifiedWhere($listquery, $module, $search_val, $current_user);
						if ($where != '') {
							$listquery .= ' and ('.$where.')';
						}
						$count_result = $db->query($listquery);
						$noofrows = $db->num_rows($count_result);

						$moduleRecordCount[$module]['count'] = $noofrows;
						$listview_entries = $db->pquery($listquery, array());
						$entity='select id from vtiger_ws_entity where ismodule=1 and name=?';
						$ws_entity=$db->pquery($entity, array($module));
						$ws_entity2= $db->query_result($ws_entity, 0, 'id');

						$filde='select fieldname,entityidfield from vtiger_entityname where modulename=?';
						$ws_entity1=$db->pquery($filde, array($module));
						$fieldname= $db->query_result($ws_entity1, 0, 'fieldname');
						$entityidfield= $db->query_result($ws_entity1, 0, 'entityidfield');

						$searchresult = explode(',', $fieldname);
						$noofrows = $db->num_rows($listview_entries);
						if ($noofrows >0) {
							for ($i=0; $i<$noofrows; $i++) {
								$lstcontent[$module][$i]['entry1'] = $db->query_result($listview_entries, $i, $searchresult[0]);
								if (isset($searchresult[1])) {
									$lstcontent[$module][$i]['entry2'] = $db->query_result($listview_entries, $i, $searchresult[1]);
								} else {
									$lstcontent[$module][$i]['entry2'] = '';
								}
								$lstcontent[$module][$i]['id']= $ws_entity2."x".$db->query_result($listview_entries, $i, 'crmid');
							}
						} else {
							$lstcontent[$module][$i]['id']='';
							$lstcontent[$module][$i]['entry1']='';
							$lstcontent[$module][$i]['entry2']='';
						}
						//get translated module name
						$modullabel[$module] = $this -> cachedModule($module)->label();
						$i++;
					}
				}
			}

			//end search
			$config = $this->getUserConfigSettings();
			$viewer = new crmtogo_UI_Viewer();
			$viewer->assign('MOD', $this->getUsersLanguage());
			$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
			$viewer->assign('LANGUAGE', $this->sessionGet('language'));
			$viewer->assign('LISTENTITY', $lstcontent);
			$viewer->assign('MODLABEL', $modullabel);
			//Get PanelMenu data
			$modules = $this->sessionGet('_MODULES');
			$viewer->assign('_MODULES', $modules);
			$response = $viewer->process('GlobalSearch.tpl');
		}
		return $response;
	}
}
?>