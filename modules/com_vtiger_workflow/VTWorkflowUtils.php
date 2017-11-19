<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//A collection of util functions for the workflow module

class VTWorkflowUtils {
	public static $userStack;
	public static $loggedInUser;

	public function __construct() {
		global $current_user;
		if (empty(self::$userStack)) {
			self::$userStack = array();
		}
	}

	/**
	 * Check whether the given identifier is valid.
	 */
	public static function validIdentifier($identifier) {
		if (is_string($identifier)) {
			return preg_match('/^[a-zA-Z][a-zA-Z_0-9]+$/', $identifier);
		} else {
			return false;
		}
	}

	/**
	 * Push the admin user on to the user stack
	 * and make it the $current_user
	 *
	 */
	public static function adminUser() {
		$user = Users::getActiveAdminUser();
		global $current_user;
		if (empty(self::$userStack) || count(self::$userStack) == 0) {
			self::$loggedInUser = $current_user;
		}
		self::$userStack[] = $current_user;
		$current_user = $user;
		return $user;
	}

	/**
	 * Push the logged in user on the user stack
	 * and make it the $current_user
	 */
	public static function loggedInUser() {
		$user = self::$loggedInUser;
		global $current_user;
		self::$userStack[] = $current_user;
		$current_user = $user;
		return $user;
	}

	/**
	 * Revert to the previous use on the user stack
	 */
	public static function revertUser() {
		global $current_user;
		if (count(self::$userStack) != 0) {
			$current_user = array_pop(self::$userStack);
		} else {
			$current_user = null;
		}
		return $current_user;
	}

	/**
	 * Get the previous user
	 */
	public static function previousUser() {
		if (count(self::$userStack)>0) {
			return self::$userStack[count(self::$userStack)-1];
		}
		return false;
	}

	/**
	 * The the webservice entity type of an EntityData object
	 */
	public static function toWSModuleName($entityData) {
		$moduleName = $entityData->getModuleName();
		if ($moduleName == 'Activity') {
			$arr = array('Task' => 'Calendar', 'Emails' => 'Emails');
			$moduleName = $arr[getActivityType($entityData->getId())];
			if ($moduleName == null) {
				$moduleName = 'Events';
			}
		}
		return $moduleName;
	}

	/**
	 * Insert redirection script
	 */
	public static function redirectTo($to, $message) {
?>
		<script type="text/javascript" charset="utf-8">
			window.location="<?php echo $to ?>";
		</script>
		<a href="<?php echo $to ?>"><?php echo $message ?></a>
<?php
	}

	/**
	 * Check if the current user is admin
	 */
	public static function checkAdminAccess() {
		global $current_user;
		return strtolower($current_user->is_admin) === 'on';
	}

	/* function to check if the module has workflow
	 * @params :: $modulename - name of the module
	 */
	public static function checkModuleWorkflow($modulename) {
		global $adb;
		$tabid = getTabid($modulename);
		$modules_not_supported = array('Calendar', 'Faq', 'Events', 'PBXManager', 'Users');
		$query = 'SELECT name FROM vtiger_tab WHERE name not in ('.generateQuestionMarks($modules_not_supported).') AND isentitytype=1 AND presence = 0 AND tabid = ?';
		$result = $adb->pquery($query, array($modules_not_supported, $tabid));
		$rows = $adb->num_rows($result);
		if ($rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public static function vtGetModules($adb) {
		$modules_not_supported = array('Calendar', 'Events', 'PBXManager');
		$sql = 'select distinct vtiger_field.tabid, name
			from vtiger_field
			inner join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid
			where vtiger_tab.name not in(' . generateQuestionMarks($modules_not_supported) . ') and vtiger_tab.isentitytype=1 and vtiger_tab.presence in (0,2)';
		$it = new SqlResultIterator($adb, $adb->pquery($sql, array($modules_not_supported)));
		$modules = array();
		foreach ($it as $row) {
			$modules[] = $row->name;
		}
		uasort($modules, function ($a, $b) {
			return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
		});
		return $modules;
	}

	public static function vtGetModulesAndExtensions($adb) {
		$modules_not_supported = array('Calendar', 'Events');
		$sql = 'select tabid, name
			from vtiger_tab
			where vtiger_tab.name not in(' . generateQuestionMarks($modules_not_supported) . ') and vtiger_tab.presence in (0,2)';
		$it = new SqlResultIterator($adb, $adb->pquery($sql, array($modules_not_supported)));
		$modules = array();
		foreach ($it as $row) {
			$modules[] = $row->name;
		}
		uasort($modules, function ($a, $b) {
			return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
		});
		return $modules;
	}
}
?>