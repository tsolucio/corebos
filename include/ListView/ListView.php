<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/logging.php';
require_once 'include/ListView/ListViewSession.php';

class ListView {
	public $local_theme = null;
	public $local_app_strings= null;
	public $local_image_path = null;
	public $local_current_module = null;
	public $local_mod_strings = null;
	public $records_per_page = 20;
	public $xTemplate = null;
	public $xTemplatePath = null;
	public $seed_data = null;
	public $query_where = null;
	public $query_limit = -1;
	public $query_orderby = null;
	public $header_title = '';
	public $header_text = '';
	public $log = null;
	public $initialized = false;
	public $querey_where_has_changed = false;
	public $display_header_and_footer = true;

	public function __construct() {
		global $log;
		$log->debug('> ListView');
		if (!$this->initialized) {
			global $theme, $app_strings, $image_path, $currentModule;
			$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
			$this->records_per_page = $list_max_entries_per_page + 0;
			$this->initialized = true;
			$this->local_theme = $theme;
			$this->local_app_strings = $app_strings;
			$this->local_image_path = $image_path;
			$this->local_current_module = $currentModule;

			if (empty($this->local_image_path)) {
				$this->local_image_path = 'themes/'.$theme.'/images';
			}
			$this->log = LoggerManager::getLogger('listView_'.$this->local_current_module);
			$log->debug('< ListView');
		}
	}

	/** sets the header title */
	public function setHeaderTitle($value) {
		global $log;
		$log->debug('> setHeaderTitle '.$value);
		$this->header_title = $value;
		$log->debug('< setHeaderTitle');
	}

	/** sets the header text this is text thats appended to the header vtiger_table and is usually used for the creation of buttons */
	public function setHeaderText($value) {
		global $log;
		$log->debug('> setHeaderText '.$value);
		$this->header_text = $value;
		$log->debug('< setHeaderText');
	}

	/**	sets the parameters dealing with the db */
	public function setQuery($where, $limit, $orderBy, $varName, $allowOrderByOveride = true) {
		global $log;
		$log->debug('> setQuery '.$where.','.$limit.','.$orderBy.','.$varName.','.$allowOrderByOveride);
		$this->query_where = $where;
		if ($this->getSessionVariable('query', 'where') != $where) {
			$this->querey_where_has_changed = true;
			$this->setSessionVariable('query', 'where', $where);
		}
		$this->query_limit = $limit;
		if (!$allowOrderByOveride) {
			$this->query_orderby = $orderBy;
			$log->debug('< setQuery');
			return;
		}
		$sortBy = $this->getSessionVariable($varName, 'ORDER_BY') ;

		if (empty($sortBy)) {
			$this->setUserVariable($varName, 'ORDER_BY', $orderBy);
			$sortBy = $orderBy;
		} else {
			$this->setUserVariable($varName, 'ORDER_BY', $sortBy);
		}
		if ($sortBy == 'amount') {
			$sortBy = 'amount*1';
		}

		$desc = false;
		$desc = $this->getSessionVariable($varName, $sortBy.'_desc');

		if (empty($desc)) {
			$desc = false;
		}
		if (isset($_REQUEST[$this->getSessionVariableName($varName, 'ORDER_BY')])) {
			$last = $this->getSessionVariable($varName, 'ORDER_BY_LAST');
		}
		if (!empty($last) && $last == $sortBy) {
			$desc = !$desc;
		} else {
			$this->setSessionVariable($varName, 'ORDER_BY_LAST', $sortBy);
		}
		$this->setSessionVariable($varName, $sortBy.'_desc', $desc);
		if (!empty($sortBy)) {
			if (substr_count(strtolower($sortBy), ' desc') == 0 && substr_count(strtolower($sortBy), ' asc') == 0) {
				if ($desc) {
					$this->query_orderby = $sortBy.' desc';
				} else {
					$this->query_orderby = $sortBy.' asc';
				}
			} else {
				$this->query_orderby = $sortBy;
			}
		} else {
			$this->query_orderby = '';
		}
		$log->debug('< setQuery');
	}

	/** sets the theme used only use if it is different from the global */
	public function setTheme($theme) {
		global $log;
		$log->debug('> setTheme '.$theme);
		$this->local_theme = $theme;
		if (isset($this->xTemplate)) {
			$this->xTemplate->assign('THEME', $this->local_theme);
		}
		$log->debug('< setTheme');
	}

	/** sets the AppStrings used only use if it is different from the global */
	public function setAppStrings(&$app_strings) {
		global $log;
		$log->debug('> setAppStrings');
		unset($this->local_app_strings);
		$this->local_app_strings = $app_strings;
		if (isset($this->xTemplate)) {
			$this->xTemplate->assign('APP', $this->local_app_strings);
		}
		$log->debug('< setAppStrings');
	}

	/** sets the ModStrings used */
	public function setModStrings(&$mod_strings) {
		global $log;
		$log->debug('> setModStrings');
		unset($this->local_module_strings);
		$this->local_mod_strings = $mod_strings;
		if (isset($this->xTemplate)) {
			$this->xTemplate->assign('MOD', $this->local_mod_strings);
		}
		$log->debug('< setModStrings');
	}

	/** sets the ImagePath used */
	public function setImagePath($image_path) {
		global $log;
		$log->debug('> setImagePath '.$image_path);
		$this->local_image_path = $image_path;
		if (empty($this->local_image_path)) {
			$this->local_image_path = 'themes/'.$this->local_theme.'/images';
		}
		if (isset($this->xTemplate)) {
			$this->xTemplate->assign('IMAGE_PATH', $this->local_image_path);
		}
		$log->debug('< setImagePath');
	}

	/** sets the currentModule only use if this is different from the global */
	public function setCurrentModule($currentModule) {
		global $log;
		$log->debug('> setCurrentModule '.$currentModule);
		unset($this->local_current_module);
		$this->local_current_module = $currentModule;
		$this->log = LoggerManager::getLogger('listView_'.$this->local_current_module);
		if (isset($this->xTemplate)) {
			$this->xTemplate->assign('MODULE_NAME', $this->local_current_module);
		}
		$log->debug('< setCurrentModule');
	}

	/** sets a session variable */
	public function setSessionVariable($localVarName, $varName, $value) {
		global $log;
		$log->debug('> setSessionVariable '.$localVarName.','.$varName.','. $value);
		coreBOS_Session::set($this->local_current_module.'_'.$localVarName.'_'.$varName, $value);
		$log->debug('< setSessionVariable');
	}

	public function setUserVariable($localVarName, $varName, $value) {
		global $log, $current_user;
		$log->debug('> setUserVariable '.$localVarName.','.$varName.','.$value);
		$current_user->setPreference($this->local_current_module.'_'.$localVarName.'_'.$varName, $value);
		$log->debug('< setUserVariable');
	}

	/** returns a session variable first checking the querey for it then checking the session */
	public function getSessionVariable($localVarName, $varName) {
		global $log;
		$log->debug('> getSessionVariable '.$localVarName.','.$varName);
		if (isset($_REQUEST[$this->getSessionVariableName($localVarName, $varName)])) {
			$this->setSessionVariable($localVarName, $varName, vtlib_purify($_REQUEST[$this->getSessionVariableName($localVarName, $varName)]));
		}
		if (isset($_SESSION[$this->getSessionVariableName($localVarName, $varName)])) {
			$log->debug('< getSessionVariable');
			return coreBOS_Session::get($this->getSessionVariableName($localVarName, $varName));
		}
		$log->debug('< getSessionVariable');
		return '';
	}

	/**
	 * @return void
	 * @param unknown $localVarName
	 * @param unknown $varName
	 * @desc returns the session/query variable name
	 */
	public function getSessionVariableName($localVarName, $varName) {
		global $log;
		$log->debug('>< getSessionVariableName '.$localVarName.','.$varName);
		return $this->local_current_module.'_'.$localVarName.'_'.$varName;
	}
}
?>