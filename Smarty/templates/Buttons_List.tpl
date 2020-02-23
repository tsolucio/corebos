{*
<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
-->*}
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
{if empty($Module_Popup_Edit)}
{if empty($CATEGORY)}
{assign var="CATEGORY" value=""}
{/if}
{if $CATEGORY eq 'Settings' || $MODULE eq 'Calendar4You'}
{assign var="action" value="index"}
{else}
{assign var="action" value="ListView"}
{/if}
{if !empty($isDetailView)}
{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
{assign var='TITLEPREFIX' value=$MOD_SEQ_ID}
{if $TITLEPREFIX eq ''} {assign var='TITLEPREFIX' value=$ID} {/if}
{assign var='MODULELABEL' value=$NAME}
{elseif !empty($isEditView)}
{if $OP_MODE eq 'edit_view'}
{assign var='TITLEPREFIX' value=$APP.LBL_EDITING}
{assign var='MODULELABEL' value=$NAME}
{elseif $OP_MODE eq 'create_view'}
{if $DUPLICATE neq 'true'}
{assign var='TITLEPREFIX' value=$APP.LBL_CREATING}
{assign var='MODULELABEL' value=$SINGLE_MOD|@getTranslatedString:$MODULE}
{else}
{assign var='TITLEPREFIX' value=$APP.LBL_DUPLICATING}
{assign var='MODULELABEL' value=$NAME}
{/if}
{assign var='UPDATEINFO' value=''}
{/if}
{else}
{assign var='MODULELABEL' value=$MODULE|@getTranslatedString:$MODULE}
{/if}
{assign var='MODULEICON' value=$MODULE|@getModuleIcon}
<div id="page-header-placeholder"></div>
<div id="page-header" class="slds-page-header slds-m-vertical_medium">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__figure">
					<a class="hdrLink" href="index.php?action={$action}&module={$MODULE}">
						<span class="{$MODULEICON.__ICONContainerClass}"
							title="{$MODULE|@getTranslatedString:$MODULE}">
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
								<span>{$MODULE|@getTranslatedString:$MODULE}</span>
								<span class="slds-page-header__title slds-truncate"
									title="{$MODULELABEL|@addslashes}">
									{if !empty($isDetailView) || !empty($isEditView)}
									<span class="slds-page-header__title slds-truncate"
										title="{$MODULELABEL|@addslashes}">
										<span class="slds-page-header__name-meta">[ {$TITLEPREFIX} ]</span>
										{$MODULELABEL|textlength_check:30}
									</span>
									{else}
									<a class="hdrLink"
										href="index.php?action={$action}&module={$MODULE}">{$MODULELABEL}</a>
									{/if}
								</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slds-page-header__col-actions">
			<div class="slds-page-header__controls">
				<div class="slds-page-header__control">
					<ul class="slds-button-group-list">
						<li class="slds-m-right_small slds-m-top_x-small">
							<span id="vtbusy_info" style="display:none;">
								<div role="status" class="slds-spinner slds-spinner_brand slds-spinner_x-small" style="position:relative; top:6px;">
									<div class="slds-spinner__dot-a"></div>
									<div class="slds-spinner__dot-b"></div>
								</div>
							</span>
						</li>
					{if $CHECK.CreateView eq 'yes' && ($MODULE eq 'Calendar' || $MODULE eq 'Calendar4You')}
						<li>
							<button class="slds-button slds-button_neutral" {$ADD_ONMOUSEOVER}>{$MOD.LBL_ADD_EVENT}</button>
						</li>
					{elseif $CHECK.CreateView eq 'yes' && $MODULE neq 'Emails' && $OP_MODE != 'create_view'}
						<li>
							<a
							class="slds-button slds-button_neutral"
							href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}"
							title="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}...">
								<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
								</svg>
								{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}
							</a>
						</li>
					{/if}
					{if $OP_MODE == 'edit_view' || $OP_MODE == 'create_view'}
						<li>
							<button
								class="slds-button slds-button_success"
								title="{$APP.LBL_SAVE_BUTTON_TITLE}"
								accessKey="{$APP.LBL_SAVE_BUTTON_KEY}"
								onclick="
									document.forms.EditView.action.value='Save';
									displaydeleted();
									{if isset($INV_CURRENCY_ID)}
										return validateInventory('{$MODULE}');
									{else}
										return formValidate();
									{/if}"
								type="submit"
								name="button">
									<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
									</svg>
									{$APP.LBL_SAVE_BUTTON_LABEL}
							</button>
						</li>
						<li>
							<button
								class="slds-button slds-button_destructive"
								title="{$APP.LBL_CANCEL_BUTTON_TITLE}"
								accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}"
								onclick="
									{if isset($smarty.request.Module_Popup_Edit)}window.close()
									{elseif isset($CANCELGO)}window.location.href='{$CANCELGO}'
									{else}window.history.back()
									{/if};"
								type="submit"
								name="button">
									<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reply"></use>
									</svg>
									{$APP.LBL_CANCEL_BUTTON_LABEL}
							</button>
						</li>
					{/if}
					{if isset($EDIT_PERMISSION) && $EDIT_PERMISSION eq 'yes'}
						<li>
							<button
								class="slds-button slds-button_neutral"
								title="{$APP.LBL_EDIT_BUTTON_TITLE}"
								accessKey="{$APP.LBL_EDIT_BUTTON_KEY}"
								onclick="
									DetailView.return_module.value='{$MODULE}';
									DetailView.return_action.value='DetailView';
									DetailView.return_id.value='{$ID}';
									DetailView.module.value='{$MODULE}';
									submitFormForAction('DetailView','EditView');"
								type="button"
								name="Edit">
									<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
									</svg>
									{$APP.LBL_EDIT_BUTTON_LABEL}
							</button>
						</li>
					{/if}
					{if ((isset($CREATE_PERMISSION) && $CREATE_PERMISSION eq 'permitted')
						|| (isset($EDIT_PERMISSION) && $EDIT_PERMISSION eq 'yes'))
						&& $MODULE neq 'Documents'
						&& $isDetailView}
						<li>
							<button
								class="slds-button slds-button_neutral"
								title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}"
								accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}"
								onclick="
									DetailView.return_module.value='{$MODULE}';
									DetailView.return_action.value='DetailView';
									DetailView.isDuplicate.value='true';
									DetailView.module.value='{$MODULE}';
									submitFormForAction('DetailView','EditView');"
								type="button"
								name="Duplicate">
									<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#file"></use>
									</svg>
									{$APP.LBL_DUPLICATE_BUTTON_LABEL}
							</button>
						</li>
					{/if}
					{if isset($DELETE) && $DELETE eq 'permitted'}
						<li>
							<button
								class="slds-button slds-button_neutral"
								title="{$APP.LBL_DELETE_BUTTON_TITLE}"
								accessKey="{$APP.LBL_DELETE_BUTTON_KEY}"
								onclick="
									DetailView.return_module.value='{$MODULE}';
									DetailView.return_action.value='index';
									{if $MODULE eq 'Accounts'}
										var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}'
									{else}
										var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}'
									{/if};
									submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);"
								type="button"
								name="Delete">
									<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
									</svg>
									{$APP.LBL_DELETE_BUTTON_LABEL}
							</button>
						</li>
					{/if}
					</ul>
					{* Buttons only for reports *}
					{if $MODULE eq 'Reports'}
					<div class="slds-button-group" role="group">
						<button
							class="slds-button slds-button_icon slds-button_icon-border-filled"
							aria-haspopup="true"
							onclick="gcurrepfolderid=0;fnvshobj(this,'reportLay');"
							title="{$MOD.LBL_CREATE_REPORT}...">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#record_create"></use>
								</svg>
								<span class="slds-assistive-text">
									{$MOD.LBL_CREATE_REPORT}...
								</span>
						</button>
						<button
							class="slds-button slds-button_icon slds-button_icon-border-filled"
							aria-haspopup="true"
							onclick="createrepFolder(this,'orgLay');"
							title="{$MOD.Create_New_Folder}...">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#opened_folder"></use>
								</svg>
								<span class="slds-assistive-text">
									{$MOD.Create_New_Folder}...
								</span>
						</button>
						<button
							class="slds-button slds-button_icon slds-button_icon-border-filled"
							aria-haspopup="true"
							onclick="fnvshobj(this,'folderLay');"
							title="{$MOD.Move_Reports}...">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#reassign"></use>
								</svg>
								<span class="slds-assistive-text">
									{$MOD.Move_Reports}...
								</span>
						</button>
						<button
							class="slds-button slds-button_icon slds-button_icon-border-filled"
							aria-haspopup="true"
							onclick="massDeleteReport();"
							title="{$MOD.LBL_DELETE_FOLDER}...">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
								</svg>
								<span class="slds-assistive-text">
									{$MOD.LBL_DELETE_FOLDER}...
								</span>
						</button>
					</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
	<div id="page-header-surplus">
		<div class="slds-page-header__row">
			<div class="slds-page-header__col-meta" style="min-width: 0;">
				<div class="slds-page-header__meta-text slds-grid">
					{if !empty($isDetailView) || !empty($isEditView)}
					<div class="slds-p-right_small">{$UPDATEINFO}</div>
					{/if}
					{assign var=ANNOUNCEMENT value=get_announcements()}
					{if $ANNOUNCEMENT}
					<style>
						#marquee span {
							display: inline-block;
							padding-left: 100%;
							animation: marquee {$ANNOUNCEMENT|count_characters / 3}s linear infinite;
						}
						#marquee span:hover {
							animation-play-state: paused
						}
						@keyframes marquee {
							0% {
							transform: translate(0, 0);
							}
							100% {
							transform: translate(-100%, 0);
							}
						}
					</style>
					<div class="slds-col slds-truncate" id="marquee">
						<span>{$ANNOUNCEMENT}</span>
					</div>
					{/if}
				</div>
			</div>
			<div class="slds-page-header__col-controls">
				<div class="slds-page-header__controls">
					<div class="slds-page-header__control">
						{if $ANNOUNCEMENT}
						<button
							class="slds-button slds-button_icon slds-button_icon-border-filled"
							aria-haspopup="true"
							style="transform: scale(-1,1); color: #d3451d;">
								<svg class="slds-button__icon" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#announcement"></use>
								</svg>
						</button>
						{/if}
						<div class="slds-button-group" role="group">
							{* Search button *}
							{if $CHECK.index eq 'yes'
								&& ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index')
								&& $MODULE neq 'Emails'
								&& $MODULE neq 'Calendar4You'
							}
								{$searchdisabled = false}
							{else}
								{$searchdisabled = true}
							{/if}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								{if $searchdisabled == true}disabled=""{/if}
								title="{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}..."
								onClick="searchshowhide('searchAcc','advSearch');mergehide('mergeDup')">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}...
									</span>
							</button>
							{* Calendar button *}
							{if $CALENDAR_DISPLAY eq 'true'}
								{$canusecalendar = true}
								{if $CHECK.Calendar != 'yes'}
									{$canusecalendar = false}
								{/if}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								{if $canusecalendar == false}disabled=""{/if}
								onclick="fnvshobj(this,'miniCal');getITSMiniCal('');"
								title="{$APP.LBL_CALENDAR_TITLE}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#monthlyview"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_CALENDAR_TITLE}
									</span>
							</button>
							{/if}
							{* World clock button *}
							{if $WORLD_CLOCK_DISPLAY eq 'true'}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								onClick="fnvshobj(this,'wclock');"
								title="{$APP.LBL_CLOCK_TITLE}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#world"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_CLOCK_TITLE}
									</span>
							</button>
							{/if}
							{* Import button *}
							{if $CHECK.Import eq 'yes'
								&& $MODULE neq 'Documents'
								&& $MODULE neq 'Calendar'
								&& $MODULE neq 'Calendar4You'
							}
							<a
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}"
								href="index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=index&parenttab={$CATEGORY}">
									<svg class="slds-button__icon slds-m-top_x-small" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#download"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}
									</span>
							</a>
							{elseif $CHECK.Import eq 'yes' && $MODULE eq 'Calendar'}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								onclick="fnvshobj(this,'CalImport');"
								title="{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#download"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_IMPORT} {$MODULE|getTranslatedString:$MODULE}
									</span>
							</button>
							{/if}
							{* Export Button *}
							{if $CHECK.Export eq 'yes' && $MODULE neq 'Calendar' && $MODULE neq 'Calendar4You'}
								{$exportbuttononclick = "return selectedRecords('{$MODULE}','{$CATEGORY}')"}
							{elseif $CHECK.Export eq 'yes' && $MODULE eq 'Calendar'}
								{$exportbuttononclick = "fnvshobj(this,'CalExport');"}
							{/if}
							{if isset($exportbuttononclick)}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								onclick="{$exportbuttononclick}"
								title="{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#upload"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_EXPORT} {$MODULE|getTranslatedString:$MODULE}
									</span>
							</button>
							{/if}
							{* Deduplicate button *}
							{if $CHECK.DuplicatesHandling eq 'yes'
								&& $MODULE neq 'Calendar4You'
								&& ($smarty.request.action eq 'ListView' || $smarty.request.action eq 'index')
							}
							<button
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								onclick="mergeshowhide('mergeDup');searchhide('searchAcc','advSearch');"
								title="{$APP.LBL_FIND_DUPLICATES}">
									<svg class="slds-button__icon" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#crossfilter"></use>
									</svg>
									<span class="slds-assistive-text">
										{$APP.LBL_FIND_DUPLICATES}
									</span>
							</button>
							{/if}
							{* Calendar4You stuff *}
							{if $MODULE eq 'Calendar4You'}
								{* Calendar settings button *}
								{if $MODE neq 'DetailView' && $MODE neq 'EditView' && $MODE neq 'RelatedList'}
								<button
									class="slds-button slds-button_icon slds-button_icon-border-filled"
									aria-haspopup="true"
									onclick="fnvshobj(this,'calSettings'); getITSCalSettings();"
									title="Settings">
										<svg class="slds-button__icon" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#metrics"></use>
										</svg>
										<span class="slds-assistive-text">Settings</span>
								</button>
								{/if}
								{* Tasks link *}
								<a
									class="slds-button slds-button_icon slds-button_icon-border-filled"
									aria-haspopup="true"
									title="{'Tasks'|getTranslatedString:$MODULE}"
									href="index.php?module=cbCalendar&action=index">
										<svg class="slds-button__icon slds-m-top_x-small" aria-hidden="true">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#task"></use>
										</svg>
										<span class="slds-assistive-text">
											{'Tasks'|getTranslatedString:$MODULE}
										</span>
								</a>
							{/if}
							{* General settings button *}
							{if $CHECK.moduleSettings eq 'yes'}
							<a
								class="slds-button slds-button_icon slds-button_icon-border-filled"
								aria-haspopup="true"
								title="{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}"
								href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings">
									<svg class="slds-button__icon slds-m-top_x-small" aria-hidden="true">
										<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#settings"></use>
									</svg>
									<span class="slds-assistive-text">
										{$MODULE|getTranslatedString:$MODULE} {$APP.LBL_SETTINGS}
									</span>
							</a>
							{/if}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{corebos_header}
{/if}