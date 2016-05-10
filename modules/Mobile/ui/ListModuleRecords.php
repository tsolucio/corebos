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
include_once dirname(__FILE__) . '/../api/ws/ListModuleRecords.php';
include_once dirname(__FILE__) . '/../api/ws/DeleteRecords.php';
include_once dirname(__FILE__) . '/models/SearchFilter.php';

class Mobile_UI_ListModuleRecords extends Mobile_WS_ListModuleRecords {
	var $current_language;
	function cachedModule($moduleName) {
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach($modules as $module) {
			if ($module->name() == $moduleName) return $module;
		}
		return false;
	}
	
	function getPagingModel(Mobile_API_Request $request) {
		$pagingModel =  Mobile_WS_PagingModel::modelWithPageStart($request->get('page'));
		$pagingModel->setLimit(Mobile::config('Navigation.Limit', 100));
		return $pagingModel;
	}
	
	/** For search capability */
	function cachedSearchFields($module) {
		$cachekey = "_MODULE.{$module}.SEARCHFIELDS";
		return $this->sessionGet($cachekey, false);
	}
	
	function getSearchFilterModel($module, $search) {
		$searchFilter = false;
		if (!empty($search)) {
			$criterias = array('search' => $search, 'fieldnames' => $this->cachedSearchFields($module));
			$searchFilter = Mobile_UI_SearchFilterModel::modelWithCriterias($module, $criterias);
			return $searchFilter;
		}
		return $searchFilter;
	}
	/** END */
	
	function process(Mobile_API_Request $request) {
		global $config_settings,$app_strings,$mod_strings;
		$wsResponse = parent::process($request);
		if (isset ($_REQUEST['delaction'])) {
			//delete?
			if ($_REQUEST['delaction']== 'deleteEntity') {
				$recordid = vtlib_purify($_REQUEST['record']);
				if (trim($recordid) !='') {
					//delete record
					$delResponse = Mobile_WS_DeleteRecords::process($request);
				}
			}
		}
		$current_user = $this->getActiveUser();
		$current_language = $this->sessionGet('language') ;
		include_once dirname(__FILE__) . '/../language/'.$current_language.'.lang.php';

		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} 
		else {
			$wsResponseResult = $wsResponse->getResult();
			$tabid = getTabid($wsResponseResult['module']);
          	$CATEGORY = getParentTabFromModule($wsResponseResult['module']);
			if ($wsResponseResult['module']!='Calendar' && $wsResponseResult['module']!='Events') {		
				$customView = new CustomView($wsResponseResult['module']);
				$id1=$_REQUEST['viewName'];
				$id2=$_REQUEST['view'];
				if($id2!=="" && $id2=='1') {
					$viewid=$id1;
				}
				else {
					$viewid = $customView->getViewId($wsResponseResult['module']);
				}
				$customview_html = $customView->getCustomViewCombo($viewid);
				$customview_html = str_replace("></option>",">".$mod_strings['LBL_FILTER']."</option>",$customview_html);
				
				$viewinfo = $customView->getCustomViewByCvid($viewid);
				
				$focus = new $wsResponseResult['module']();
				$focus->initSortbyField($wsResponseResult['module']);
				$order_by = $focus->getOrderBy();
				$url_string = '';
			}
			else {
				$calendarview_selected=$_REQUEST['viewName'];
				//week as default value
				if($calendarview_selected =="") {
					if (isset($config_settings ['calendarview']) and $config_settings ['calendarview']!='') {
						//get the default view from config
						$calendarview_selected=$config_settings ['calendarview'];
					}
					else {
						$calendarview_selected='week';
					}
				}
				$CATEGORY = getParentTabFromModule($wsResponseResult['module']);
				$customView = new CustomView($wsResponseResult['module']);
				$viewid = $customView->getViewId($wsResponseResult['module']);
				//special view for Calendar, custom filters are not considered
				$customview_arr = array('today' =>'LBL_TODAY','week' =>'LBL_WEEK','month' =>'LBL_MONTH','year' =>'LBL_YEAR');
				$customview_html ='';
				foreach ($customview_arr as $key =>$value) {
				    if ($key!=$calendarview_selected) {
						$customview_html .= "<option value=".$key.">".$mod_strings[$value]."</option>";
					}
					else {
						$customview_html .= "<option value=".$key." selected='selected'>".$mod_strings[$value]."</option>";
					}
				}
			}	
			$viewer = new Mobile_UI_Viewer();
			
			if($viewinfo['viewname'] == 'All' || $viewinfo['viewname'] == '' ) {
				$viewer->assign('_ALL', 'ALL');
			}
		    global $current_user,$adb,$list_max_entries_per_page;
			$current_user = $this->getActiveUser();	
			
			$viewer->assign('_MODULE', $this->cachedModule($wsResponseResult['module']) );
			$viewer->assign('_MODE', $request->get('mode'));
			$viewer->assign('_CATEGORY', $CATEGORY);
			$viewer->assign('_CUSTOMVIEW_OPTION', $customview_html);
			$viewer->assign('_PAGER', $this->getPagingModel($request));
			$viewer->assign('_SEARCH', $request->get('search'));
			$viewer->assign('MOD', $mod_strings);
			$viewer->assign('LANGUAGE', $current_language);
			$viewer->assign('_VIEW', $viewid);
			$viewer->assign('_VIEWNAME', $calendarview_selected);
			$viewer->assign('CALENDARSELECT', $config_settings ['compactcalendar']);
			$response = $viewer->process('generic/List.tpl');
		}
		return $response;
	}
}
?>