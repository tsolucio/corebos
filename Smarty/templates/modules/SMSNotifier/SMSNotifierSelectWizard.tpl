{*<!--
/*+*******************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *
  *******************************************************************************/
-->*}

<div style="width: 400px;">

	<form method="POST" action="javascript:void(0);">

	<table width="100%" cellpadding="5" cellspacing="0" border="0" class="layerHeadingULine">
	<tr>
		<td class="genHeaderSmall" width="90%" align="left">Select Phone Numbers</td>
		<td width="10%" align="right">
			<a href="javascript:void(0);" onclick="SMSNotifierCommon.hideSelectWizard();"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"/></a>
		</td>
	</table>
	
	<table width="95%" cellpadding="5" cellspacing="0" border="0" align="center">
	<tr>
		<td>
		
			<table width="100%" cellpadding="5" cellspacing="0" border="0" align="center" bgcolor="white">
			<tr>
				<td align="left">Please select the number types to send the SMS<br/><br/>
				
				<div align="center" style="height: 120px; overflow-y: auto; overflow-x: hidden;">
					<table width="90%" cellpadding="5" cellspacing="0" border="0" align="left">
					
					{foreach key=_FIELDID item=_FIELDINFO from=$PHONEFIELDS}					
						{foreach key=_FIELDLABEL item=_FIELDNAME from=$_FIELDINFO}
						<tr>
							<td align="right" width="15%"><input type="checkbox" name="phonetype" value="{$_FIELDNAME}"/></td>
							<td align="left"><strong>{$_FIELDLABEL}</strong> {if $FIELDVALUES.$_FIELDNAME}<br/>{$FIELDVALUES[$_FIELDNAME]}{/if}</td>
						</tr>
						{/foreach}
					{/foreach}
										
					</table> 	
				</div>
				</td>			
			</tr>			
			</table>			
		
		</td>
	</tr>
	</table>
	
	<table width="100%" cellpadding="5" cellspacing="0" border="0" class="layerPopupTransport">
	<tr>
		<td class="small" align="center">
			<input type="hidden" name="idstring" value="{$IDSTRING}" />
            <input type="hidden" name="excludedRecords" value="{$excludedRecords}"/>
            <input type="hidden" name="viewid" value="{$VIEWID}"/>
			<input type="hidden" name="searchurl" value="{$SEARCHURL}"/>
			<input type="hidden" name="sourcemodule" value="{$SOURCEMODULE}" />
			<input type="button" class="small crmbutton create" onclick="SMSNotifierCommon.displayComposeWizard(this.form);" value="{$APP.LBL_SELECT_BUTTON_LABEL}"/>
			<input type="button" class="small crmbutton cancel" onclick="SMSNotifierCommon.hideSelectWizard();" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"/>
		</td>
	</tr>
	</table>
	
	</form>

</div>