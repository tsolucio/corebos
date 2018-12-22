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
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#password_unlock"></use>
					</svg>
				</div>
				<div class="slds-media__body">
					<h1 class="slds-page-header__title slds-m-right_small slds-align-middle slds-truncate"
						title="{$TITLE_MESSAGE}">{$TITLE_MESSAGE}</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<form role="form" style="margin:0 100px;" name="faform">
<input type="hidden" name="module" value="Utilities">
<input type="hidden" name="action" value="integration">
<input type="hidden" name="_op" id="_op" value="setconfig2fa">
<header>
	<div class="slds-form-element slds-lookup" data-select="single" style="width: 400px; margin-bottom: 6px;">
		<label class="slds-form-element__label" for="lookup-339">{'LBL_USER'|getTranslatedString:'Users'}</label>
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
				<select name="user_list" id="user_list" class="slds-lookup__search-input slds-input_bare" type="search"
					onChange="document.getElementById('_op').value='getconfig2fa';document.faform.submit();"
					aria-owns="user_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
					<option value="none" selected="true">{$APP.LBL_NONE}</option>
					{$USERLIST}
				</select>
			</div>
		</div>
	</div>
</header>
<div class="slds-form-element">
	<label class="slds-checkbox_toggle slds-grid">
	<input type="checkbox" name="2faactive" aria-describedby="toggle-desc" {if $isActive}checked{/if} onChange="document.faform.submit();"/>
	<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
		<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
	</span>
	</label>
</div>
<div class="slds-form-element slds-m-top_small" id="fasecretcode" style="display:{if $isActive}block{else}none{/if}">
	<label class="slds-form-element__label" for="clientId">{'2FA Secret Code'|@getTranslatedString:$MODULE}</label>
	<div class="slds-form-element__control">
		<b>{$FASecret}</b><br/><img style="vertical-align: text-top;" src="{$QRCODE}">
	</div>
</div>
</form>
