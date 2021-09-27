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
<table border=0 cellspacing=0 cellpadding=5 width=100% align=center>
	<tr>
		<td class="small">
			<!-- popup specific content fill in starts -->
			<form name="EditView" id="massedit_form" action="index.php" onsubmit="VtigerJS_DialogBox.block();" method="POST">
				<input id="idstring" name="idstring" value="{$IDS}" type="hidden" />
				<table border=0 cellspacing=0 cellpadding=0 width=100% align=center bgcolor=white>
				<tr>
					<td colspan=4 valign="top">
						<div style='padding: 5px 0;'>
							<span class="helpmessagebox">{$APP.LBL_SELECT_FIELDS_TO_UDPATE_WITH_NEW_VALUE}</span>
						</div>
						<!-- Hidden Fields -->
						{include file='EditViewHidden.tpl'}
						<input type="hidden" name="massedit_recordids">
						<input type="hidden" name="massedit_module">
					</td>
				</tr>
					<tr>
						<td colspan=4 class="cblds-p_none">
							<table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
								<tbody><tr>
									<td class="dvtTabCache" style="width: 10px;" nowrap>&nbsp;</td>
									{foreach key=header name=block item=data from=$BLOCKS}
										{if $smarty.foreach.block.index eq 0}
											<td nowrap class="dvtSelectedCell" id="tab{$smarty.foreach.block.index}" onclick="massedit_togglediv({$smarty.foreach.block.index},{$BLOCKS|@count});">
											<b>{$header}</b>
											</td>
										{else}
											<td nowrap class="dvtUnSelectedCell" id="tab{$smarty.foreach.block.index}" onclick="massedit_togglediv({$smarty.foreach.block.index},{$BLOCKS|@count});">
											<b>{$header}</b>
											</td>
										{/if}
									{/foreach}
								<td class="dvtTabCache" nowrap style="width:55%;">&nbsp;</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan=4 class="cblds-p_none">
							{foreach key=header name=block item=data from=$BLOCKS}
								{if $smarty.foreach.block.index eq 0}
									<div id="massedit_div{$smarty.foreach.block.index}" style='display:block;'>
									<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
										{include file="DisplayFields.tpl"}
									</table>
									</div>
								{else}
									<div id="massedit_div{$smarty.foreach.block.index}" style='display:none;'>
									<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
										{include file="DisplayFields.tpl"}
									</table>
									</div>
								{/if}
							{/foreach}
						</td>
					</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
				<tr>
					<td align="center" class="cblds-t-align_center">
						<!--input type="submit" name="save" class="crmbutton small edit" value="{$APP.LBL_SAVE_LABEL}">
						<input type="button" name="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="fninvsh('massedit')"-->
						<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="run_massedit();" type="button" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
						<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="fninvsh('massedit')" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>

<script type="text/javascript" id="massedit_javascript">
	window.fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	window.fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	window.fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
	count=0;
<!-- vtlib customization: Help information assocaited with the fields -->
{if $FIELDHELPINFO}
	window.fieldhelpinfo = {literal}{}{/literal};
{foreach item=FIELDHELPVAL key=FIELDHELPKEY from=$FIELDHELPINFO}
	fieldhelpinfo["{$FIELDHELPKEY}"] = "{$FIELDHELPVAL}";
{/foreach}
{/if}
<!-- END -->
</script>
{if $FIELD_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="include/js/FieldDepFunc.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim} (new FieldDependencies({$FIELD_DEPENDENCY_DATASOURCE})).init() {rdelim});
	massedit_initOnChangeHandlers();
</script>
{/if}
