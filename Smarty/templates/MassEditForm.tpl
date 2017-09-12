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
				<input id="idstring" value="{$IDS}" type="hidden" />
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
						<td colspan=4>
							<div class="slds-table--scoped">
								<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0; border-bottom: 0;">
									{foreach key=header name=block item=data from=$BLOCKS}
										{if $smarty.foreach.block.index eq 0}
											<li class="slds-tabs--scoped__item active" id="tab{$smarty.foreach.block.index}" onClick="massedit_togglediv({$smarty.foreach.block.index},{$BLOCKS|@count});" role="presentation" style="border-top-left-radius: .25rem;">
												<a href="javascript:doNothing()" class="slds-tabs--scoped__link " role="tab" tabindex="0" aria-selected="true"><b>{$header}</b></a>
											</li>
										{else}
											<li class="slds-tabs--scoped__item" id="tab{$smarty.foreach.block.index}" onClick="massedit_togglediv({$smarty.foreach.block.index},{$BLOCKS|@count});" role="presentation">
												<a href="javascript:doNothing()" class="slds-tabs--scoped__link" role="tab" tabindex="-1" aria-selected="false" ><b>{$header}</b></a>
											</li>
										{/if}
									{/foreach}
								</ul>
									{foreach key=header name=block item=data from=$BLOCKS}
										{if $smarty.foreach.block.index eq 0}
											<div id="massedit_div{$smarty.foreach.block.index}" role="tabpanel" class="slds-tabs--scoped__content slds-truncate" style='display:block;'>
												{include file="DisplayFields.tpl"}
											</div>
										{else}
											<div id="massedit_div{$smarty.foreach.block.index}" role="tabpanel" class="slds-tabs--scoped__content slds-truncate" style='display:none;'>
												{include file="DisplayFields.tpl"}
											</div>
										{/if}
									{/foreach}
							</div>
						</td>
					</tr>
				</table>
				<table class="layerPopupTransport" width="100%">
					<tr class="slds-line-height--reset">
						<td style="padding: .5rem;" align="center">
							<!--input type="submit" name="save" class="crmbutton small edit" value="{$APP.LBL_SAVE_LABEL}">
							<input type="button" name="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="fninvsh('massedit')"-->
							<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="slds-button slds-button--small slds-button_success" onclick="document.getElementById('massedit_form').action.value='MassEditSave'; return massEditFormValidate()" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  ">
							<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="slds-button slds-button--small slds-button--destructive" onclick="fninvsh('massedit')" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
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
	massedit_initOnChangeHandlers();
{if $PICKIST_DEPENDENCY_DATASOURCE neq ''}
	(new FieldDependencies({$PICKIST_DEPENDENCY_DATASOURCE})).setup();
{/if}
<!-- vtlib customization: Help information assocaited with the fields -->
{if $FIELDHELPINFO}
	window.fieldhelpinfo = {literal}{}{/literal};
{foreach item=FIELDHELPVAL key=FIELDHELPKEY from=$FIELDHELPINFO}
	fieldhelpinfo["{$FIELDHELPKEY}"] = "{$FIELDHELPVAL}";
{/foreach}
{/if}
<!-- END -->
</script>
