<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'include/Webservices/Utils.php';
include_once 'modules/Mobile/Mobile.php';
include_once dirname(__FILE__) . '/Utils.php';

class crmtogo_WS_Controller  {
	
	function requireLogin() {
		return true;
	}
	
	private $activeUser = false;
	public function initActiveUser($user) {
		$this->activeUser = $user;
	}
	
	protected function setActiveUser($user) {
		$this->sessionSet('_authenticated_user_id', $user->id);
		$this->initActiveUser($user);
	}
	
	protected function getActiveUser() {
		global $current_user;
		if($this->activeUser === false) {
			$userid = $this->sessionGet('_authenticated_user_id');
			if(!empty($userid)) {
				$this->activeUser = CRMEntity::getInstance('Users');
				$this->activeUser->retrieveCurrentUserInfoFromFile($userid);
				if ($this->activeUser->is_admin == 'on') {
					$this->activeUser->is_admin = true;
					$this->activeUser->column_fields['is_admin'] = true;
				}
			}
		}
		//needed for 5.x
		$current_user = $this->activeUser;
		return $this->activeUser;
	}
	
	function hasActiveUser() {
		$user = $this->getActiveUser();
		return ($user !== false);
	}
	
	function sessionGet($key, $defvaule = '') {
		return coreBOS_Session::get($key, $defvalue);
	}
	
	function sessionSet($key, $value) {
		coreBOS_Session::set($key, $value);
	}
	
	function getLanguage() {
		//cache language
		static $used_language = NULL;
		if(is_null($used_language)) {
			//check whether language file for crmtogo exists, if not switch en_us
			$lang = self::sessionGet('language');
			$basedir= realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../../../'); 
			$fileName = $basedir . "/languages/".$lang ."/crmtogo.php";
			if(file_exists($fileName) && (filesize($fileName) != 0)) {
				$used_language = $lang;
			}
			else {
				$used_language = 'en_us';
			}
		}
		return $used_language;
	}
	
	function getConfigDefaults() {
		//cache config information
		static $crmtogoDefaultsConfigCache = NULL;
		if (is_null($crmtogoDefaultsConfigCache)) {
			$crmtogoDefaultsConfigCache = crmtogo_WS_Utils::getConfigDefaults();
		}
		return $crmtogoDefaultsConfigCache;
	}
	
	function getUserConfigSettings() {

		//cache config information
		static $crmtogoConfigCache = NULL;
		if (is_null($crmtogoConfigCache)) {
			$crmtogoConfigCache = crmtogo_WS_Utils::getUserConfigSettings(self::sessionGet('_authenticated_user_id'));
		}
		return $crmtogoConfigCache;
	}

	function getUserConfigModuleSettings() {
		//cache config information
		static $crmtogoModuleConfigCache = NULL;
		if (is_null($crmtogoModuleConfigCache)) {
			$crmtogoModuleConfigCache = crmtogo_WS_Utils::getUserConfigModuleSettings(self::sessionGet('_authenticated_user_id'));
		}
		return $crmtogoModuleConfigCache;
	}
	
	function getConfigSettingsComments() {
		//cache config information
		static $crmtogoCommentsConfigCache = NULL;
		if (is_null($crmtogoCommentsConfigCache)) {
			$crmtogoCommentsConfigCache = crmtogo_WS_Utils::getConfigComments();
		}
		return $crmtogoCommentsConfigCache;
	}

	function getUserModule() {
		global $current_user,$current_language;
		if(empty($current_language))
			$current_language = self::sessionGet('language');
		//vtws_listtypes class is used to get permitted modules and set the language
		$listresult = vtws_listtypes(null,self::getActiveUser());
		$modulewsids = crmtogo_WS_Utils::getEntityModuleWSIds();
		unset($modulewsids['Users']);

		$userModule = self::getUserConfigModuleSettings();
		//maintain order and remove not permitted modules
		foreach($userModule as $modulename => $modno) {
			if (!in_array($modulename, $listresult['types'])) {
				unset($userModule[$modulename]);
			}
			else {
				$userModule[$modulename]['label']= $listresult['information'][$modulename]['label'];
				$userModule[$modulename]['id']  = $modulewsids[$modulename];
				$userModule[$modulename]['isEntity'] = $listresult['information'][$modulename]['isEntity'];
			}
		}
		return $userModule;
	}
	function getUsersLanguage() {
		//cache config information
		static $user_lang_strings = NULL;
		if (is_null($user_lang_strings)) {
			$user_lang_strings = crmtogo_WS_Utils::getUsersLanguage(self::sessionGet('language'));
		}
		return $user_lang_strings;
	}
	
}
