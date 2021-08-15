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
<script>
	loadJS('modules/{$MODULE}/{$MODULE}.js');
</script>
{include file='QuickCreateHidden.tpl'}
<table border="0" align="center" cellspacing="0" cellpadding="0" width="90%" class="mailClient mailClientBg">
<tr>
<td>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class='small' style="cursor: move;">
	<tr>
		<td width="90%" class="mailSubHeader" background="{'qcBg.gif'|@vtiger_imageurl:$THEME}"><b >{$APP.LBL_CREATE_BUTTON_LABEL} {$QCMODULE}</b></td>
		<td nowrap class="mailSubHeader moduleName" align=right><i>{$APP.LBL_QUICK_CREATE}</i></td></tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
	<tr>
		<td>
			<!-- quick create UI starts -->
			<table border="0" cellspacing="0" cellpadding="5" width="100%" class="small" bgcolor="white" >
			{assign var="fromlink" value="qcreate"}
			{foreach item=subdata from=$QUICKCREATE}
				<tr>
					{foreach key=mainlabel item=maindata from=$subdata name=rowlayoutloop}
						{if count($maindata)>0}{include file='EditViewUI.tpl' rowiteration=$smarty.foreach.rowlayoutloop.iteration}{/if}
					{/foreach}
				</tr>
			{/foreach}
			</table>

		<!-- save cancel buttons -->
		<table border="0" cellspacing="0" cellpadding="5" width="100%" class=qcTransport>
			<tr>
				<td style="width:50%;text-align:right;"><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  "></td>
				<td style="width:50%;text-align:left;"><input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="hide('{if $FROM eq 'popup'}qcformpop{else}qcform{/if}');" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  "></td>
			</tr>
		</table>

		</td>
	</tr>
	</table>
</td>
</tr>
</table>
<SCRIPT id="qcvalidate">
	var qcfieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var qcfieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var qcfielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</SCRIPT>
</form>
