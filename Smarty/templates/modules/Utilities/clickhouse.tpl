<script src="./include/js/ListViewRenderes.js"></script>
{include file='Buttons_List.tpl'}

<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-modal="true" style="margin-top: 10px">
	<div class="slds-modal__container slds-p-around_none slds-modal__header" style="margin-top: 10px">
		<div class="slds-tabs_default">
			<ul class="slds-tabs_default__nav" role="tablist">
				<li class="slds-tabs_default__item slds-is-active" title="{'Settings'|getTranslatedString:'Settings'}" id="tab-settings" role="Settings">
					<a class="slds-tabs_default__link" onclick="showChTab('settings')" role="tab" tabindex="0" aria-selected="true" aria-controls="tab-default-1">
						<span class="slds-tabs__left-icon">
							<span class="slds-icon_container slds-icon-standard-opportunity" title="{'Settings'|getTranslatedString:'Settings'}">
								<svg class="slds-icon slds-icon_small" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#settings"></use>
								</svg>
							</span>
						</span>{'Settings'|getTranslatedString:'Settings'}
					</a>
				</li>
				<li class="slds-tabs_default__item" title="{'Tables'|getTranslatedString:'Settings'}" id="tab-tables" role="table">
					<a class="slds-tabs_default__link" onclick="showChTab('tables')" role="tab" tabindex="-1" aria-selected="false" aria-controls="tab-default-2">
						<span class="slds-tabs__left-icon">
							<span class="slds-icon_container slds-icon-standard-case" title="{'Tables'|getTranslatedString:'Settings'}">
								<svg class="slds-icon slds-icon_small" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#case"></use>
								</svg>
							</span>
						</span>{'Tables'|getTranslatedString:'Settings'}
					</a>
				</li>
			</ul>
			<div id="tab-data-settings" class="slds-tabs_default__content slds-show" role="tabpanel">
		<div class="slds-modal__container slds-p-around_none">
			<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
				<h2 class="slds-text-heading_medium">
				<div class="slds-media__figure">
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user slds-m-right_small">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
					</svg>
					{$TITLE_MESSAGE}
				</div>
				</h2>
			</header>
			<div class="slds-modal__content slds-app-launcher__content slds-p-around_medium">
			{if $ISADMIN}
				<form role="form" style="margin: 0 100px">
					<input type="hidden" name="module" value="Utilities" />
					<input type="hidden" name="action" value="integration" />
					<input type="hidden" name="_op" value="setconfigsclickhouse" />
				<div class="slds-form-element">
					<label class="slds-checkbox_toggle slds-grid">
					<span class="slds-form-element__label slds-m-bottom_none">{'clickhouse_active'|@getTranslatedString:$MODULE}</span>
					<input type="checkbox" name="clickhouse_active" aria-describedby="toggle-desc" {if $isActive}checked{/if} />
					<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString:'Settings'}</span>
						<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString:'Settings'}</span>
					</span>
					</label>
				</div>
				<div class="slds-grid slds-gutters">
					<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-3">
						<label class="slds-form-element__label" for="clickhouse_host">{'clickhouse_host'|@getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input type="text" id="clickhouse_host" name="clickhouse_host" class="slds-input" value="{$clickhouse_host}" />
						</div>
					</div>
					<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-3">
						<label class="slds-form-element__label" for="clickhouse_port">{'clickhouse_port'|@getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input type="text" id="clickhouse_port" name="clickhouse_port" class="slds-input" value="{$clickhouse_port}" />
						</div>
					</div>
					<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-3">
						<label class="slds-form-element__label" for="database">{'clickhouse_database'|@getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input type="text" id="clickhouse_database" name="clickhouse_database" class="slds-input" value="{$clickhouse_database}" />
						</div>
					</div>
				</div>
				<div class="slds-grid slds-gutters">
					<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-3">
						<label class="slds-form-element__label" for="username">{'clickhouse_username'|@getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input type="text" id="clickhouse_username" name="clickhouse_username" class="slds-input" value="{$clickhouse_username}" />
						</div>
					</div>
					<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-3">
						<label class="slds-form-element__label" for="clickhouse_password">{'clickhouse_password'|@getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input type="text" id="clickhouse_password" name="clickhouse_password" class="slds-input" value="{$clickhouse_password}" />
						</div>
					</div>
					<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-3">
						<label class="slds-form-element__label" for="clickhouse_webhook_secret">{'clickhouse_webhook_secret'|@getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input type="text" id="clickhouse_webhook_secret" name="clickhouse_webhook_secret" class="slds-input" value="{$clickhouse_webhook_secret}" />
						</div>
					</div>
				</div>
				<div class="slds-m-top_large">
					<button type="submit" name="btnchsave" class="slds-button slds-button_brand">
						{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}
					</button>
				</div>
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="chquery">{'clickhouse_query'|@getTranslatedString:$MODULE}</label>
					<div class="slds-form-element__control">
						<textarea id="chquery" name="chquery" class="slds-textarea">{$CHQUERY}</textarea>
					</div>
				</div>
				<div class="slds-m-top_large">
					<button type="submit" name="btnchquery" class="slds-button slds-button_brand">
						{'LBL_QUERY'|@getTranslatedString:'Users'}
					</button>
				</div>
				<div class="slds-m-top_large" id="chqueryresults">
					{foreach from=$CHQUERYRDO item=item}
					<div style="text-align: left">{$item}</div>
					{/foreach}
				</div>
				</form>
			{/if}
			</div>
		</div>
		</div>
			<div id="tab-data-tables" class="slds-tabs_default__content slds-hide" role="tabpanel">
				<div class="slds-modal__container slds-p-around_none">
					<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
						<h2 class="slds-text-body_regular">
							<a class="slds-button slds-button_neutral" onclick="addChRow()">{'Add Table'|getTranslatedString:'Settings'}</a>
						</h2>
					</header>
					<div id="chgrid"></div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
$.getScript('modules/Utilities/Utilities.js', function () {});
</script>
