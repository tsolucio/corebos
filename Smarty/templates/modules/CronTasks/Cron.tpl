{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
<script type="text/javascript" src="modules/CronTasks/CronTasks.js"></script>
{assign var="MODULE" value='CronTasks'}
{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
<div id="page-header-placeholder"></div>
<div id="page-header" class="slds-page-header slds-m-vertical_medium">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action=ListCronJobs&module={$MODULE}">
						<span class="slds-icon_container slds-icon-standard-account" title="{$MODULE|@getTranslatedString:$MODULE}">
							<svg class="slds-icon slds-page-header__icon" id="page-header-icon" aria-hidden="true">
								<use xmlns:xlink="http://www.w3.org/1999/xlink"
									xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clock" />
							</svg>
							<span class="slds-assistive-text">{$MOD.LBL_SCHEDULER}</span>
						</span>
					</a>
				</div>
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span>{$MODULE|@getTranslatedString:$MODULE}</span>
								<span class="slds-page-header__title slds-truncate" title="{$MODULELABEL|@addslashes}">
									{if !empty($isDetailView) || !empty($isEditView)}
									<span class="slds-page-header__title slds-truncate" title="{$MODULELABEL|@addslashes}">
										<span class="slds-page-header__name-meta">[ {$TITLEPREFIX} ]</span>
										{$MODULELABEL|textlength_check:30}
									</span>
									{else}
									<a class="hdrLink"
										href="index.php?action=ListCronJobs&module={$MODULE}">{$MOD.LBL_SCHEDULER}</a>
									{/if}
								</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-page-header__col-actions">
		</div>
		<div id="page-header-surplus">
		</div>
	</div>
</div>
<div id="notifycontents">
{include file='modules/CronTasks/CronContents.tpl'}
</div>
<div id="editdiv" style="display:none;position:absolute;width:450px;"></div>
