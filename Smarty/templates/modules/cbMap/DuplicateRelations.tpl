<script src="include/Webservices/WSClientp.js" type="text/javascript" charset="utf-8"></script>
<input type="hidden" name="MapID" value="{$MapID}" id="MapID">
<input type="hidden" name="MapName" id="MapName" value="{$NameOFMap}">
<table class="slds-table slds-no-row-hover slds-table-moz map-generator-table">
	<tbody>
		<tr id="DivObjectID">
			<td class="detailViewContainer" valign="top">
				<div>
					<article class="slds-card" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media_center slds-has-flexi-truncate">
								<h1 id="mapNameLabel" class="slds-page-header__title slds-m-right_small slds-truncate">
									{if $NameOFMap neq ''} {$NameOFMap} {/if}
								</h1>
								<p class="slds-text-heading_label slds-line-height_reset">{$MapFields.maptype|@getTranslatedString:$MODULE}</p>
							</header>
							<div class="slds-no-flex">
								<div class="slds-section-title_divider">
									<button class="slds-button slds-button_small slds-button_neutral" id="SaveAsButton" onclick="saveModuleMapAction();">{'LBL_SAVE_LABEL'|@getTranslatedString}</button>
								</div>
							</div>
						</div>
					</article>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div class="slds-p-around_x-small slds-grid slds-gutters">
<div class="slds-col  slds-size_1-of-3 slds-form-element slds-text-align_left">
	<legend class="slds-form-element__legend slds-form-element__label">{'LBL_MODULE'|@getTranslatedString:'cbMap'}</legend>
	<div class="slds-form-element__control">
		<div class="slds-select_container">
			<select id="tmodule" required name="tmodule" class="slds-select" onchange="selectModule(this.value)">
				{foreach item=arr from=$MODULES}
					<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
				{/foreach}
			</select>
		</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-3">
<fieldset class="slds-form-element">
<label class="slds-checkbox_toggle slds-grid">
	<span class="slds-form-element__label slds-m-bottom_none slds-col slds-size_2-of-3">&nbsp;{'DuplicateDirectRelations'|@getTranslatedString:'cbMap'}</span>
	<input type="checkbox" name="DuplicateDirectRelations" aria-describedby="{'DuplicateDirectRelations'|@getTranslatedString:'cbMap'}" {if $DuplicateDirectRelations}checked{/if} />
	<span id="DuplicateDirectRelations" class="slds-checkbox_faux_container slds-col slds-size_1-of-3" aria-live="assertive">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-checkbox_on"></span>
		<span class="slds-checkbox_off"></span>
	</span>
</label>
</fieldset>
</div>
<div class="slds-col slds-size_1-of-3"></div>
</div>

<div class="slds-p-around_x-small slds-form-element">

	<div class="slds-p-around_x-small slds-grid slds-gutters slds-grid_vertical">
		<div class="slds-col slds-size_1-of-2 slds-form-element">
			<label class="slds-form-element__label" for="params">{'Relations'|@getTranslatedString:'cbMap'}</label>
			<div class="slds-form-element" role="group" aria-labelledby="{'Relations'|@getTranslatedString:'cbMap'}">
				<div class="slds-form-element__control">
				<div class="slds-dueling-list">
				<div class="slds-dueling-list__column">
					<div class="slds-dueling-list__options" style="height:22rem;width:22rem;">
					<ul id="notselectedrelations" aria-describedby="option-drag-label" aria-labelledby="{'Relations'|@getTranslatedString:'cbMap'}" aria-multiselectable="true" class="slds-listbox slds-listbox_vertical" role="listbox">
					</ul>
					</div>
				</div>
				<div class="slds-dueling-list__column">
				<button class="slds-button slds-button_icon slds-button_icon-container" onclick="dplfindmoveli('notselectedrelations', 'selectedrelations');">
				<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#right"></use>
				</svg>
				</button>
				<button class="slds-button slds-button_icon slds-button_icon-container" onclick="dplfindmoveli('selectedrelations', 'notselectedrelations');">
				<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#left"></use>
				</svg>
				</button>
				</div>
				<div class="slds-dueling-list__column">
				<div class="slds-dueling-list__options" style="height:22rem;width:22rem;">
				<ul id="selectedrelations" aria-describedby="option-drag-label" aria-multiselectable="true" class="slds-listbox slds-listbox_vertical" role="listbox">
				{foreach item=rl from=$RelatedModules}
				<li role="presentation" class="slds-listbox__item" onclick="dplsetSelected('dplselectedrelations', this);" ondblclick="dplchangelist('dplselectedrelations', this)">
					<div name="dplselectedrelations" class="slds-listbox__option slds-listbox__option_plain slds-media slds-media_small slds-media_inline" aria-selected="false" draggable="true" role="option">
					<span class="slds-media__body">
					<span name="dplspan" class="slds-truncate" title="{$rl[0]}">{$rl[0]} ({$rl[1]})</span>
					</span>
					</div>
				</li>
				{/foreach}
				</ul>
				</div>
				</div>
				</div>
				</div>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-2 slds-form-element">&nbsp;</div>
	</div>

</div>
<script>
var selectedmodules = {json_encode($RelatedModules)};
var mapMainModule = "{$targetmodule}";
</script>
<script src="modules/cbMap/generatemap/DuplicateRelations.js" type="text/javascript" charset="utf-8"></script>
