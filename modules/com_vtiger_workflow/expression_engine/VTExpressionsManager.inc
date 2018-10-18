<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
require_once 'include/events/SqlResultIterator.inc';

class VTExpressionsManager {

	public function __construct($adb) {
		$this->adb = $adb;
	}

	/** Caching logic **/
	private static $cache = array();

	private static function addToCache($key, $value) {
		self::$cache[$key] = $value;
	}

	private static function fromCache($key) {
		if (isset(self::$cache[$key])) {
			return self::$cache[$key];
		}
		return false;
	}

	private static function clearCache() {
		self::$cache = array();
	}

	public function fields($moduleName) {
		global $current_user;
		$mainhandler = vtws_getModuleHandlerFromName($moduleName, $current_user);
		$mainmeta = $mainhandler->getMeta();
		$referenceFieldInfoList = $mainmeta->getReferenceFieldDetails();
		$reffields = array_keys($referenceFieldInfoList);
		$result = vtws_describe($moduleName, $current_user);
		$fields = $result['fields'];
		$arr = $reflabels = array();
		foreach ($fields as $field) {
			$arr[$field['name']] = $field['label'];
			if (in_array($field['name'], $reffields)) {
				$reflabels[$field['name']] = $field['label'];
			}
		}
		foreach ($referenceFieldInfoList as $mainfield => $relmodules) {
			if (empty($reflabels[$mainfield])) {
				continue;
			}
			$mainfieldi18n = getTranslatedCurrencyString($reflabels[$mainfield], $moduleName);
			foreach ($relmodules as $relmodule) {
				$relmodulei18n = getTranslatedCurrencyString($relmodule, $relmodule);
				$relhandler = vtws_getModuleHandlerFromName($relmodule, $current_user);
				$relmeta = $relhandler->getMeta();
				$relfields = $relmeta->getModuleFields();
				foreach ($relfields as $relfield => $wsfld) {
					$relfieldi18n = getTranslatedCurrencyString($wsfld->getFieldLabelKey(), $relmodule);
					$arr["$($mainfield : ($relmodule) $relfield)"] = "($mainfieldi18n : ($relmodulei18n) $relfieldi18n)";
				}
			}
		}
		return $arr;
	}

	public function expressionFunctions() {
		global $adb;
		$return = array(
			'concat' => 'concat(a,b)',
			'coalesce' => 'coalesce(a,...,n)',
			'time_diffdays(a,b)' => 'time_diffdays(a,b)',
			'time_diffdays(a)' => 'time_diffdays(a)',
			'time_diffyears(a,b)' => 'time_diffyears(a,b)',
			'time_diffyears(a)' => 'time_diffyears(a)',
			'time_diffweekdays(a,b)' => 'time_diffweekdays(a,b)',
			'time_diffweekdays(a)' => 'time_diffweekdays(a)',
			'time_diff(a,b)' => 'time_diff(a,b)',
			'time_diff(a)' => 'time_diff(a)',
			'aggregate_time' => 'aggregate_time(relatedModuleName, relatedModuleField, conditions)',
			'add_days' => 'add_days(datefield, noofdays)',
			'sub_days' => 'sub_days(datefield, noofdays)',
			'add_months' => 'add_months(datefield, noofmonths)',
			'sub_months' => 'sub_months(datefield, noofmonths)',
			'add_time(timefield, minutes)' => 'add_time(timefield, minutes)',
			'sub_time(timefield, minutes)' => 'sub_time(timefield, minutes)',
			'today' => "get_date('today')",
			'tomorrow' => "get_date('tomorrow')",
			'yesterday' => "get_date('yesterday')",
			'time' => "get_date('time')",
			'format_date' => 'format_date(date,format)',
			'next_date' => "get_nextdate(startDate,days,holidays,include_weekend)",
			'next_date_laborable' => "get_nextdatelaborable(startDate,days,holidays,saturday_laborable)",
			'stringposition' => 'stringposition(haystack,needle)',
			'stringlength' => 'stringlength(string)',
			'stringreplace' => 'stringreplace(search,replace,subject)',
			'power(base,exponential)' => 'power(base,exponential)',
			'substring' => 'substring(stringfield,start,end)',
			'uppercase'=>'uppercase(stringfield)',
			'lowercase'=>'lowercase(stringfield)',
			'uppercasefirst'=>'uppercasefirst(stringfield)',
			'uppercasewords'=>'uppercasewords(stringfield)',
			'num2str' => 'num2str(number|field, language)',
			'translate' => 'translate(string|field)',
			'round' => 'round(numericfield,decimals)',
			'ceil' => 'ceil(numericfield)',
			'floor' => 'floor(numericfield)',
			'modulo' => 'modulo(numericfield)',
			'hash' => 'hash(field, method)',
			'globalvariable' => 'globalvariable(gvname)',
			'aggregation'=>'aggregation(operation,RelatedModule,relatedFieldToAggregate,conditions)',
			'aggregation_fields_operation'=>'aggregation_fields_operation(operation,RelatedModule,relatedFieldsToAggregateWithOperation,conditions)',
			'getCurrentUserID' => 'getCurrentUserID()',
			'getCurrentUserName' => 'getCurrentUserName({full})',
			'getCurrentUserField' => 'getCurrentUserField(fieldname)',
			'getEntityType'=>'getEntityType(field)',
			'getimageurl'=>'getimageurl(field)',
			'getLatitude' => 'getLatitude(address)',
			'getLongitude' => 'getLongitude(address)',
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
			'getGEODistanceFromCoordinates' => 'getGEODistanceFromCoordinates({lat1},{long1},{lat2},{long2})',
		);
		$wfexpfuncs = $adb->query('select expname,expinfo from com_vtiger_workflows_expfunctions');
		while ($func = $adb->fetch_array($wfexpfuncs)) {
			$return[$func['expname']] = $func['expinfo'];
		}
		return $return;
	}
}
?>