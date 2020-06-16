{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
		<div class="slds-media__figure">
			<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
			</svg>
			{$TITLE_MESSAGE}
		</div>
		</h2>
	</header>
<br>
{if $ERROR eq 1}
<div class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center" style="color:red">{'Missing_GlobalVar'|getTranslatedString:'Missing_GlobalVar'}
</div>
{/if}
<br>
<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium slds-card">
<form role="form" style="margin:0 100px;" name="socketform">
<input type="hidden" name="module" value="Utilities">
<input type="hidden" name="action" value="integration">
<input type="hidden" name="_op" id="_op" value="setconfigsocketblockit">
<header>
	<div class="slds-form-element slds-lookup" data-select="single" style="width: 400px; margin-bottom: 6px;">
		<label class="slds-form-element__label" for="lookup-339">{'LBL_MODULE'|getTranslatedString:'LBL_MODULE'}</label>
		<div class="slds-form-element__control slds-grid slds-box_border">
			<div class="slds-dropdown_trigger slds-dropdown-trigger_click slds-align-middle slds-m-left_xx-small slds-shrink-none">
				<svg aria-hidden="true" class="slds-icon slds-icon-standard-account slds-icon_small">
					<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#user"></use>
				</svg>
			</div>
			<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
				<svg aria-hidden="true" class="slds-input__icon">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
				</svg>
				<select name="module_list" id="module_list" class="slds-lookup__search-input slds-input_bare" type="search"
					onChange="document.getElementById('_op').value='getconfigsocketblockit';document.socketform.submit();"
					aria-owns="module_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
					<option value="none" selected="true">{$APP.LBL_NONE}</option>
					{$MODULELIST}
				</select>
			</div>
		</div>
	</div>
</header>

<div class="slds-form-element">
	<label class="slds-checkbox_toggle slds-grid">
	<input type="checkbox" name="rvactive" aria-describedby="toggle-desc" {if $isActive}checked{/if} onChange="if (document.getElementById('sockethost').value=='') { alert('{'choosesocket'|@getTranslatedString:'choosesocket'}'); return false; } else { document.socketform.submit(); }"/>
	<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
		<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
	</span>
	</label>
</div>
<br><br>
<div class="slds-form-element">
 {'SocketHost'|@getTranslatedString:'SocketHost'}   <input class="slds-input" type="text" name="sockethost" id="sockethost" value="{$sockethost}">
</div>
<br><br>
<div class="slds-form-element">
<div class="slds-checkbox">
<input type="checkbox" name="blockit" id="blockit" {if $blockit eq '1'}checked{/if}>
<label class="slds-checkbox__label" for="blockit">
<span class="slds-checkbox_faux"></span>
<span class="slds-form-element__label">{'BlockIt'|@getTranslatedString:'BlockIt'}</span>
</label>
</div>
</div>
</form>