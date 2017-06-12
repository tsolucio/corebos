<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
require_once("include/events/SqlResultIterator.inc");
class VTExpressionsManager{
	function __construct($adb){
		$this->adb = $adb;
	}

	/** Caching logic **/
	private static $cache = array();
	static function addToCache($key, $value) {
		self::$cache[$key] = $value;
	}
	static function fromCache($key) {
		if(isset(self::$cache[$key])) return self::$cache[$key];
		return false;
	}
	static function clearCache() {
		self::$cache = array();
	}

	function fields($moduleName){
		global $current_user;
		$result = vtws_describe($moduleName, $current_user);
		$fields = $result['fields'];
		$arr = array();
		foreach($fields as $field){
			$arr[$field['name']] = $field['label'];
		}
		return $arr;
	}

	function expressionFunctions() {
		global $adb;
		$return = array('concat' => 'concat(a,b)', 'time_diffdays(a,b)' => 'time_diffdays(a,b)', 'time_diffdays(a)' => 'time_diffdays(a)', 'time_diff(a,b)' => 'time_diff(a,b)','time_diff(a)' => 'time_diff(a)',
			'add_days' => 'add_days(datefield, noofdays)', 'sub_days' => 'sub_days(datefield, noofdays)', 'add_time(timefield, minutes)' => 'add_time(timefield, minutes)', 'sub_time(timefield, minutes)' => 'sub_time(timefield, minutes)',
			'today' => "get_date('today')", 'tomorrow' => "get_date('tomorrow')", 'yesterday' => "get_date('yesterday')",'time' => "get_date('time')", 'format_date' => 'format_date(date,format)', 'substring' => 'substring(stringfield,start,end)',
			'uppercase'=>'uppercase(stringfield)', 'lowercase'=>'lowercase(stringfield)', 'uppercasefirst'=>'uppercasefirst(stringfield)', 'uppercasewords'=>'uppercasewords(stringfield)', 'round' => 'round(numericfield,decimals)',
			'ceil' => 'ceil(numericfield)', 'floor' => 'floor(numericfield)',
			'getCurrentUserID' => 'getCurrentUserID()','getCurrentUserName' => 'getCurrentUserName({full})',
			'getLatitude' => 'getLatitude(address)', 'getLongitude' => 'getLongitude(address)',
			'getGEODistance' => 'getGEODistance(address_from,address_to)',
			'getGEODistanceFromCompanyAddress' => 'getGEODistanceFromCompanyAddress(address)',
			'getGEODistanceFromUserAddress' => 'getGEODistanceFromUserAddress(address)',
			'getGEODistanceFromUser2AccountBilling' => 'getGEODistanceFromUser2AccountBilling(account,address_specification)',
			'getGEODistanceFromAssignUser2AccountBilling' => 'getGEODistanceFromAssignUser2AccountBilling(account,assigned_user,address_specification)',
			'getGEODistanceFromUser2AccountShipping' => 'getGEODistanceFromUser2AccountShipping(account,address_specification)',
			'getGEODistanceFromAssignUser2AccountShipping' => 'getGEODistanceFromAssignUser2AccountShipping(account,assigned_user,address_specification)',
			'getGEODistanceFromUser2ContactBilling' => 'getGEODistanceFromUser2ContactBilling(contact,address_specification)',
			'getGEODistanceFromAssignUser2ContactBilling' => 'getGEODistanceFromAssignUser2ContactBilling(contact,assigned_user,address_specification)',
			'getGEODistanceFromUser2ContactShipping' => 'getGEODistanceFromUser2ContactShipping(contact,address_specification)',
			'getGEODistanceFromAssignUser2ContactShipping' => 'getGEODistanceFromAssignUser2ContactShipping(contact,assigned_user,address_specification)',
			'getGEODistanceFromCoordinates' => 'getGEODistanceFromCoordinates({lat1},{long1},{lat2},{long2})');
		$wfexpfuncs = $adb->query('select expname,expinfo from com_vtiger_workflows_expfunctions');
		while ($func = $adb->fetch_array($wfexpfuncs)) {
			$return[$func['expname']] = $func['expinfo'];
		}
		return $return;
	}
}

?>