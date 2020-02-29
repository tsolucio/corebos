{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#change_record_type"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
	<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium">
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
	</div>
</div>
</section>
