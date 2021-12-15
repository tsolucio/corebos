{if empty($MODULE)}
	{assign var='MODULE' value='com_vtiger_workflow'}
{/if}
{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
{assign var='MODULEICON' value=$MODULE|@getModuleIcon}
<div id="page-header-placeholder"></div>
<div id="page-header" class="slds-page-header slds-m-vertical_medium">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action=workflowlist&module={$MODULE}">
						<span class="{$MODULEICON.__ICONContainerClass}" title="{$MODULE|@getTranslatedString:$MODULE}">
							<svg class="slds-icon slds-page-header__icon" id="page-header-icon" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink"
									xlink:href="include/LD/assets/icons/{$MODULEICON.__ICONLibrary}-sprite/svg/symbols.svg#{$MODULEICON.__ICONName}" />
							</svg>
							<span class="slds-assistive-text">{$MODULELABEL}</span>
						</span>
					</a>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span>{$MODULELABEL}</span>
								<span class="slds-page-header__title slds-truncate" title="{$MODULELABEL|@addslashes}">
									{if !empty($isDetailView) || !empty($isEditView)}
									<span class="slds-page-header__title slds-truncate" title="{$MODULELABEL|@addslashes}">
										<span class="slds-page-header__name-meta">[ {$TITLEPREFIX} ]</span>
										{$MODULELABEL|textlength_check:30}{$MODULE}
									</span>
									{else}
									<a class="hdrLink" href="index.php?action=workflowlist&module={$MODULE}">{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}</a>
									{/if}
								</span>
							</h1>
							<p class="slds-page-header__row slds-page-header__name-meta">
								{if isset($CRON_TASK)}
									<b>
									{if $CRON_TASK->isDisabled() }{'LBL_DISABLED'|@getTranslatedString:$MODULE_NAME}{/if}
									{if $CRON_TASK->isRunning() }{'LBL_RUNNING'|@getTranslatedString:$MODULE_NAME}{/if}
									{if $CRON_TASK->isEnabled()}
										{if $CRON_TASK->hadTimedout()}
											{'LBL_LAST_SCAN_TIMED_OUT'|@getTranslatedString:$MODULE_NAME}.
										{elseif $CRON_TASK->getLastEndDateTime() neq ''}
											{'LBL_LAST_SCAN_AT'|@getTranslatedString:$MODULE_NAME}
											{$CRON_TASK->getLastEndDateTime()}
											&
											{'LBL_TIME_TAKEN'|@getTranslatedString:$MODULE_NAME}:
											{$CRON_TASK->getTimeDiff()}
											{'LBL_SHORT_SECONDS'|@getTranslatedString:$MODULE_NAME}
										{else}
										{/if}
									{/if}
									</b>
								{/if}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-page-header__col-actions">
			{if isset($show)}
				{if $show=='wflist'}
				<div class="slds-grid slds-gutters slds-m-around_xxx-small">
					<div class="slds-col">
						<button class="slds-button slds-button_success" id='new_workflow'>{$MOD.LBL_NEW_WORKFLOW}</button>
						{include file='com_vtiger_workflow/ActionMenu.tpl'}
					</div>
				</div>
				{elseif $show=='wfedit'}
				<div class="slds-grid slds-gutters slds-m-around_xxx-small">
					<div class="slds-col">
						{if $saveType eq "edit"}
						<button class="slds-button slds-button_success" type="button" id='new_template'>
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
							</svg>
							{$MOD.LBL_NEW_TEMPLATE}
						</button>
						{/if}
						<button class="slds-button slds-button_success" type="button" id='save_submit' style="display:none;" {if $workflow->executionConditionAsLabel() eq 'MANUAL'} onclick="return confirm_changing()"{/if}>
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
							</svg>
							{$APP.LBL_SAVE_LABEL}
						</button>
						<script type="text/javascript">
							function confirm_changing() {
								if (document.getElementById('save_description').value != document.getElementById('hidden_description').value) {
									alert('{$MOD.LBL_WF_MANUAL_WARNING}');
								}
							}
						</script>
						<button class="slds-button slds-button_destructive" type="button" onclick="window.location.href='index.php?module=com_vtiger_workflow&action=workflowlist'">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use>
							</svg>
							{$APP.LBL_CANCEL_BUTTON_LABEL}
						</button>
					</div>
				</div>
				{elseif $show=='tkedit'}
				<div class="slds-grid slds-gutters slds-m-around_xxx-small">
					<div class="slds-col">
						<button class="slds-button slds-button_success" type="button" id='save'>
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
							</svg>
							{$APP.LBL_SAVE_BUTTON_LABEL}
						</button>
						<button class="slds-button slds-button_destructive" type="button" id='edittask_cancel_button'>
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use>
							</svg>
							{$APP.LBL_CANCEL_BUTTON_LABEL}
						</button>
					</div>
				</div>
				{/if}
			{/if}
		</div>
		<div id="page-header-surplus">
		</div>
	</div>
</div>
