<script type="text/javascript" charset="utf-8">
{literal}
	var searchConditions = [
		{"groupid":"1",
		 "columnname":"vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V",
		 "comparator":"e",
		 "value":"Condition Query",
		 "columncondition":""}
	];
	var advSearch = '&query=true&searchtype=advance&advft_criteria='+convertArrayOfJsonObjectsToString(searchConditions);
	var SpecialSearch = encodeURI(advSearch);
{/literal}
</script>
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
<form role="form" style="margin:0 100px;" name="elsform">
<input type="hidden" name="module" value="Utilities">
<input type="hidden" name="action" value="integration">
<input type="hidden" name="_op" id="_op" value="setconfigelasticsearch">
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
					onChange="document.getElementById('_op').value='getconfigelasticsearch';document.elsform.submit();"
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
	<input type="checkbox" name="rvactive" aria-describedby="toggle-desc" {if $isActive}checked{/if} onChange="if (document.getElementById('bmapid').value=='') { alert('{'choosemap'|@getTranslatedString:'choosemap'}'); return false; } else { document.elsform.submit(); }"/>
	<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
		<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
	</span>
	</label>
</div>
<br>
<div class="slds-form-element">
{'Map_Query'|@getTranslatedString:'Map_Query'}
<div class="slds-form-element__control">
	<input id="bmapid" name="bmapid" type="hidden" value="{$tablemapid}">
	<input id="bmapid_display" name="bmapid_display" readonly="" style="border:1px solid #bababa;" type="text" value="{$mapname}">&nbsp;
	<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="1" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}"
		onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=elsform&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch, 'vtlibui10wf', cbPopupWindowSettings);"
		style="cursor:hand;cursor:pointer" align="absmiddle">&nbsp;
	<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
	alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
</div>
</div>
<br>
<center><b>{'sel_fields_es'|@getTranslatedString:'sel_fields_es'}</b></center>
<br>
<div class="slds-form-element">
<table><th></th><th>{'Name'|@getTranslatedString}</th><th>{'LBL_TYPE'|@getTranslatedString}</th><th>{'Analyzed'|@getTranslatedString:'Analyzed'}</th>
{$fields}
</table>
</div>
</div>
</form>