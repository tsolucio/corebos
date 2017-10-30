{include file='com_vtiger_workflow/Header.tpl'}
<script src="modules/{$module->name}/resources/jquery.timepicker.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/json2.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/edittaskscript.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/editworkflowscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
	var returnUrl = '{$returnUrl}';
	var validator;
	edittaskscript(jQuery);
{if !empty($task->test)}
	var conditions = JSON.parse('{$task->test|@addslashes}');
{else}
	var conditions = null;
{/if}
	editworkflowscript(jQuery, conditions);
</script>

<!--Error message box popup-->
{include file='com_vtiger_workflow/ErrorMessageBox.tpl'}
<!--Done popups-->

{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<form name="new_task" id="new_task_form" method="post" onsubmit="VtigerJS_DialogBox.block();">

		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td>

					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<!-- Title -->
								<header class="slds-media slds-media--center slds-has-flexi-truncate">
									<div class="slds-media__body">
										<h2>
											<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
												<strong>{$MOD.LBL_SUMMARY}</strong>
											</span>
										</h2>
									</div>
								</header>
								<!-- Button -->
								<div class="slds-no-flex">
									<input type="submit" name="{$APP.LBL_SAVE_LABEL}" class="slds-button slds-button--small slds-button_success" value="{$APP.LBL_SAVE_BUTTON_LABEL}" id="save">
									<input type="button" id="edittask_cancel_button" class="slds-button slds-button--small slds-button--destructive" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
								</div>
							</div>
						</article>
					</div>

					<div class="slds-truncate">
						<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table edittask-summary-table">
							<tr>
								<td class="dvtCellLabel" align=right width=25% nowrap="nowrap"><b><font color="red">*</font> {$MOD.LBL_TASK_TITLE}</b></td>
								<td class="dvtCellInfo" align="left" ><input type="text" class="slds-input" name="summary" value="{$task->summary}" id="save_summary" style="width: 100%;"></td>
							</tr>
							<tr>
								<td class="dvtCellLabel" align=right width=25% nowrap="nowrap"><b>{$MOD.LBL_PARENT_WORKFLOW}</b></td>
								<td class="dvtCellInfo" align="left">
									{$workflow->description}
									<input type="hidden" name="workflow_id" value="{$workflow->id}" id="save_workflow_id">
								</td>
							</tr>
							<tr>
								<td class="dvtCellLabel" align=right width=25% nowrap="nowrap"><b>{$MOD.LBL_STATUS}</b></td>
								<td class="dvtCellInfo" align="left">
									<select name="active" class="slds-select">
										<option value="true">{$MOD.LBL_ACTIVE}</option>
										<option value="false" {if not $task->active}selected{/if}>{$MOD.LBL_INACTIVE}</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="dvtCellLabel" width="25%" nowrap="nowrap">
									<span class="slds-checkbox">
										<input type="checkbox" name="check_select_date" value="" id="check_select_date" {if !empty($trigger)}checked{/if}>
										<label class="slds-checkbox__label" for="check_select_date">
											<span class="slds-checkbox--faux"></span>
										</label>
										<span class="">
											<b>{$MOD.MSG_EXECUTE_TASK_DELAY}</b>
										</span>
									</span>
								</td>
								<td class="dvtCellInfo" style="padding: .1rem .5rem;">
									<div id="select_date" {if empty($trigger)}style="display:none;"{/if}>
										<input type="text" name="select_date_days" value="{if isset($trigger.days)}{$trigger.days}{/if}" id="select_date_days" class="slds-input">
										&nbsp;
										days
										&nbsp;
										<select name="select_date_direction" class="slds-select" style="width: 15%">
											<option {if isset($trigger.direction) && $trigger.direction eq 'after'}selected{/if} value='after'>{$MOD.LBL_AFTER}</option>
											<option {if isset($trigger.direction) && $trigger.direction eq 'before'}selected{/if} value='before'>{$MOD.LBL_BEFORE}</option>
										</select>
										&nbsp;
										<select name="select_date_field" class="slds-select" style="width: 20%;">
											{foreach key=name item=label from=$dateFields}
												<option value='{$name}' {if isset($trigger.field) && $trigger.field eq $name}selected{/if}>
													{$label}
												</option>
											{/foreach}
										</select>
									</div>
								</td>
							</tr>
						</table>

					</div>

				</td>
			</tr>
		</table>

		<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
			<tr>
				<td width='100%' nowrap="nowrap">
					{include file='com_vtiger_workflow/ListConditions.tpl' showreeval='true'}
				</td>
			</tr>
		</table>

		<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
			<tr class="blockStyleCss">
				<td valign="top">
					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<!-- Title -->
								<header class="slds-media slds-media--center slds-has-flexi-truncate">
									<div class="slds-media__body">
										<h2>
											<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
												<strong>{$MOD.LBL_TASK_OPERATIONS}</strong>
											</span>
										</h2>
									</div>
								</header>
							</div>
						</article>
					</div>

						{include file="$taskTemplate"}
						<input type="hidden" name="save_type" value="{$saveType}" id="save_save_type">
						{if $edit}
							<input type="hidden" name="task_id" value="{$task->id}" id="save_task_id">
						{/if}
						<input type="hidden" name="task_type" value="{$taskType}" id="save_task_type">
						<input type="hidden" name="action" value="savetask" id="save_action">
						<input type="hidden" name="module" value="{$module->name}" id="save_module">
						<input type="hidden" name="return_url" value="{$returnUrl}" id="save_return_url">
						<input type="hidden" name="conditions" value="" id="save_conditions_json"/>

				</td>
			</tr>
		</table>

	</form>
</div>
<div id="dump" style="display:None;"></div>
{include file='com_vtiger_workflow/Footer.tpl'}
