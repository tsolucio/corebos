<!-- Workflow list Header/Title -->
<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
	<tr class="slds-text-title--caps">
		<td style="padding: 0;">
			<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
				<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
					<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
						<!-- Image -->
						<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
							<div class="slds-media__figure slds-icon forceEntityIcon">
								<span class="photoContainer forceSocialPhoto">
									<div class="small roundedSquare forceEntityIcon">
										<span class="uiImage">
											<img src="{'settingsWorkflow.png'|@vtiger_imageurl:$THEME}">
										</span>
									</div>
								</span>
							</div>
						</div>
						<!-- Title & help Text -->
						<div class="slds-media__body">
							<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
								<!-- Title -->
								<span class="uiOutputText" style="width: 100%;">
									<b>
										<a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings">{'Settings'|@getTranslatedString:$MODULE_NAME}</a> >
										<a href="index.php?module={$module->name}&amp;action=workflowlist&amp;parenttab=Settings">{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}</a> > {$PAGE_NAME}
									</b>
								</span>
								<!-- Help text -->
								<span class="small">{$PAGE_TITLE}</span>
							</h1>
						</div>
						<!-- Last Scane text -->
						<div class="slds-no-flex">
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
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>