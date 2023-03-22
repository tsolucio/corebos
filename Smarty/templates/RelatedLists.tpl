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
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/RelatedLists.js"></script>
{include file='Buttons_List.tpl' isDetailView=true}
{include file='applicationmessage.tpl'}
<div id="editlistprice" style="position:absolute;width:300px;"></div>
<div class="slds-grid slds-gutters" style="background: white; padding-top: 1%;width: 98%;margin-left: 1%;margin-top: -0.5%">
	<div class="slds-col">
		<div class="slds-tabs_{$TABSCOPED} slds-tabs_medium">
			<ul class="slds-tabs_{$TABSCOPED}__nav" role="tablist">
				<li class="slds-tabs_{$TABSCOPED}__item" role="presentation">
					<a class="slds-tabs_{$TABSCOPED}__link" role="tab" tabindex="0" href="index.php?action=DetailView&module={$MODULE}&record={$ID}" style="font-size: 13px">
						<span class="{$currentModuleIcon['containerClass']}">
							<svg class="slds-icon slds-icon_small" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/{$currentModuleIcon['library']}-sprite/svg/symbols.svg#{$currentModuleIcon['icon']}"></use>
							</svg>
						</span>
						{$SINGLE_MOD} {$APP.LBL_INFORMATION}
					</a>
				</li>
				{if isset($HASRELATEDPANES) && $HASRELATEDPANES eq 'true'}
					{assign var="rlmode" value="RelatedPane"}
					{include file='RelatedPanes.tpl' tabposition='top'}
				{else}
					{if !(GlobalVariable::getVariable('Application_Hide_Related_List', 0))}
					<li class="slds-tabs_{$TABSCOPED}__item slds-is-active" role="presentation">
						<a class="slds-tabs_{$TABSCOPED}__link" role="tab" tabindex="0" aria-selected="true" style="font-weight: 600;font-size: 13px">
							{$APP.LBL_MORE} {$APP.LBL_INFORMATION}
						</a>
					</li>
					{/if}
				{/if}
			</ul>
		</div>
	</div>
</div>
{if isset($OP_MODE) && $OP_MODE eq 'edit_view'}
	{assign var="action" value="EditView"}
{else}
	{assign var="action" value="DetailView"}
{/if}
<div class="slds-grid slds-gutters" style="background: white; padding-top: 1%;width: 98%;margin-left: 1%;margin-bottom: -0.5%;">
	<div class="slds-col">
		{include file='RelatedListsHidden.tpl'}
		<div id="RLContents">
		{include file='RelatedListContents.tpl'}
		</div>
		</form>
	</div>
</div>
{if $MODULE|hasEmailField}
<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();" method="post"><div id="sendmail_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
{/if}
