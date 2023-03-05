<table width="80%" cellpadding="5" cellspacing="0" class="searchUIBasic small" align="center" border=0>
<tr>
	<td class="searchUIName small" nowrap align="left" width="15%">
		<span class="moduleName">{$APP.LBL_SEARCH}</span><br>
	</td>
	<td class="small" width="20%">
		<div class="slds-form-element">
			<div class="slds-form-element__control slds-input-has-icon slds-input-has-icon_left">
				<svg class="slds-icon slds-input__icon slds-input__icon_left slds-icon-text-default" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
				</svg>
				<input type="text" id="search_text" name="search_text" placeholder="{$APP.LBL_SEARCH_FOR}" class="slds-input" />
			</div>
		</div>
	</td>
	<td class="small" nowrap width="1%">
		<label class="slds-form-element__label">{$APP.LBL_IN}</label>
	</td>
	<td class="small" nowrap  width="20%">
		<div id="basicsearchcolumns_real">
			<div class="slds-form-element">
				<div class="slds-form-element__control">
					<div class="slds-select_container">
						<select class="slds-select" name="search_field" id="bas_searchfield">
							{html_options options=$SEARCHLISTHEADER }
						</select>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="searchtype" value="BasicSearch">
		<input type="hidden" name="module" value="{$SELECTED_MODULE}">
		<input type="hidden" name="action" value="index">
		<input type="hidden" name="query" value="true">
		<input type="hidden" name="search_cnt">
	</td>
	<td class="small" nowrap width="30%">
		<div class="slds-button-group" role="group">
			<button onClick="callRBSearch('Basic');" class="slds-button slds-button_neutral" type="button">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
				</svg>
				{$APP.LBL_SEARCH_NOW_BUTTON}
			</button>
			<button onClick="ListView.Reload()" class="slds-button slds-button_text-destructive" type="button">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#refresh"></use>
				</svg>
				{$APP.LBL_CLEAR}
			</button>
		</div>
	</td>
</tr>
<tr>
	<td colspan="7" align="center" class="small">
		<table border=0 cellspacing=0 cellpadding=0 width=100%>
			<tr>
				{$ALPHABETICAL}
			</tr>
		</table>
	</td>
</tr>
</table>
