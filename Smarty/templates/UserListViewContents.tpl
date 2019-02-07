{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<!-- BunnyJs Script Files -->
<link rel="stylesheet" href="include/bunnyjs/css/svg-icons.css">
<script src="include/bunnyjs/utils-dom.min.js"></script>
<script src="include/bunnyjs/ajax.min.js"></script>
<script src="include/bunnyjs/template.min.js"></script>
<script src="include/bunnyjs/pagination.min.js"></script>
<script src="include/bunnyjs/url.min.js"></script>
<script src="include/bunnyjs/utils-svg.min.js"></script>
<script src="include/bunnyjs/spinner.min.js"></script>
<script src="include/bunnyjs/datatable.min.js"></script>
<script src="include/bunnyjs/datatable.icons.min.js"></script>
<script src="include/bunnyjs/element.min.js"></script>
<script src="include/bunnyjs/datatable.scrolltop.min.js"></script>
<!-- BunnyJs Script Files -->
<script type="text/javascript" src="include/js/ListView.js"></script>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
<tr>
	<td class="big"><strong>{$MOD.LBL_USERS_LIST}</strong></td>
	<td class="small" align=right>&nbsp;</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
<tr>
	<td class="big" nowrap align="right">
		<div align="right">
		<input title="{$CMOD.LBL_NEW_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_NEW_USER_BUTTON_KEY}" type="submit" name="button" value="{$CMOD.LBL_NEW_USER_BUTTON_LABEL}" class="crmButton create small">
		<input title="{$CMOD.LBL_EXPORT_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_EXPORT_USER_BUTTON_KEY}" type="button" onclick="return selectedRecords('Users','ptab')" value="{$CMOD.LBL_EXPORT_USER_BUTTON_LABEL}" class="crmButton small cancel">
		</div>
	</td>
</{$APP.LBL_EXPORT}
{if !empty($ERROR_MSG)}
<tr>
	{$ERROR_MSG}
</tr>
{/if}
</tr>
</table>
<div id="view" class="workflows-list">
	<datatable url="index.php?module=Users&action=index&file=getJSON" template="userlist_row_template">
	<header>
			<div class="slds-grid slds-gutters" style="width: 650px;">
				<div class="slds-col">
					<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;">
						<label class="slds-form-element__label" for="lookup-339">{'LBL_MODULE'|getTranslatedString:'Reports'} {'LBL_Search'|getTranslatedString:'MailManager'}</label>
						<div class="slds-form-element__control slds-grid slds-box_border">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<select name="list_module" id="list_module" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
									aria-owns="list_module" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
									<option value="all" selected="true">{$APP.LBL_ALLPICKLIST}</option>
									{$modulelist}
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element" style="width: 162px; margin-bottom: 6px;">
						<label class="slds-form-element__label" for="text-input-id-1">
						{'LBL_DESCRIPTION'|getTranslatedString:'Reports'} {'LBL_Search'|getTranslatedString:'MailManager'}
						</label>
						<div class="slds-form-element__control">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<input type="text"  name="desc_search" id="desc_search" class="slds-input" style="height: 30px;"/>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element" style="width: 162px;">
						<label class="slds-form-element__label" for="text-input-id-1">
						{'LBL_PURPOSE'|getTranslatedString:'Reports'} {'LBL_Search'|getTranslatedString:'MailManager'}
						</label>
						<div class="slds-form-element__control">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<input type="text"  name="purpose_search" id="purpose_search" class="slds-input" style="height: 30px;"/>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;">
						<label class="slds-form-element__label" for="lookup-339">{'LBL_TRIGGER'|getTranslatedString:'Reports'} {'LBL_Search'|getTranslatedString:'MailManager'}</label>
						<div class="slds-form-element__control slds-grid slds-box_border">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<select name="trigger_list" id="trigger_list" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
									aria-owns="trigger_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
									<option value="all" selected="true">{$APP.LBL_ALLPICKLIST}</option>
									{$triggerlist}
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<footer>
			<pagination limit={$PAGINATION_LIMIT} outer></pagination>
		</footer>
		<table class="rptTable">
			<tr>
			{foreach key=dtkey item=dtheader from=$LIST_HEADER}
				<th pid="{$dtkey}" class="rptCellLabel">{$dtheader}</th>
			{/foreach}
			</tr>
		</table>
	</datatable>
</div>
<table id="userlist_row_template" hidden>
	<tr>
		{foreach key=dtkey item=dtheader from=$LIST_FIELDS}
			{if $dtheader eq 'workflow_id'}
			<td class="rptData">
				<a av="href:Record"><span>
				<img border="0" title="{'LBL_EDIT'|@getTranslatedString}" alt="{'LBL_EDIT'|@getTranslatedString}"
					style="cursor: pointer;" src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/></span></a>
				<a av="href:RecordDel"><span av="id:workflow_id"><img border="0" title="{'LBL_DELETE'|@getTranslatedString}" alt="{'LBL_DELETE'|@getTranslatedString}"
					src="{'delete.gif'|@vtiger_imageurl:$THEME}" style="cursor: pointer;"</span>
				</a>
			</td>
			{else}
			<td v="{$dtkey}" class="rptData"></td>
			{/if}
		{/foreach}
	</tr>
</table>
<script type="text/javascript">
{literal}
Template.define('userlist_row_template', {});
DataTable.onRedraw(document.getElementsByTagName('datatable')[0], function (data) {
	for (index in data.data) {
		if (data.data[index].isDefaultWorkflow) {
			document.getElementById(data.data[index].workflow_id).style.display = "none";
		}
	}
});
{/literal}
Pagination._config.langFirst = "{$APP.LNK_LIST_START}";
Pagination._config.langLast = "{$APP.LNK_LIST_END}";
Pagination._config.langPrevious = "< {$APP.LNK_LIST_PREVIOUS}";
Pagination._config.langNext = "{$APP.LNK_LIST_NEXT} >";
{literal}
Pagination._config.langStats = "{from}-{to} {/literal}{$APP.LBL_LIST_OF}{literal} {total} ({/literal}{$APP.Page}{literal} {currentPage} {/literal}{$APP.LBL_LIST_OF}{literal} {lastPage})";
DataTableConfig.loadingImg = 'themes/images/loading.svg';
DataTableConfig.searchInputName = 'trigger_list';
DataTableConfig.searchInputName = 'list_module';
</script>
{/literal}
