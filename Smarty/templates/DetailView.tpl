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
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
{if $FIELD_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="include/js/FieldDepFunc.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim} (new FieldDependencies({$FIELD_DEPENDENCY_DATASOURCE})).init(document.forms['DetailView']) {rdelim});
</script>
{/if}
<script type="text/javascript" src="include/js/clipboard.min.js"></script>
<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
	<a class="link" id="clipcopylink" href="javascript:;" onclick="handleCopyClipboard(event);" data-clipboard-text="">
		{$APP.LBL_COPY_BUTTON}
	</a>
</span>
<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
{literal}
	var clipcopyobject = new ClipboardJS('#clipcopylink');
	clipcopyobject.on('success', function(e) { clipcopyclicked = false; });
	clipcopyobject.on('error', function(e) { clipcopyclicked = false; });
	{/literal}
</script>
<script>
{if isset($PopupFilterMapResults)}
	let PopupFilterMapResults = JSON.parse(`{$PopupFilterMapResults}`);
{/if}
</script>

<div id="lstRecordLayout" class="layerPopup" style="display:none;width:325px;height:300px;"></div>
{if $MODULE eq 'Accounts' || $MODULE eq 'Contacts'}
	{if $MODULE eq 'Accounts'}
		{assign var=address1 value=$MOD.LBL_BILLING_ADDRESS}
		{assign var=address2 value=$MOD.LBL_SHIPPING_ADDRESS}
	{/if}
	{if $MODULE eq 'Contacts'}
		{assign var=address1 value=$MOD.LBL_PRIMARY_ADDRESS}
	{assign var=address2 value=$MOD.LBL_ALTERNATE_ADDRESS}
{/if}
<div class="slds-card" id="locateMap" onMouseOut="fninvsh('locateMap')" onMouseOver="fnvshNrm('locateMap')">
	<div class="slds-card__body slds-card__body_inner">
		<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation('Main');" class="calMnu">
			{$address1}
		</a>
	</div>
	<div class="slds-card__body slds-card__body_inner">
		<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation('Other');" class="calMnu">
			{$address2}
		</a>
	</div>
</div>
{/if}
<section class="detailview_wrapper_table">
	<div style="margin-top: -0.5%;">
	{include file='Buttons_List.tpl' isDetailView=true}
	</div>
	{include file='Components/DetailView/MainHeader.tpl'}
	<div class="small" style="padding:14px" onclick="hndCancelOutsideClick();">
	{include file='applicationmessage.tpl'}
	{include file='DetailViewWidgetBar.tpl'}
	<div class="slds-grid slds-gutters" style="width: 99.5%;margin-left: 0.0%;">
		<div class="slds-col slds-size_4-of-5" id="bodycolumn" style="background: white;margin-right: 0.5%;">
			{include file='Components/DetailView/Body.tpl'}
		</div>
		<div class="slds-col slds-size_1-of-5" id="actioncolumn" style="background: white;{$DEFAULT_ACTION_PANEL_STATUS}">
			{include file='Components/DetailView/Actions.tpl'}
		</div>
	</div>
	<div class="slds-grid" style="background: white;padding-bottom: 1%;margin-top: 0.5%">
		<div class="slds-col">
			{include file='Components/DetailView/Others.tpl'}
		</div>
	</div>
	<script>
		var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
		var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
		var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
	</script>
	{include file='MassEditHtml.tpl'}
	{if $MODULE|hasEmailField}
	<form name="SendMail" method="post">
		<div id="sendmail_cont" style="z-index:100001;position:absolute;"></div>
	</form>
	{/if}