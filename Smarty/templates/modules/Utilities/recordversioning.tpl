<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td class="detailview_wrapper_cell">
			{include file='Buttons_List.tpl'}
		</td>
	</tr>
</table>
<div class="slds-page-header" role="banner">
	<div class="slds-grid">
		<div class="slds-col slds-has-flexi-truncate">
			<div class="slds-media slds-no-space slds-grow">
				<div class="slds-media__figure">
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user">
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#change_record_type"></use>
					</svg>
				</div>
				<div class="slds-media__body">
					<h1 class="slds-page-header__title slds-m-right_small slds-align-middle slds-truncate" title="{$TITLE_MESSAGE}">{$TITLE_MESSAGE}</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<form role="form" name="faform">
<input type="hidden" name="module" value="Utilities">
<input type="hidden" name="action" value="integration">
<input type="hidden" name="_op" id="_op" value="setconfigrecordversioning">
<input type="hidden" name="tabid" id="tabid" value="">
<input type="hidden" name="onoroff" id="onoroff" value="">
<div class="slds-grid slds-wrap slds-p-around_x-small">
	{foreach item=modinfo from=$MODULELIST}
	<div class="slds-col slds-medium-size_1-of-2 slds-large-size_1-of-3 slds-size_1-of-1 slds-form-element slds-m-top_x-small slds-utility-bar__action">
		<label class="slds-checkbox_toggle slds-grid">
			<span class="slds-form-element__label slds-m-bottom_none slds-col slds-size_2-of-3">&nbsp;{$modinfo['name']}</span>
			<input type="checkbox" name="tabid_{$modinfo['tabid']}" value="{$modinfo['tabid']}" aria-describedby="{$modinfo['name']}" {if $modinfo['visible']}checked{/if}
				onChange="VtigerJS_DialogBox.block();document.getElementById('tabid').value=this.value;document.getElementById('onoroff').value=this.checked;document.faform.submit();" />
			<span id="tabid_{$modinfo['tabid']}" class="slds-checkbox_faux_container slds-col slds-size_1-of-3" aria-live="assertive">
				<span class="slds-checkbox_faux"></span>
				<span class="slds-checkbox_on"></span>
				<span class="slds-checkbox_off"></span>
			</span>
		</label>
	</div>
	{/foreach}
</div>
</form>
