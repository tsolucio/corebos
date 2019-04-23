{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<link rel="stylesheet" type="text/css" href="themes/{$THEME}/style.css">
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/js/vtlib.js"></script>
<script type="text/javascript" src="modules/Import/resources/Import.js"></script>

<div id="status" style="position:absolute;display:none;left:850px;top:15px;height:27px;white-space:nowrap;">
	<img src="{'status.gif'|@vtiger_imageurl:$THEME}">
</div>
<form onsubmit="VtigerJS_DialogBox.block();" action="index.php" enctype="multipart/form-data" method="POST" name="importBasic">
	<input type="hidden" name="module" value="{$FOR_MODULE}" />
	<input type="hidden" name="action" value="Import" />
	<input type="hidden" name="mode" value="upload_and_parse" />
	<table cellpadding="5" cellspacing="12" class="searchUIBasic">
		<tr>
			<td class="heading2" align="left" colspan="2">
				{'LBL_IMPORT'|@getTranslatedString:$MODULE} {$FOR_MODULE|@getTranslatedString:$FOR_MODULE} - {'LBL_LAST_IMPORTED_RECORDS'|@getTranslatedString:$MODULE}
			</td>
		</tr>
		{if !empty($ERROR_MESSAGE)}
		<tr>
			<td class="style1" align="left" colspan="2">
				{$ERROR_MESSAGE}
			</td>
		</tr>
		{/if}
		<tr>
			<td class="leftFormBorder1" width="60%" valign="top">
				<div id="import_listview_contents" class="small">
				{include file='modules/Import/ListViewEntries.tpl'}
				</div>
			</td>
		</tr>
	</table>
</form>