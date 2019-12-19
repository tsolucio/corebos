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

<table border=0 cellspacing=0 cellpadding=20 width="99%" class="settingsUI">
<tr>
<td valign=top>
<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr>
<td class="small" valign=top align=left>
<div id="view" class="workflows-list">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<datatable url="index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON" template="workflowlist_row_template">
	<header>
			<div class="slds-grid slds-gutters" style="width: 650px;">
				<div class="slds-col">
					<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;">
						<label class="slds-form-element__label" for="lookup-339">{'LBL_MODULE'|getTranslatedString:'Reports'} </label>
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
						{'LBL_DESCRIPTION'|getTranslatedString:'Reports'} 
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
						{'LBL_PURPOSE'|getTranslatedString:'Reports'} 
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
						<label class="slds-form-element__label" for="lookup-339">{'LBL_TRIGGER'|getTranslatedString:'Reports'} </label>
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
				<div class="slds-col">
					<div class="slds-form-element slds-float_right" style="width: 162px; margin-top: 24px;">
						<button class="slds-button slds-button_success" id='new_workflow'>{$MOD.LBL_NEW_WORKFLOW}</button>
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
			<td class="rptData" style="min-width:90px;">
				<a av="href:Record">
				<span class="slds-icon_container slds-icon_container_circle slds-icon-action-edit" title="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}">
					<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}</span>
				</span>
				</a>
				<a av="href:RecordDel" data-handler="remove" class="deleteanchor">
				<span av="id:workflow_id" class="slds-icon_container slds-icon_container_circle slds-icon-action-delete" title="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}">
					<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}</span>
				</span>
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
Template.define('workflowlist_row_template', {
	remove: function(button, data) {
		button.addEventListener('click', function (event) {
			event.preventDefault();
			var prompt = document.getElementById('confirm-prompt');
			prompt.style.display = 'block';
			document.getElementById('no_button').onclick = function () {
				document.getElementById('confirm-prompt').style.display = 'none';
			};
			document.getElementById('yes_button').onclick = function () {
				var return_url = encodeURIComponent('index.php?module=com_vtiger_workflow&action=workflowlist');
				document.getElementById('confirm-prompt').style.display = 'none';
				var base_url = (window.location.origin + window.location.pathname).replace('index.php', '');
				var idPart= '&workflow_id='+data.workflow_id;
				var deleteURL =base_url + 'index.php?module=com_vtiger_workflow&action=deleteworkflow'+idPart+'&return_url='+return_url;
				window.location.href = deleteURL;
			};
		});
	}
});
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
<!-- Prompt -->
<div id="confirm-prompt" style="display:none;">
	<section role="alertdialog" tabindex="0" aria-labelledby="modal-heading-01" aria-modal="true" aria-describedby="modal-content-id-1" class="slds-modal slds-fade-in-open">
		<div class="slds-modal__container">
			<header class="slds-modal__header slds-theme_error slds-theme_alert-texture">
				<h2 id="modal-heading-01" class="slds-text-heading_medium slds-hyphenate">{'LBL_DELETE_WORKFLOW'|@getTranslatedString:'com_vtiger_workflow'}</h2>
			</header>
			<div class="slds-modal__content slds-p-around_medium" id="prompt-message-wrapper">
				<p>{'WORKFLOW_DELETE_CONFIRMATION'|@getTranslatedString:'com_vtiger_workflow'}</p>
			</div>
			<footer class="slds-modal__footer" style="width:auto;">
				<button class="slds-button slds-button_neutral" id="no_button">{$APP.LBL_NO}</button>
				<button class="slds-button slds-button_neutral" id="yes_button">{$APP.LBL_YES}</button>
			</footer>
		</div>
	</section>
	<div class="slds-backdrop slds-backdrop_open"></div>
</div>
{include file='com_vtiger_workflow/Footer.tpl'}

