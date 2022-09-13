{*<!--/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/ -->*}
<form action="index.php" method="post" name="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="fld_module" value="{$MODULE}">
	<input type="hidden" name="module" value="Settings">
	{assign var=entries value=$CFENTRIES}
	<div class="slds-page-header">
		<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
		<div class="slds-media">
		<div class="slds-media__body">
		<div class="slds-form" role="list">
			<div class="slds-form__row">
				<div class="slds-form__item">
					<div class="slds-form-element slds-form-element_horizontal">
						<label class="slds-form-element__label">{$MOD.LBL_RELATED_MODULE}</label>
							<div class="slds-form-element__control">
							<select class="slds-select" name='relatewithmodule' id='relatewithmodule'>
								{html_options options=$NotRelatedModules}
							</select>
						</div>
					</div>
				</div>
				<div class="slds-form__item">
					<div class="slds-form-element slds-form-element_horizontal">
						<label class="slds-form-element__label">{$MOD.LBL_RL_LABEL}</label>
						<div class="slds-form-element__control">
							<div class="slds-combobox_container">
								<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click">
									<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
										<input type="text" class="slds-input" id="rllabel">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="slds-form__row">
				<div class="slds-form__item">
					<div class="slds-form-element slds-form-element_horizontal">
						<label class="slds-form-element__label">{$MOD.Relation}</label>
							<div class="slds-form-element__control">
							<select class="slds-select" id='relation'>
								{html_options options=$Functions}
							</select>
						</div>
					</div>
				</div>
				<div class="slds-form__item">
					<div class="slds-form-element slds-form-element_horizontal">
						<button type="button" onclick="createRelatedList('{$MODULE}');" class="slds-button slds-button_icon slds-button_icon-border-filled" title="{$APP.LBL_ADD_NEW}" name="crlbutton">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
							</svg>
							<span class="slds-assistive-text">{$APP.LBL_ADD_NEW}</span>
						</button>
					</div>
				</div>
			</div>
		</div>
		</div>
		</div>
		</div>
		</div>
	</div>
	<table class="slds-table slds-table_bordered">
		{foreach item=related from=$RELATEDLIST name=relinfo}
		<tr>
			<td>{$related.label}</td>
			{if $smarty.foreach.relinfo.first}
			<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium" colspan="2">
				<button type="button" class="slds-button slds-button_icon" title="{$MOD.DOWN}" onclick="changeRelatedListorder('move_down','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}');">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowdown"></use>
					</svg>
					<span class="slds-assistive-text">{$MOD.DOWN}</span>
				</button>
			</td>
			{elseif $smarty.foreach.relinfo.last}
			<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
				<button type="button" class="slds-button slds-button_icon" title="{$MOD.UP}" onclick="changeRelatedListorder('move_up','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}');">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowup"></use>
					</svg>
					<span class="slds-assistive-text">{$MOD.UP}</span>
				</button>
			</td>
			<td align="right" class="cblds-t-align_right cblds-p-v_medium">
			</td>
			{else}
			<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
				<button type="button" class="slds-button slds-button_icon" title="{$MOD.UP}" onclick="changeRelatedListorder('move_up','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}');">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowup"></use>
					</svg>
					<span class="slds-assistive-text">{$MOD.UP}</span>
				</button>
			</td>
			<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
				<button type="button" class="slds-button slds-button_icon" title="{$MOD.DOWN}" onclick="changeRelatedListorder('move_down','{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}');">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowdown"></use>
					</svg>
					<span class="slds-assistive-text">{$MOD.DOWN}</span>
				</button>
			</td>
			{/if}
			<td align="right" valign="middle" class="cblds-t-align_right cblds-p-v_medium">
				<button type="button" class="slds-button slds-button_icon slds-button_icon-error" title="{$MOD.LBL_DELETE}" onclick="if (confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)) deleteRelatedList('{$related.tabid}','{$related.sequence}','{$related.id}','{$MODULE}');">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-assistive-text">{$MOD.LBL_DELETE}</span>
				</button>
			</td>
		</tr>
		{/foreach}
	</table>
</form>
