<style>

</style>
<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
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

<div id="selectfunction"></div>
<div class="slds-p-around_x-small slds-grid slds-gutters">
<div class="slds-col slds-size_1-of-2 slds-form-element slds-text-align_left">
	<legend class="slds-form-element__legend slds-form-element__label">{'HitPolicy'|@getTranslatedString:'cbMap'}</legend>
	<div class="slds-form-element__control">
		<div class="slds-select_container">
			<select id="hitpolicy" required name="hitpolicy" class="slds-select" onchange="changeHitPolicy(this.value)">
				<option value="U" {if $hitpolicy=='U'}selected{/if}>{'Unique'|@getTranslatedString:'cbMap'}</option>
				<option value="F" {if $hitpolicy=='F'}selected{/if}>{'First'|@getTranslatedString:'cbMap'}</option>
				<option value="C" {if $hitpolicy=='C'}selected{/if}>{'Collect'|@getTranslatedString:'cbMap'}</option>
				<option value="A" {if $hitpolicy=='A'}selected{/if}>{'Any'|@getTranslatedString:'cbMap'}</option>
				<option value="R" {if $hitpolicy=='R'}selected{/if}>{'Rule Order'|@getTranslatedString:'cbMap'}</option>
				<option value="G" {if $hitpolicy=='G'}selected{/if}>{'Aggregate'|@getTranslatedString:'cbMap'}</option>
			</select>
		</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-2">
<fieldset class="slds-form-element">
	<legend class="slds-form-element__legend slds-form-element__label">{'Aggregate'|@getTranslatedString:'cbMap'}</legend>
	<div class="slds-form-element__control">
		<div class="slds-select_container">
			<select id="aggregate" name="aggregate" class="slds-select" {if $hitpolicy!='G'}disabled{/if}>
				<option value="sum" {if $aggregate=='sum'}selected{/if}>{'SUM'|@getTranslatedString:'Reports'}</option>
				<option value="min" {if $aggregate=='min'}selected{/if}>{'MIN'|@getTranslatedString:'Reports'}</option>
				<option value="max" {if $aggregate=='max'}selected{/if}>{'MAX'|@getTranslatedString:'Reports'}</option>
				<option value="count" {if $aggregate=='count'}selected{/if}>{'COUNT'|@getTranslatedString:'Reports'}</option>
			</select>
		</div>
	</div>
</fieldset>
</div>
</div>

<div class="slds-p-around_x-small slds-grid slds-gutters">
	<div class="slds-col slds-size_5-of-12 slds-text-align_left">
		<div class="slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-2 slds-text-align_left">
				<h2 class="slds-expression__title">{'LBL_RULES'|@getTranslatedString:'Settings'}</h2>
			</div>
			<div class="slds-col slds-size_1-of-2 slds-text-align_right">
				<button class="slds-button slds-button_neutral" type="button" id='addrule_button' onclick="appendEmptyFieldRow(); event.stopPropagation();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
					</svg>
					{'Add Rule'|@getTranslatedString:'cbMap'}
				</button>
				<button class="slds-button slds-button_text-destructive slds-float_right" type="button" id='delfield_button' onclick="deleteFieldRow(); event.stopPropagation();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					{'LBL_DELETE'|getTranslatedString}
				</button>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_7-of-12 slds-text-align_left">
		<h2 class="slds-expression__title">{'LBL_RULEDEF'|@getTranslatedString:'cbMap'}</h2>
	</div>
</div>

<div class="slds-p-around_x-small slds-grid slds-gutters">
<div class="slds-col slds-size_5-of-12 slds-form-element slds-text-align_left">
	<div>
		<div class="slds-page-header__meta-text slds-m-left_x-small" id="rulegrid" style="width:99%;"></div>
	</div>
</div>
<div class="slds-col slds-size_7-of-12 slds-form-element slds-form-element_horizontal slds-text-align_left slds-p-right_x-small">
<section class="slds-card" id="ruleeditsection">
	<section id="expeditsection">
		<div class="slds-p-around_x-small slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-2 slds-text-align_left">
				<h2 class="slds-expression__title">{'Expression'|@getTranslatedString:'cbMap'}</h2>
			</div>
			<div class="slds-col slds-size_1-of-2 slds-text-align_right">
				<button class="slds-button slds-button_neutral" onclick="openFunctionSelection('exptextarea');">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
					</svg>
					{'Function'|@getTranslatedString:'cbMap'}
				</button>
			</div>
		</div>
		<div class="slds-p-around_x-small slds-grid slds-gutters">
			<div class="slds-col slds-size_1-of-1 slds-text-align_left">
				<textarea id="exptextarea" class="slds-textarea"></textarea>
			</div>
		</div>
	</section>
	<section id="bmeditsection" style="display:none;">
		<div class="slds-form slds-p-around_small">
			<form name="dtbmselection">
			<label class="slds-form-element__label"> {'SINGLE_cbMap'|@getTranslatedString:'cbMap'} </label>
			<div class="slds-form-element__control slds-input-has-fixed-addon">
				<input id="bmapid" name="bmapid" class="slds-input" type="hidden" value="">
				<input id="bmapid_display" class="slds-input" name="bmapid_display" readonly="" style="border:1px solid #bababa;" type="text" value="" onclick="return window.open('index.php?module=cbMap&action=Popup&html=Popup_picker&form=dtbmselection&forfield=bmapid&srcmodule=GlobalVariable'+SpecialSearch,'vtlibui10wf','width=680,height=602,resizable=0,scrollbars=0,top=150,left=200');">
				<span class="slds-form-element__addon" id="fixed-text-addon-post">
					<button type="image" class="slds-button" alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.bmapid.value=''; this.form.bmapid_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
						<svg class="slds-icon slds-icon_small slds-icon-text-light" aria-hidden="true" >
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clear"></use> 
						</svg>
					</button>
				</span>
			</div>
			</form>
		</div>
	</section>
	<section id="dteditsection" style="display:none;">
		<div class="slds-p-around_small">
			<legend class="slds-form-element__legend slds-form-element__label">{'LBL_MODULE'|@getTranslatedString}</legend>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="dtmodule" required name="dtmodule" class="slds-select" onchange="getDTModuleFields(this.value);">
						{foreach item=arr from=$MODULES}
							<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<legend class="slds-form-element__legend slds-form-element__label">{'RETURN_FIELDS'|@getTranslatedString:'cbMap'}</legend>
			<div class="slds-form-element__control">
				<div class="slds-input_container">
					<input id="returnfields" required name="returnfields" class="slds-input">
				</div>
			</div>
			<legend class="slds-form-element__legend slds-form-element__label">{'Order by Column'|@getTranslatedString:'cbQuestion'}</legend>
			<div class="slds-form-element__control">
				<div class="slds-input_container">
					<input id="orderbyrule" required name="orderbyrule" class="slds-input">
				</div>
			</div>
		</div>

		<div class="slds-tabs_default">
		<ul class="slds-tabs_default__nav" role="tablist">
			<li class="slds-tabs_default__item" title="{'LBL_SEARCH'|@getTranslatedString}" role="presentation" id="tabsearchli" onclick="setActiveDTTab('tabsearch', 'tabconditions');">
			<a class="slds-tabs_default__link" href="javascript:void(0);" role="tab">
				<span class="slds-tabs__left-icon">
				<span class="slds-icon_container slds-icon-standard-case" title="{'LBL_SEARCH'|@getTranslatedString}">
					<svg class="slds-icon slds-icon_small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#search"></use>
					</svg>
				</span>
				</span>{'LBL_SEARCH'|@getTranslatedString}</a>
			</li>
			<li class="slds-tabs_default__item" title="{'LBL_CONDITIONS'|@getTranslatedString:'Settings'}" role="presentation" id="tabconditionsli" onclick="setActiveDTTab('tabconditions', 'tabsearch');">
			<a class="slds-tabs_default__link" href="javascript:void(0);" role="tab">
				<span class="slds-tabs__left-icon">
				<span class="slds-icon_container slds-icon-standard-opportunity" title="{'LBL_CONDITIONS'|@getTranslatedString:'Settings'}">
					<svg class="slds-icon slds-icon_small" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#filter"></use>
					</svg>
				</span>
				</span>{'LBL_CONDITIONS'|@getTranslatedString:'Settings'}</a>
			</li>
		</ul>
		</div>
		<div id="tabconditions">
			<div class="slds-p-around_x-small slds-grid slds-gutters">
				<div class="slds-col slds-size_1-of-2 slds-text-align_left">
					<h2 class="slds-expression__title">{'LBL_CONDITIONS'|@getTranslatedString:'Settings'}</h2>
				</div>
				<div class="slds-col slds-size_1-of-2 slds-text-align_right">
					<button class="slds-button slds-button_neutral">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
						{'Add Condition'|@getTranslatedString:'cbMap'}
					</button>
					<button class="slds-button slds-button_text-destructive slds-float_right" type="button" id='delfield_button' onclick="deleteFieldRow(); event.stopPropagation();">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
						{'LBL_DELETE'|getTranslatedString}
					</button>
				</div>
			</div>

			<div class="slds-p-around_small">
				<div class="slds-page-header__meta-text slds-m-left_x-small" id="condgrid" style="width:99%;"></div>
			</div>
		</div>
		<div id="tabsearch">
			<div class="slds-p-around_x-small slds-grid slds-gutters">
				<div class="slds-col slds-size_1-of-2 slds-text-align_left">
					<h2 class="slds-expression__title">{'LBL_SEARCH'|@getTranslatedString:'Settings'}</h2>
				</div>
				<div class="slds-col slds-size_1-of-2 slds-text-align_right">
					<button class="slds-button slds-button_neutral">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
						</svg>
						{'Add Search'|@getTranslatedString:'cbMap'}
					</button>
					<button class="slds-button slds-button_text-destructive slds-float_right" type="button" id='delfield_button' onclick="deleteFieldRow(); event.stopPropagation();">
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
						</svg>
						{'LBL_DELETE'|getTranslatedString}
					</button>
				</div>
			</div>

			<div class="slds-p-around_small">
				<div class="slds-page-header__meta-text slds-m-left_x-small" id="srchgrid" style="width:99%;"></div>
			</div>
		</div>
	</section>
</section>
</div>
</div>
<script>
/// Function selection
var wfexpfndefs = {$FNDEFS};
var wfexpselectionDIV = 'selectfunction';
///
var DecisionTableMap = {$mapcontent};

function saveModuleMapAction() {
	saveMapAction('mapid={$MapID}&tmodule={$targetmodule}&content='+encodeURI(JSON.stringify(DecisionTableMap)));
}
</script>
{include file='Components/ComponentsCSS.tpl'}
{include file='Components/ComponentsJS.tpl'}
<script src="modules/com_vtiger_workflow/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/cbMap/generatemap/DecisionTable.js" type="text/javascript" charset="utf-8"></script>
