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
		$relmodulei18n = getTranslatedString('SINGLE_Users', 'Users');
		$relhandler = vtws_getModuleHandlerFromName('Users', $current_user);
		$relmeta = $relhandler->getMeta();
		$relfields = $relmeta->getModuleFields();
		foreach ($mainmeta->getOwnerFields() as $ownerfield) {
			$mainfieldi18n = getTranslatedString($ownerfield, 'BusinessActions');
			foreach ($relfields as $relfield => $wsfld) {
				$relfieldi18n = getTranslatedString($wsfld->getFieldLabelKey(), 'Users');
				$arr["$($ownerfield : (Users) $relfield)"] = "($mainfieldi18n : ($relmodulei18n) $relfieldi18n)";
			}
		}
		foreach ($referenceFieldInfoList as $mainfield => $relmodules) {
			if (empty($reflabels[$mainfield])) {
				continue;
			}
			$mainfieldi18n = getTranslatedString($reflabels[$mainfield], $moduleName);
			foreach ($relmodules as $relmodule) {
				$relmodulei18n = getTranslatedString('SINGLE_'.$relmodule, $relmodule);
				if ($relmodulei18n=='SINGLE_'.$relmodule) {
					$relmodulei18n = getTranslatedString($relmodule, $relmodule);
				}
				$relhandler = vtws_getModuleHandlerFromName($relmodule, $current_user);
				$relmeta = $relhandler->getMeta();
				$relfields = $relmeta->getModuleFields();
				foreach ($relfields as $relfield => $wsfld) {
					$relfieldi18n = getTranslatedString($wsfld->getFieldLabelKey(), $relmodule);
					$arr["$($mainfield : ($relmodule) $relfield)"] = "($mainfieldi18n : ($relmodulei18n) $relfieldi18n)";
				}
			}
		}
		return $arr;
	}

	public function expressionFunctionCategories() {
		return array(
			'All' => getTranslatedString('LBL_ALL'),
			'Math' => getTranslatedString('Math'),
			'Date and Time' => getTranslatedString('Date and Time'),
			'Text' => getTranslatedString('Text'),
			'Information' => getTranslatedString('Information'),
			'Logical' => getTranslatedString('Logical'),
			'Finance' => getTranslatedString('Finance'),
			'Application' => getTranslatedString('Application'),
			'Statistics' => getTranslatedString('Statistics'),
		);
	}

	public function expressionFunctionDetails() {
		global $current_language, $adb;
		$fname = 'modules/com_vtiger_workflow/language/'.$current_language.'.fndefs.php';
		if (file_exists($fname) && isInsideApplication($fname)) {
			include $fname;
		} else {
			include 'modules/com_vtiger_workflow/language/en_us.fndefs.php';
		}
		$wfexpfuncs = $adb->query('select expname,expinfo,funcdesc,funccategory,funcparam,funcexamples from com_vtiger_workflows_expfunctions');
		while ($func = $adb->fetch_array($wfexpfuncs)) {
			if (empty($WFExpressionFunctionDefinitons[$func['expname']])) {
				$WFExpressionFunctionDefinitons[$func['expname']] = array(
					'name' => $func['expinfo'],
					'desc' => $func['funcdesc'],
					'params' => json_decode(decode_html($func['funcparam']), true),
					'categories' => json_decode(decode_html($func['funccategory']), true),
					'examples' => json_decode(decode_html($func['funcexamples']), true),
				);
			}
		}
		asort($WFExpressionFunctionDefinitons);
		foreach ($WFExpressionFunctionDefinitons as $fn => $fdesc) {
			$WFExpressionFunctionDefinitons[$fn]['nameuc'] = strtoupper($WFExpressionFunctionDefinitons[$fn]['name']);
		}
		return $WFExpressionFunctionDefinitons;
	}

	public function expressionFunctions() {
		global $adb;
		$return = array(
			'concat' => 'concat(a,b,...)',
			'coalesce' => 'coalesce(a,b,...)',
			'time_diffdays(a,b)' => 'time_diffdays(a,b)',
			'time_diffdays(a)' => 'time_diffdays(a)',
			'time_diffyears(a,b)' => 'time_diffyears(a,b)',
			'time_diffyears(a)' => 'time_diffyears(a)',
			'time_diffweekdays(a,b)' => 'time_diffweekdays(a,b)',
			'time_diffweekdays(a)' => 'time_diffweekdays(a)',
			'holidaydifference' => 'holidaydifference(startDate, endDate, include_saturdays, holidays)',
			'networkdays' => 'networkdays(startDate, endDate, holidays)',
			'isholidaydate' => 'isholidaydate(date, saturdayisholiday, holidays)',
			'time_diff(a,b)' => 'time_diff(a,b)',
			'time_diff(a)' => 'time_diff(a)',
			'aggregate_time' => 'aggregate_time(relatedModuleName, relatedModuleField, conditions)',
			'add_days' => 'add_days(datefield, noofdays)',
			'add_workdays' => 'add_workdays(date, numofdays, addsaturday, holidays)',
			'sub_days' => 'sub_days(datefield, noofdays)',
			'sub_workdays' => 'sub_workdays(date, numofdays, removesaturday, holidays)',
			'add_months' => 'add_months(datefield, noofmonths)',
			'sub_months' => 'sub_months(datefield, noofmonths)',
			'add_time(timefield, minutes)' => 'add_time(timefield, minutes)',
			'sub_time(timefield, minutes)' => 'sub_time(timefield, minutes)',
			'today' => "get_date('today')",
			'today' => "get_date('now')",
			'tomorrow' => "get_date('tomorrow')",
			'yesterday' => "get_date('yesterday')",
			'time' => "get_date('time')",
			'format_date' => 'format_date(date,format)',
			'next_date' => "get_nextdate(startDate,days,holidays,include_weekend)",
			'next_date_laborable' => "get_nextdatelaborable(startDate,days,holidays,saturday_laborable)",
			'stringposition' => 'stringposition(haystack,needle)',
			'stringlength' => 'stringlength(string)',
			'stringreplace' => 'stringreplace(search,replace,subject)',
			'regexreplace' => 'regexreplace(pattern,replace,subject)',
			'power(base,exponential)' => 'power(base,exponential)',
			'substring' => 'substring(stringfield,start,length)',
			'randomstring' => 'randomstring(length)',
			'uppercase'=>'uppercase(stringfield)',
			'lowercase'=>'lowercase(stringfield)',
			'uppercasefirst'=>'uppercasefirst(stringfield)',
			'uppercasewords'=>'uppercasewords(stringfield)',
			'num2str' => 'num2str(number|field, language)',
			'number_format' => 'number_format(number, decimals, decimal_separator, thousands_separator)',
			'translate' => 'translate(string|field)',
			'round' => 'round(numericfield,decimals)',
			'ceil' => 'ceil(numericfield)',
			'floor' => 'floor(numericfield)',
			'modulo' => 'modulo(numericfield,numericfield)',
			'hash' => 'hash(field, method)',
			'globalvariable' => 'globalvariable(gvname)',
			'aggregation'=>'aggregation(operation,RelatedModule,relatedFieldToAggregate,conditions)',
			'aggregation_fields_operation'=>'aggregation_fields_operation(operation,RelatedModule,relatedFieldsToAggregateWithOperation,conditions)',
			'getCurrentUserID' => 'getCurrentUserID()',
			'getCurrentUserName' => 'getCurrentUserName({full})',
			'getCurrentUserField' => 'getCurrentUserField(fieldname)',
			'getCRMIDFromWSID' => 'getCRMIDFromWSID(id)',
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
			'getIDof' => 'getIDof(module, searchon, searchfor)',
			'getRelatedIDs' => 'getRelatedIDs(module, recordid)',
			'getRelatedMassCreateArray' => 'getRelatedMassCreateArray(module,recordid)',
			'getRelatedMassCreateArrayConverting' => 'getRelatedMassCreateArrayConverting(module, MainModuleDestination, RelatedModuleDestination, recordid)',
			'getRelatedRecordCreateArrayConverting' => 'getRelatedRecordCreateArrayConverting(module, RelatedModuleDestination, recordid)',
			'getISODate' => 'getISODate(year, week, dayOfWeek)',
			'getFieldsOF' => 'getFieldsOF(id, module, fields)',
			'getFromContext' => 'getFromContext(variablename)',
			'getFromContextSearching' => 'getFromContextSearching(variablename, searchon, searchfor, returnthis)',
			'setToContext' => 'setToContext(variablename, value)',
			'jsonEncode' => 'jsonEncode(field)',
			'jsonDecode' => 'jsonDecode(field)',
			'implode' => 'implode(delimiter, field)',
			'explode' => 'explode(delimiter, field)',
			'sendMessage' => 'sendMessage(message, channel, time)',
			'readMessage' => 'readMessage(channel)',
			'getSetting' => "getSetting('setting_key', 'default')",
			'setSetting' => "setSetting('setting_key', value)",
			'delSetting' => "delSetting('setting_key')",
			'evaluateRule' => 'evaluateRule(ruleID)',
			'executeSQL' => 'executeSQL(query, parameters...)',
			'getCRUDMode' => 'getCRUDMode()',
			'Importing' => 'Importing()',
			'isNumeric' => 'isNumeric(fieldname)',
			'isString' => 'isString(fieldname)',
			'OR' => 'OR(condition1, condition2)',
			'AND' => 'AND(condition1, condition2)',
			'NOT' => 'NOT(condition1)',
			'regex' => 'regex(pattern, subject)',
			'exists' => 'exists(fieldname, value)',
			'existsrelated' => 'existsrelated(relatedmodule, fieldname, value)',
			'allrelatedare' => 'allrelatedare(relatedmodule, fieldname, value)',
			'allrelatedarethesame' => 'allrelatedarethesame(relatedmodule, fieldname, value)',
			'min' => 'min(fieldname)',
			'max' => 'max(fieldname)',
			'average' => 'average(number,...)',
			'getCurrentConfiguredTaxValues' => 'getCurrentConfiguredTaxValues(taxname)',
			'getCurrencyConversionValue' => 'getCurrencyConversionValue(currency_code)',
		);
		$wfexpfuncs = $adb->query('select expname,expinfo from com_vtiger_workflows_expfunctions');
		while ($func = $adb->fetch_array($wfexpfuncs)) {
			$return[$func['expname']] = $func['expinfo'];
		}
		return $return;
	}
}
?>
