{include file='Buttons_List.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
	<header class="slds-modal__header slds-grid slds-grid_align-spread slds-grid_vertical-align-center">
		<h2 id="header43" class="slds-text-heading_medium">
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
		<form role="form" style="margin:0 100px;">
		<input type="hidden" name="module" value="Utilities">
		<input type="hidden" name="action" value="integration">
		<input type="hidden" name="_op" value="setconfigsclickhouse">
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
			<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-2">
				<label class="slds-form-element__label" for="username">{'clickhouse_username'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="clickhouse_username" name="clickhouse_username" class="slds-input" value="{$clickhouse_username}" />
				</div>
			</div>
			<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-2">
				<label class="slds-form-element__label" for="clickhouse_password">{'clickhouse_password'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<input type="text" id="clickhouse_password" name="clickhouse_password" class="slds-input" value="{$clickhouse_password}" />
				</div>
			</div>
		</div>
		<div class="slds-m-top_large">
			<button type="submit" name="btnchsave" class="slds-button slds-button_brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
		<div class="slds-grid slds-gutters">
			<div class="slds-form-element slds-m-top_small slds-col slds-size_1-of-1">
				<label class="slds-form-element__label" for="chquery">{'clickhouse_query'|@getTranslatedString:$MODULE}</label>
				<div class="slds-form-element__control">
					<textarea id="chquery" name="chquery" class="slds-input">{$CHQUERY}</textarea>
				</div>
			</div>
		</div>
		<div class="slds-m-top_large">
			<button type="submit" name="btnchquery" class="slds-button slds-button_brand">{'LBL_QUERY'|@getTranslatedString:'Users'}</button>
		</div>
		<div class="slds-m-top_large" id="chqueryresults">
		{foreach from=$CHQUERYRDO item=item}
			<div>{$item}</div>
		{/foreach}
		</div>
		</form>
	{/if}
	</div>
</div>
</section>