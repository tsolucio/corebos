{include file='com_vtiger_workflow/Header.tpl'}
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

<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/workflowlistscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
</script>

{include file='SetMenu.tpl'}
<div id="view" class="workflows-list">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<table class="listTableTopButtons" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="small"> <span id="status_message"></span> </td>
			<td class="small cblds-t-align_right" align="right">
				<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_WORKFLOW}" id='new_workflow'/>
			</td>
		</tr>
	</table>
	<datatable url="index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON" template="workflowlist_row_template">
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
								<select name="modules_list" id="modules_list" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
									aria-owns="modules_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
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
									<option value="all" selected="true">{$APP.LBL_ALL}</option>
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
<table id="workflowlist_row_template" hidden>
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
Template.define('workflowlist_row_template', {});
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
DataTableConfig.searchInputName = 'modules_list';
</script>
{/literal}
<!--New workflow popup-->
<div id="new_workflow_popup" class="layerPopup" style="display:none;">
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine">
		<tr>
			<td width="80%" align="left" class="layerPopupHeading">
				{$MOD.LBL_CREATE_WORKFLOW}
				</td>
			<td width="20%" class="cblds-t-align_right" align="right">
				<a href="javascript:void(0);" id="new_workflow_popup_close">
					<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
			</td>
		</tr>
	</table>

	<form action="index.php" method="post" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
		<div class="popup_content">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr align="left">
					<td><input type="radio" name="source" value="from_module" checked="true" class="workflow_creation_mode">
						{$MOD.LBL_FOR_MODULE}</td>
					<td><input type="radio" name="source" value="from_template" class="workflow_creation_mode">
						{$MOD.LBL_FROM_TEMPLATE}</td>
				</tr>
			</table>
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr align="left">
					<td width='10%' nowrap="nowrap">{$MOD.LBL_CREATE_WORKFLOW_FOR}</td>
					<td>
						<select name="module_name" id="module_list" class="small">
{foreach item=moduleName from=$moduleNames}
							<option value="{$moduleName}" {if $moduleName eq $listModule}selected="selected"{/if}>
								{$moduleName|@getTranslatedString:$moduleName}
							</option>
{/foreach}
						</select>
					</td>
				</tr>
				<tr align="left" id="template_select_field" style="display:none;">
					<td>{$MOD.LBL_CHOOSE_A_TEMPLATE}</td>
					<td>
						<span id="template_list_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
						<span id="template_list_foundnone" style='display:none;'><b>{$MOD.LBL_NO_TEMPLATES}</b></span>
						<select id="template_list" name="template_id" class="small"></select>
					</td>
				</tr>
			</table>
			<input type="hidden" name="save_type" value="new" id="save_type_new">
			<input type="hidden" name="module" value="{$module->name}" id="save_module">
			<input type="hidden" name="action" value="editworkflow" id="save_action">
			<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
				<tr><td class="cblds-t-align_center" align="center">
					<input type="submit" class="crmButton small save" value="{$APP.LBL_CREATE_BUTTON_LABEL}" name="save" id='new_workflow_popup_save'/>
					<input type="button" class="crmButton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " name="cancel" id='new_workflow_popup_cancel'/>
				</td></tr>
			</table>
		</div>
	</form>
</div>
<!--Done Popups-->
{include file='com_vtiger_workflow/Footer.tpl'}

