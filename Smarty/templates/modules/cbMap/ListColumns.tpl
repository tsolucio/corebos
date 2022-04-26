<script src="modules/cbMap/generatemap/ListColumns.js"></script>
<script>
	LCInstance.MapID = {$MapID};
	LCInstance.MapFields = '{$MapFields|json_encode}';
	LCInstance.ModuleName = '{$module}';
	LCInstance.MapName = '{$mapname}';
</script>
<article class="slds-card">
	<div class="slds-card__header slds-grid">
		<header class="slds-media slds-media_center slds-has-flexi-truncate">
		<div class="slds-media__figure">
			<span class="slds-icon_container slds-icon-standard-account" title="action_list_component">
				<svg class="slds-icon slds-icon_small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#action_list_component"></use>
				</svg>
				<span class="slds-assistive-text">action_list_component</span>
			</span>
		</div>
		<div class="slds-media__body">
			<h2 class="slds-card__header-title">
				<a href="#" class="slds-card__header-link slds-truncate" title="Accounts">
					<span>Select columns to show in Mass Create Grid</span>
				</a>
			</h2>
		</div>
		</header>
	</div>
	<hr>
	<div class="slds-card__body slds-card__body_inner" style="height: 80vh">
		<div class="slds-grid slds-wrap">
			{foreach from=$MapFields item=$i}
			{assign var='checked' value=''}	
			{if $i['active'] eq 1}
				{assign var='checked' value='checked'}
			{/if}
			<div class="slds-col slds-size_3-of-12">
				<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-checkbox">
							<input type="checkbox" name="grid-fields" id="checkbox-{$i['name']}" value="checkbox-{$i['name']}" {$checked}/>
							<label class="slds-checkbox__label" for="checkbox-{$i['name']}">
								<span class="slds-checkbox_faux"></span>
								<span class="slds-form-element__label">{$i['name']}</span>
							</label>
						</div>
					</div>
				</div>
			</div>
			{/foreach}
		</div>
	</div>
	<footer class="slds-card__footer">
		<button type="button" class="slds-button slds-button_brand" onclick="LCInstance.SaveMap()">Save Map
		</button>
	</footer>
</article>
{include file='Components/ComponentsCSS.tpl'}
{include file='Components/ComponentsJS.tpl'}