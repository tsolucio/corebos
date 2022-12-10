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
<script type="text/javascript" src="modules/Settings/import_xml.js"></script>

<form action="" enctype="multipart/form-data" method="POST">
	<input type="hidden" id="category" name="category" value="{$CATEGORY}" />
	<table style="width:80%;margin-left:auto;margin-right:auto;margin-top:10px;" cellpadding="5" cellspacing="12" class="searchUIBasic" id="import-table">
		<tr>
			<td class="leftFormBorder1 cblds-m_x-large" width="60%" valign="top">
				<table width="100%" cellspacing="0" cellpadding="5">
					<tr>
						<td class="big" style="padding: 10px;">{$CATEGORY|@getTranslatedString:$CATEGORY}</td>
					</tr>
					<tr>
						<td style="padding: 10px;">
							<input type="file" name="import_file" id="import_file" class="small" size="60"/>
						</td>
					</tr>
					<tr>
						<td class="small" style="padding: 10px;">{'LBL_IMPORT_SUPPORTED_FILE_TYPE'|@getTranslatedString:$MODULE}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<button type="submit" style="float: right; color: #ffffff; font-weight: 500; margin: auto 2px;" class="slds-button slds-button_destructive cancel" onClick="window.history.back()">
					{'LBL_CANCEL_BUTTON'|@getTranslatedString:$MODULE}
				</button>
				<button type="submit" style="float: right; color: #ffffff; font-weight: 500; margin: auto 2px;" id="import" class="slds-button slds-button_success">
					{'LBL_IMPORT'|@getTranslatedString:$MODULE}
				</button>
			</td>
		</tr>
		<tr style="display: none;" id="message-sucess"></tr>
		<tr style="display: none;" id="message-warning"></tr>
	</table>
</form>