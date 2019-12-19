<div class="slds-page-header__row">
	<div class="slds-p-right_medium">
		<div class="slds-media">
			<div class="slds-media__figure">
				<a class="hdrLink" href="index.php?action=workflowlist&module={$module->name}">
				<span class="slds-icon_container slds-icon-standard-user" title="{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}">
				<img width="48" height="48" border="0" src="{'settingsWorkflow.png'|@vtiger_imageurl:$THEME}"/>
				<span class="slds-assistive-text">{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}</span>
				</span>
				</a>
			</div>
			<div class="slds-media__body">
				<div class="slds-page-header__name">
					<div class="slds-page-header__name-title">
						<span class="slds-page-header__title slds-truncate" title="{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}">
							<span class="slds-page-header__title slds-truncate" title="{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}">
								<b>
								{if $ISADMIN}
								<a href="index.php?module=Settings&action=index&parenttab=Settings">
								{/if}
								{'Settings'|@getTranslatedString:$MODULE_NAME}
								{if $ISADMIN}
								</a>
								{/if}
								&nbsp;>&nbsp;
								<a href="index.php?module={$module->name}&action=workflowlist">{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}</a>
								</b>
							</span>
						</span>
					</div>
				</div>
				<p class="slds-page-header__name-meta">
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
<br>
