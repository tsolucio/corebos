{*<!--
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************//
-->*}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
{literal}
var searchConditions = [
	{"groupid":"1",
	 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
	 "comparator":"e",
	 "value":"Record Access Control",
	 "columncondition":"and"},
	{"groupid":"1",
	 "columnname":"vtiger_cbmap:targetname:targetname:cbMap_Target_Module:V",
	 "comparator":"e",
	 "value":moduleName,
	 "columncondition":""}
];
var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
var SpecialSearch = encodeURI(advSearch);
{/literal}
</script>
<h2>{'LBL_SELECT'|@getTranslatedString}</h2>
<input id="bmapid" name="bmapid" type="hidden" value="{$task->bmapid}">
<input id="bmapid_display" name="bmapid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$task->bmapid_display}">&nbsp;
<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="1" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}"
 onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=new_task&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch,'vtlibui10wf','width=680,height=602,resizable=0,scrollbars=0,top=150,left=200');" style="cursor:hand;cursor:pointer" align="absmiddle">&nbsp;
<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
