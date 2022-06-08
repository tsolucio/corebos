<script src="modules/{$module->name}/resources/jquery.timepicker.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/edittaskscript.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
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

{include file='com_vtiger_workflow/ModuleTitle.tpl' show='tkedit'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div id="view" class="workflows-edit slds-modal__container slds-p-around_none slds-card">
	<form name="new_task" id="new_task_form" method="post" onsubmit="VtigerJS_DialogBox.block();">
		<!-- Heading *Summary -->
		<div class="slds-page-header">
			<div class="slds-page-header__row">
				<div class="slds-grid slds-gutters">
					<div class="slds-col slds-size_1-of-1">
						<h1>
							<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_SUMMARY}">
								<span class="slds-tabs__left-icon">
									<span class="slds-icon_container" title="{$MOD.LBL_SUMMARY}">
										<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#summary"></use>
										</svg>
									</span>
								</span>
								{$MOD.LBL_SUMMARY}
							</span>
						</h1>
					</div>
				</div>
			</div>
		</div>
		<!-- Task Title -->
		<div class="slds-grid slds-grid_vertical-align-center">
			<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
				<span> <b> <abbr class="slds-required" title="required">* </abbr> {$MOD.LBL_TASK_TITLE} </b> </span>
			</div>

			<div class="slds-col slds-size_9-of-12 slds-p-around_x-small">
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<input type="text" class="slds-input" name="summary" value="{$task->summary}" id="save_summary" />
					</div>
				</div>
			</div>
		</div>
		<!-- Parent Workflow -->
		<div class="slds-grid slds-grid_vertical-align-center">
			<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
				<span> <b> {$MOD.LBL_PARENT_WORKFLOW} </b> </span>
			</div>
			<div class="slds-col slds-size_9-of-12 slds-p-around_x-small">
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						{$workflow->description}
						<input type="hidden" name="workflow_id" value="{$workflow->id}" id="save_workflow_id" />
					</div>
				</div>
			</div>
		</div>
		<!-- Label Status -->
		<div class="slds-grid slds-grid_vertical-align-center">
			<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
				<span> <b> {$MOD.LBL_STATUS} </b> </span>
			</div>
			<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-select_container">
								<select name="active" class="slds-select slds-page-header__meta-text">
									<option value="true">{$MOD.LBL_ACTIVE}</option>
									<option value="false" {if not $task->active}selected{/if}>{$MOD.LBL_INACTIVE}</option>
								</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-grid slds-grid_vertical-align-center slds-p-horizontal_xx-large slds-border_top slds-border_bottom">
			<!-- Execute the task after some delay -->
			<div class="slds-col slds-2-of-3 slds-text-align_center slds-p-around_x-small">
				<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
					<div class="slds-col slds-size_1-of-3 slds-text-align_center">
						<div class="slds-form-element">
							<div class="slds-form-element__control">
								<div class="slds-checkbox">
									<input type="checkbox" name="check_select_date" value="" id="check_select_date" {if !empty($trigger)}checked{/if} />
									<label class="slds-checkbox__label" for="check_select_date">
										<span class="slds-checkbox_faux"></span>
										<span class="slds-form-element__label"> {$MOD.MSG_EXECUTE_TASK_DELAY} </span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="slds-col slds-size_2-of-3 slds-text-align_center" id="select_date" {if empty($trigger)}style="display:none;"{/if}>
						<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
							<!-- Date/Days_Hours -->
							<div class="slds-col slds-size_1-of-3 slds-text-align_center">
								<div class="slds-form-element">
									<div class="slds-form-element__control slds-input-has-fixed-addon">
										<input type="number" name="select_date_days" value="{if isset($trigger.days)}{$trigger.days}{/if}" id="select_date_days" class="slds-input">
										<input type="number" name="select_date_hours" value="{if isset($trigger.hours)}{$trigger.hours}{/if}{if isset($trigger.mins)}{$trigger.mins}{/if}" id="select_date_hours" class="slds-input">
										<select class="slds-select slds-page-header__meta-text" name="select_days_hours_option" id="select_days_hours_option" onselect="evaluatedatehoursoptions();">
											<option {if isset($trigger.days)}selected{/if} value='days'>{$MOD.LBL_DAYS}</option>
											<option {if isset($trigger.hours)}selected{/if} value='hours'>{$MOD.LBL_HOURS}</option>
											<option {if isset($trigger.mins)}selected{/if} value='mins'>{'LBL_MINUTES'|@getTranslatedString:'CronTasks'}</option>
										</select>
									</div>
								</div>
							</div>
							<!-- After/Before -->
							<div class="slds-col slds-size_1-of-3 slds-text-align_center">
								<div class="slds-form-element">
									<div class="slds-form-element__control">
										<div class="slds-select_container">
											<select class="slds-select slds-page-header__meta-text" name="select_date_direction">
												<option {if isset($trigger.direction) && $trigger.direction eq 'after'}selected{/if} value='after'>{$MOD.LBL_AFTER}</option>
												<option {if isset($trigger.direction) && $trigger.direction eq 'before'}selected{/if} value='before'>{$MOD.LBL_BEFORE}</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<!-- Created/Modified Time -->
							<div class="slds-col slds-size_1-of-3 slds-text-align_center">
								<div class="slds-form-element">
									<div class="slds-form-element__control">
										<div class="slds-select_container">
											<select class="slds-select slds-page-header__meta-text" name="select_date_field">
												{foreach key=name item=label from=$dateFields}
													<option value='{$name}' {if isset($trigger.field) && $trigger.field eq $name}selected{/if}>
														{$label}
													</option>
												{/foreach}
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Evaluate conditions on delayed execution -->
			<div class="slds-col slds-size_1-of-3 slds-text-align_center slds-p-around_x-small">
					<div class="slds-form-element">
						<div class="slds-form-element__control">
							<div class="slds-checkbox">
								<input type="checkbox" name="reevaluate" id="reevaluate" {if isset($task->reevaluate) && $task->reevaluate eq 1}checked{/if}>
								<label class="slds-checkbox__label" for="reevaluate">
									<span class="slds-checkbox_faux"></span>
									<span class="slds-form-element__label"> {$MOD.LBL_REEVALCONDITIONS} </span>
								</label>
							</div>
						</div>
					</div>
			</div>
		</div>
		<!-- Conditions -->
		<div class="slds-grid slds-gutters">
			<div class="slds-col">
				{include file='com_vtiger_workflow/ListConditions.tpl' RecordSetTab=0}
			</div>
		</div>
		<div class="slds-page-header">
			<div class="slds-grid slds-gutters">
				<div class="slds-col slds-size_1-of-1">
					<h1>
						<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_TASK_OPERATIONS}">
							<span class="slds-tabs__left-icon">
								<span class="slds-icon_container" title="{$MOD.LBL_TASK_OPERATIONS}">
									<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#task"></use>
									</svg>
								</span>
							</span>
							{$MOD.LBL_TASK_OPERATIONS}
						</span>
					</h1>
				</div>
			</div>
		</div>

{include file="$taskTemplate"}
		<input type="hidden" name="save_type" value="{$saveType}" id="save_save_type">
{if $edit}
		<input type="hidden" name="task_id" value="{$task->id}" id="save_task_id">
{/if}
		<input type="hidden" name="task_type" value="{$taskType}" id="save_task_type">
		<input type="hidden" name="action" value="savetask">
		<input type="hidden" name="module" value="{$module->name}">
		<input type="hidden" name="return_url" value="{$returnUrl}">
		<input type="hidden" name="conditions" value="" id="save_conditions_json"/>
	</form>
</div>
</section>
<div id="dump" style="display:None;"></div>