<script type="module" src="./include/ldswc/vaadingrid/vaadingrid.js"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/workflowlistscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
	var i18nWorkflowActions = {
		'WORKFLOW_DELETE_CONFIRMATION': '{'WORKFLOW_DELETE_CONFIRMATION'|@getTranslatedString:'com_vtiger_workflow'}',
		'LBL_DELETE_WORKFLOW': '{'LBL_DELETE_WORKFLOW'|@getTranslatedString:'com_vtiger_workflow'}',
		'WORKFLOW_ACTIVATE_CONFIRMATION': '{'WORKFLOW_ACTIVATE_CONFIRMATION'|@getTranslatedString:'com_vtiger_workflow'}',
		'LBL_ACTIVATE_WORKFLOW': '{'LBL_ACTIVATE_WORKFLOW'|@getTranslatedString:'com_vtiger_workflow'}',
		'WORKFLOW_DEACTIVATE_CONFIRMATION': '{'WORKFLOW_DEACTIVATE_CONFIRMATION'|@getTranslatedString:'com_vtiger_workflow'}',
		'LBL_DEACTIVATE_WORKFLOW': '{'LBL_DEACTIVATE_WORKFLOW'|@getTranslatedString:'com_vtiger_workflow'}',
	}
</script>
{include file='com_vtiger_workflow/ModuleTitle.tpl' show='wflist'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">

{include file='com_vtiger_workflow/NewWorkflowPopup.tpl'}
<!-- Prompt -->
<div id="confirm-prompt" style="display:none;">
	<section role="alertdialog" tabindex="0" aria-labelledby="modal-wfaction" aria-modal="true" aria-describedby="modal-content-id-1" class="slds-modal slds-fade-in-open">
		<div class="slds-modal__container">
			<header class="slds-modal__header slds-theme_error slds-theme_alert-texture">
				<h2 id="modal-wfaction" class="slds-text-heading_medium slds-hyphenate">{'LBL_DELETE_WORKFLOW'|@getTranslatedString:'com_vtiger_workflow'}</h2>
			</header>
			<div class="slds-modal__content slds-p-around_medium slds-page-header__title" id="prompt-message-wrapper">
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

<vaadin-grid id="wfgrid" theme="row-dividers" column-reordering-allowed multi-sort class="slds-table slds-table_cell-buffer slds-table_bordered slds-carousel__panel-action slds-m-around_xx-small" style="height: 70vh;">
	<vaadin-grid-selection-column auto-select frozen></vaadin-grid-selection-column>
	{foreach key=dtkey item=dtheader from=$LIST_HEADER}
		{if $dtheader=='Tools'}
			<vaadin-grid-column id="wftoolcol" header="{$APP.LBL_TOOLS}" width="180px" flex-grow="0"></vaadin-grid-column>
		{elseif $dtheader=='Module'}
			<vaadin-grid-column-group resizable>
				<vaadin-grid-column>
					<template class="header">
						<vaadin-grid-sorter path="{$dtheader}" header="{$APP.LBL_MODULE}">{$APP.LBL_MODULE}</vaadin-grid-sorter><br>
						<vaadin-grid-filter id="wfmodfilter" path="{$dtheader}" value="">
							<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;" slot="filter">
								<div class="slds-form-element__control slds-grid slds-box_border">
									<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
										<svg aria-hidden="true" class="slds-input__icon">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
										</svg>
										<select name="list_module" id="list_module" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
											aria-owns="list_module" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list"
											onchange="document.getElementById('wfmodfilter').value=this.value">
											{$modulelist}
										</select>
									</div>
								</div>
							</div>
						</vaadin-grid-filter>
					</template>
					<template>[[item.{$dtheader}]]</template>
				</vaadin-grid-column>
			</vaadin-grid-column-group>
		{elseif $dtheader=='Description'}
			<vaadin-grid-column-group resizable>
				<vaadin-grid-column>
					<template class="header">
						<vaadin-grid-sorter path="{$dtheader}" header="{$APP.LBL_DESCRIPTION}">{$APP.LBL_DESCRIPTION}</vaadin-grid-sorter><br>
						<vaadin-grid-filter id="wfdescfilter" path="{$dtheader}" value="">
							<div class="slds-form-element" style="width: 162px; margin-bottom: 6px;" slot="filter">
								<div class="slds-form-element__control">
									<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
										<svg aria-hidden="true" class="slds-input__icon">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
										</svg>
										<input type="text" name="desc_search" id="desc_search" class="slds-input" style="height: 30px;" onchange="document.getElementById('wfdescfilter').value=this.value"/>
									</div>
								</div>
							</div>
						</vaadin-grid-filter>
					</template>
					<template>[[item.{$dtheader}]]</template>
				</vaadin-grid-column>
			</vaadin-grid-column-group>
		{elseif $dtheader=='Purpose'}
			<vaadin-grid-column-group resizable>
				<vaadin-grid-column>
					<template class="header">
						<vaadin-grid-sorter path="{$dtheader}" header="{$APP.LBL_PURPOSE}">{$APP.LBL_PURPOSE}</vaadin-grid-sorter><br>
						<vaadin-grid-filter id="wfpurposefilter" path="{$dtheader}" value="">
							<div class="slds-form-element" style="width: 162px;" slot="filter">
								<div class="slds-form-element__control">
									<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
										<svg aria-hidden="true" class="slds-input__icon">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
										</svg>
										<input type="text" name="purpose_search" id="purpose_search" class="slds-input" style="height: 30px;" onchange="document.getElementById('wfpurposefilter').value=this.value"/>
									</div>
								</div>
							</div>
						</vaadin-grid-filter>
					</template>
					<template>[[item.{$dtheader}]]</template>
				</vaadin-grid-column>
			</vaadin-grid-column-group>
		{elseif $dtheader=='Status'}
			<vaadin-grid-column-group resizable>
				<vaadin-grid-column>
					<template class="header">
						<vaadin-grid-sorter path="{$dtheader}" header="{$APP.LBL_STATUS}">{$APP.LBL_STATUS}</vaadin-grid-sorter><br>
						<vaadin-grid-filter id="wfstatusfilter" path="{$dtheader}" value="">
							<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;" slot="filter">
								<div class="slds-form-element__control slds-grid slds-box_border">
									<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
										<svg aria-hidden="true" class="slds-input__icon">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
										</svg>
										<select name="status_list" id="status_list" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
											aria-owns="status_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list"
											onchange="document.getElementById('wfstatusfilter').value=this.value">
											<option value="true" selected="false">{$APP.Active}</option>
											<option value="false" selected="false">{$APP.Inactive}</option>
											<option value="all" selected="true">{$APP.LBL_ALLPICKLIST}</option>
										</select>
									</div>
								</div>
							</div>
						</vaadin-grid-filter>
						</template>
					<template>[[item.{$dtheader}]]</template>
				</vaadin-grid-column>
			</vaadin-grid-column>
		{elseif $dtheader=='Trigger'}
			<vaadin-grid-column-group resizable>
				<vaadin-grid-column>
					<template class="header">
						<vaadin-grid-sorter path="{$dtheader}" header="{$APP.LBL_TRIGGER}">{$APP.LBL_TRIGGER}</vaadin-grid-sorter><br>
						<vaadin-grid-filter id="wftriggerfilter" path="{$dtheader}" value="">
							<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;" slot="filter">
								<div class="slds-form-element__control slds-grid slds-box_border">
									<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
										<svg aria-hidden="true" class="slds-input__icon">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
										</svg>
										<select name="trigger_list" id="trigger_list" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
											aria-owns="trigger_list" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list"
											onchange="document.getElementById('wftriggerfilter').value=this.value">
											<option value="all" selected="true">{$APP.LBL_ALLPICKLIST}</option>
											{$triggerlist}
										</select>
									</div>
								</div>
							</div>
						</vaadin-grid-filter>
					</template>
					<template>[[item.{$dtheader}]]</template>
				</vaadin-grid-column>
			</vaadin-grid-column-group>
		{else}
			<vaadin-grid-sort-column path="{$dtheader}" header="{$dtheader}">
			</vaadin-grid-sort-column>
		{/if}
	{/foreach}
</vaadin-grid>
</div>
</section>
<script>
	var url = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON';
	document.getElementById('wftoolcol').renderer = (root, grid, rowData) => {
		let ihtml = `<a href="{literal}${rowData.item.Record}{/literal}">
			<span class="slds-icon_container slds-icon_container_circle slds-icon-action-edit" title="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}">
				<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use>
				</svg>
				<span class="slds-assistive-text">{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE_NAME}</span>
			</span>
			</a>`;
		if (!rowData.item.isDefaultWorkflow) {
			ihtml += `<a href="javascript:wfRemoveFromList({literal}'${rowData.item.RecordDel}'{/literal})" data-handler="remove" class="deleteanchor">
			<span av="id:workflow_id" class="slds-icon_container slds-icon_container_circle slds-icon-action-delete" title="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}">
				<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use>
				</svg>
				<span class="slds-assistive-text">{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE_NAME}</span>
			</span>
			</a>`;
		}
		root.innerHTML = ihtml;
	};
{literal}
	const grid = document.getElementById('wfgrid');
	grid.dataProvider = wfListDataProvider;
	GlobalVariable_getVariable('Workflow_ListView_PageSize', 20, '', gVTUserID).then(function (response) {
		var obj = JSON.parse(response);
		grid.pageSize = parseInt(obj.Workflow_ListView_PageSize, 10);
		grid.querySelector('vaadin-checkbox[aria-label="Select All"]').style.display = 'none';
	});
{/literal}
</script>
