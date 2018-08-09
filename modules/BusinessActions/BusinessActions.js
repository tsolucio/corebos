/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function openBRMapInBA(fromlink, fldname, MODULE, ID) {
	var searchConditions = [
		{
			'groupid':'1',
			'columnname':'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
			'comparator':'e',
			'value':'Condition Expression',
			'columncondition':'or'
		},
		{
			'groupid':'1',
			'columnname':'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V',
			'comparator':'e',
			'value':'Condition Query',
			'columncondition':''
		}
	];
	var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
	var SpecialSearch = encodeURI(advSearch);
	window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=brmap&srcmodule=BusinessActons'+SpecialSearch, 'vtlibui10', 'width=680,height=602,resizable=0,scrollbars=0,top=150,left=200');
}