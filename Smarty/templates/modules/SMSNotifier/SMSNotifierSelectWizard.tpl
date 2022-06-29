{*<!--
/*+*******************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*******************************************************************************/
-->*}
<div style="width: 400px;" class=" sdls-card">
	<form method="POST" action="javascript:void(0);">
		<table width="100%" cellpadding="5" cellspacing="0" border="0" class="layerHeadingULine slds-table slds-table_bordered">
			<tr>
				<td class="genHeaderSmall" width="90%" align="left">{$MOD.SelectPhoneNumbers}</td>
				<td width="10%" align="right">
					<a href="javascript:void(0);" onclick="SMSNotifierCommon.hideSelectWizard();"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"/></a>
				</td>
		</table>

		<table width="95%" cellpadding="5" cellspacing="0" border="0" align="center" style="padding: 7px;">
			<tr>
				<td>
				<table width="100%" cellpadding="5" cellspacing="0" border="0" align="center" bgcolor="white">
					<tr>
						<td align="left">{$MOD.SelectNumberTypes}
						<br/>
						<br/>
						<div align="center" style="height: 120px; overflow-y: auto; overflow-x: hidden;">
							<table width="90%" cellpadding="5" cellspacing="0" border="0" align="left">

								{foreach key=_FIELDID item=_FIELDINFO from=$PHONEFIELDS}
								{foreach key=_FIELDLABEL item=_FIELDNAME from=$_FIELDINFO}
								<tr>
									<td align="right" width="15%">
										<div class="slds-form-element__control">
											<div class="slds-checkbox">
												<input type="checkbox" name="phonetype" id="{$_FIELDNAME}" value="{$_FIELDNAME}" />
												<label class="slds-checkbox__label" for="{$_FIELDNAME}">
													<span class="slds-checkbox_faux"></span>
												</label>
											</div>
										</div>
									</td>
									<td align="left"><strong>{$_FIELDLABEL}</strong> {if isset($FIELDVALUES.$_FIELDNAME)}
									<br/>
									{$FIELDVALUES[$_FIELDNAME]}{/if}</td>
								</tr>
								{/foreach}
								{/foreach}

							</table>
						</div></td>
					</tr>
				</table></td>
			</tr>
		</table>

		<table style="text-align: center; margin: auto; display: flex; justify-content: center; display: grid;" width="100%" cellpadding="5" cellspacing="0" border="0" class="layerPopupTransport slds-table slds-table_bordered">
			<tr>
				<td class="small" align="center">
				<input type="hidden" name="idstring" value="{$IDSTRING}" />
				<input type="hidden" name="excludedRecords" value="{$excludedRecords}"/>
				<input type="hidden" name="viewid" value="{$VIEWID}"/>
				<input type="hidden" name="searchurl" value="{$SEARCHURL}"/>
				<input type="hidden" name="sourcemodule" value="{$SOURCEMODULE}" />
				<input type="button" class="slds-button slds-button_brand small crmbutton create" onclick="SMSNotifierCommon.displayComposeWizard(this.form);" value="{$APP.LBL_SELECT_BUTTON_LABEL}"/>
				<input type="button" class="slds-button slds-button_destructive small crmbutton cancel" onclick="SMSNotifierCommon.hideSelectWizard();" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"/>
				</td>
			</tr>
		</table>
	</form>
</div>