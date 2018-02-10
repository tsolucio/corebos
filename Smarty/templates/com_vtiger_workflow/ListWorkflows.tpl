{include file='com_vtiger_workflow/Header.tpl'}
<script src="modules/{$module->name}/resources/json2.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/workflowlistscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
</script>
<!--New workflow popup-->
<div id="new_workflow_popup" class="layerPopup" style="display:none;">
	<table class="slds-table slds-no-row-hover" style="border-bottom: 1px solid #d4d4d4;">
		<tr class="slds-text-title--header">
			<!-- Header -->
			<th scope="col">
				<div class="slds-truncate moduleName">{$MOD.LBL_CREATE_WORKFLOW}</div>
			</th>
			<!-- Close Icon -->
			<th scope="col" style="padding: .5rem;text-align: right;">
				<div class="slds-truncate">
					<a href="javascript:void(0);" id="new_workflow_popup_close">
						<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
					</a>
				</div>
			</th>
		</tr>
	</table>

	<form action="index.php" method="post" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
		<div class="popup_content">
			<!-- New workflow for module or from template options -->
			<table class="slds-table slds-no-row-hover slds-table--cell-buffer">
				<!-- For module and from template options -->
				<tr>
					<!-- For module option -->
					<td class="dvtCellInfo">
						<span class="slds-radio">
							<input type="radio" name="source" id="forModule" value="from_module" checked="true" class="workflow_creation_mode"/>
							<label class="slds-radio__label" for="forModule">
								<span class="slds-radio--faux" style="margin-right: 0;"></span>
							</label>
							<span class="slds-form-element__label">{$MOD.LBL_FOR_MODULE}</span>
						</span>
					</td>
					<!-- From template option -->
					<td class="dvtCellInfo">
						<span class="slds-radio">
							<input type="radio" name="source" id="fromTemplate" value="from_template" class="workflow_creation_mode">
							<label class="slds-radio__label" for="fromTemplate">
								<span class="slds-radio--faux" style="margin-right: 0;"></span>
							</label>
							<span class="slds-form-element__label">{$MOD.LBL_FROM_TEMPLATE}</span>
						</span>
					</td>
				</tr>
				<!-- select module to create workflow -->
				<tr>
					<td class="dvtCellLabel" width='10%' nowrap="nowrap">{$MOD.LBL_CREATE_WORKFLOW_FOR}</td>
					<td class="dvtCellInfo">
						<select name="module_name" id="module_list" class="slds-select">
							{foreach item=moduleName from=$moduleNames}
								<option value="{$moduleName}" {if $moduleName eq $listModule}selected="selected"{/if}>
									{$moduleName|@getTranslatedString:$moduleName}
								</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr align="left" id="template_select_field" style="display:none;">
					<td class="dvtCellLabel">{$MOD.LBL_CHOOSE_A_TEMPLATE}</td>
					<td class="dvtCellInfo">
						<span id="template_list_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
						<span id="template_list_foundnone" style='display:none;'><b>{$MOD.LBL_NO_TEMPLATES}</b></span>
						<select id="template_list" name="template_id" class="small"></select>
					</td>
				</tr>
			</table>
			<input type="hidden" name="save_type" value="new" id="save_type_new">
			<input type="hidden" name="module" value="{$module->name}" id="save_module">
			<input type="hidden" name="action" value="editworkflow" id="save_action">
			<!-- Create & Cancel buttons -->
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
				<tr>
					<td align="center" style="padding: 5px;">
						<input type="submit" class="slds-button slds-button--small slds-button_success" value="{$APP.LBL_CREATE_BUTTON_LABEL}" name="save" id='new_workflow_popup_save'/>
						<input type="button" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " name="cancel" id='new_workflow_popup_cancel'/>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
<!--Done Popups-->

{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="big" nowrap="nowrap">
				<!-- Workflow List container -->
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<!-- Workflow Module Info -->
						<div class="slds-card__header slds-grid">
							<!-- Workflow Module Title -->
							<header class="slds-media slds-media--center slds-has-flexi-truncate">
								<div class="slds-media__body">
									<h2>
										<span class="slds-text-title--caps slds-truncate actionLabel prvPrfBigText">
											<strong><span id="module_info"></span></strong>
										</span>
									</h2>
								</div>
							</header>
							<!-- Select Modules combobox -->
							<div class="slds-no-flex">
								<div class="actionsContainer">
									<form action="index.php" method="post" accept-charset="utf-8" id="filter_modules" onsubmit="VtigerJS_DialogBox.block();" style="display: inline;">
										<b>{$MOD.LBL_SELECT_MODULE}: </b>
										<select class="importBox slds-select" name="list_module" id='pick_module' style="width: 65%;">
											<option value="All">{$APP.LBL_ALL}</option>
											<option value="All" disabled="disabled" >-----------------------------</option>
											{foreach item=moduleName from=$moduleNames}
											<option value="{$moduleName}" {if $moduleName eq $listModule}selected="selected"{/if}>
												{$moduleName|@getTranslatedString:$moduleName}
											</option>
											{/foreach}
										</select>
										<input type="hidden" name="module" value="{$module->name}">
										<input type="hidden" name="action" value="workflowlist">
									</form>
								</div>
							</div>
						</div>
						<!-- See workflows and create new workflow -->
						<div class="slds-card__body slds-card__body--inner">
							<div class="commentData">
								<!-- Status message & Create new Workflow button -->
								<table class="listTableTopButtons" width="100%" border="0" cellspacing="0" cellpadding="5">
									<tr>
										<!-- Status message -->
										<td class="small"> 
											<span id="status_message"></span>
										</td>
										<!-- Create new workflow button -->
										<td class="small" align="right">
											<input type="button" class="slds-button slds-button--small slds-button_success" value="{$MOD.LBL_NEW_WORKFLOW}" id='new_workflow'/>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</article>
				</div>
				<br/>
				<!-- Workflow data table -->
				<table class="slds-table slds-table--bordered listTable" id='expressionlist' >
					<thead>
						<tr>
							<!-- Name Header -->
							<th class="slds-text-title--caps" scope="col">
								<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
									{'Module'|@getTranslatedString:$moduleName}
								</span>
							</th>
							<!-- Description Header -->
							<th class="slds-text-title--caps" scope="col">
								<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
									{'Description'|@getTranslatedString:$moduleName}
								</span>
							</th>
							<!-- Tools Header -->
							<th class="slds-text-title--caps" scope="col">
								<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
									{'Tools'|@getTranslatedString:$moduleName}
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<!-- Module Workflow List-->
						{foreach item=workflow from=$workflows}
							<tr class="slds-hint-parent slds-line-height--reset">
								<!-- Module Name -->
								<th scope="row">
									<div class="slds-truncate">
										{$workflow->moduleName|@getTranslatedString:$workflow->moduleName}
									</div>
								</th>
								<!-- Module Description -->
								<th scope="row">
									<div class="slds-truncate">
										{$workflow->description}
									</div>
								</th>
								<!-- Module Tools (Edit, Delete) -->
								<th scope="row">
									<div class="slds-truncate">
										<!-- Edit Tool -->
										<a href="{$module->editWorkflowUrl($workflow->id)}">
											<img border="0" title="{'LBL_EDIT'|@getTranslatedString}" alt="{'LBL_EDIT'|@getTranslatedString}" style="cursor: pointer;" id="expressionlist_editlink_{$workflow->id}" src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
										</a>
										<!-- Delete Tool -->
										{if empty($workflow->defaultworkflow) && $workflow->executionConditionAsLabel() neq 'MANUAL'}
											<a href="{$module->deleteWorkflowUrl($workflow->id)}" onclick="return confirm('{$APP.SURE_TO_DELETE}');">
												<img border="0" title="{'LBL_DELETE'|@getTranslatedString}" alt="{'LBL_DELETE'|@getTranslatedString}" src="{'delete.gif'|@vtiger_imageurl:$THEME}" style="cursor: pointer;" id="expressionlist_deletelink_{$workflow->id}" />
											</a>
										{/if}
									</div>
								</th>
							</tr>
						{/foreach}
					</tbody>
				</table>

			</td>
		</tr>
	</table>

</div>
{include file='com_vtiger_workflow/Footer.tpl'}
