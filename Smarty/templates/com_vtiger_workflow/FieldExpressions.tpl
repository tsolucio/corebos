{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}

<div id='editpopup' class='layerPopup' style='display:none;'>
	<div id='editpopup_draghandle' style='cursor: move;'>
		<table class="slds-table slds-no-row-hover" width="100%" style="border-bottom: 1px solid #d4d4d4;">
			<tr class="slds-text-title--header">
				<th scope="col">
					<div class="slds-truncate moduleName">
						<b>{$MOD.LBL_SET_VALUE}</b>
					</div>
				</th>
				<th scope="col" style="padding: .5rem;text-align: right;">
					<div class="slds-truncate">
						<a href="javascript:;">
							<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
						</a>
					</div>
				</th>
			</tr>
		</table>
	</div>
	<!-- Raw, fieldname and expression select box -->
	<table width="100%" bgcolor="white" align="center" border="0" cellspacing="0" cellpadding="5">
		<tr valign="top">
			<td class='dvtCellInfo' align="left">
				<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
					<tr valign="top">
						<td>
							<!-- Expression type select -->
							<select id='editpopup_expression_type' class='slds-select'>
								<option value="rawtext">{$MOD.LBL_RAW_TEXT}</option>
								<option value="fieldname">{$MOD.LBL_FIELD}</option>
								<option value="expression">{$MOD.LBL_EXPRESSION}</option>
							</select>
							<br/>
							<!-- Field value type select -->
							<select id='editpopup_fieldnames' class='slds-select' style="">
								<option value="">{$MOD.LBL_USE_FIELD_VALUE_DASHDASH}</option>
							</select>
							<br/>
							<!-- Functions type select -->
							<select id='editpopup_functions' class='slds-select' style="">
								<option value="">{$MOD.LBL_USE_FUNCTION_DASHDASH}</option>
							</select>
						</td>
					</tr>
					<!-- Expression textarea -->
					<tr valign="top">
						<td>
							<input type="hidden" id='editpopup_field' />
							<input type="hidden" id='editpopup_field_type' />
							<textarea name="Name" class="slds-textarea" rows="10" cols="50" id='editpopup_expression'></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<!-- Save and Cancel buttons -->
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport" style="background-color: #f4f6f9;">
		<tr>
			<td align="center" style="padding: 5px;">
				<input type="button" class="slds-button_success slds-button slds-button--small" value="{$APP.LBL_SAVE_BUTTON_LABEL}" name="save" id='editpopup_save'/>
				<input type="button" class="slds-button--small slds-button slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" id='editpopup_cancel'/>
			</td>
		</tr>
	</table>

	<!-- Raw text help -->
	<div class="helpmessagebox" id="text_help" style="display:none;">
		<table width="100%" cellspacing="1" cellpadding="5" border="0">
			<tr valign="top">
				<td><b>{$MOD.LBL_RAW_TEXT}</b></td>
			</tr>
			<tr valign="top">
				<td>2000</td>
			</tr>
			<tr valign="top">
				<td>any text</td>
			</tr>
		</table>
	</div>

	<!-- Fieldname help text -->
	<div class="helpmessagebox" id="fieldname_help" style="display:none;">
		<table width="100%" cellspacing="1" cellpadding="5" border="0">
			<tr valign="top">
				<td><b>{$MOD.LBL_FIELD}</b></td>
			</tr>
			<tr valign="top">
				<td><i>annual_revenue</i></td>
			</tr>
			<tr valign="top">
				<td><i>notify_owner</i></td>
			</tr>
		</table>
	</div>
	<!-- Expression help text -->
	<div class="helpmessagebox" id="expression_help" style="display:none;">
		<table width="100%" cellspacing="1" cellpadding="5" border="0">
			<tr valign="top">
				<td><b>{$MOD.LBL_EXPRESSION}</b></td>
			</tr>
			<tr valign="top">
				<td><i>annual_revenue</i> / 12</td>
			</tr>
			<tr valign="top">
				<td>
					<font color=blue>if</font> <i>mailingcountry</i> == 'India' <font color=blue>then</font> <font color=blue>concat</font>(<i>firstname</i>,' ',<i>lastname</i>) <font color=blue>else</font> <font color=blue>concat</font>(<i>lastname</i>,' ',<i>firstname</i>) <font color=blue>end</font>
				</td>
			</tr>
		</table>
	</div>
</div>