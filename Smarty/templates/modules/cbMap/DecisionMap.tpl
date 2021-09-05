<style>
.slds-dropdown__scroll::-webkit-scrollbar {
  width: 8px;
}

.slds-dropdown__scroll::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey;
  border-radius: 5px;
}

.slds-dropdown__scroll::-webkit-scrollbar-thumb {
  background: grey;
  border-radius: 5px;
}

.slds-dropdown__scroll::-webkit-scrollbar-thumb:hover {
  background: #d3d3d3;
}
</style>
<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
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
				<button class="slds-button slds-button_neutral" type="button" id='addrule_button' onclick="appendEmptyFieldRow('rule'); event.stopPropagation();">
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
	<section id="show-ruleeditsection"></section>
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
	let newDecisionTableMap = {};
	newDecisionTableMap.hitPolicy = document.getElementById('hitpolicy').value;
	if (newDecisionTableMap.hitPolicy == 'G') {
		newDecisionTableMap.aggregate = document.getElementById('aggregate').value;
	}
	let newRuleData = new Array();
	for (let r in ruleData) {
		if (ruleData[r].ruletype == 'expression') {
			console.log(ruleData[r].sequence);
			const expression = document.getElementById('exptextarea-'+ruleData[r].sequence).value;
			const expRule = {
				sequence: parseInt(ruleData[r].sequence),
				expression: expression == '' ? '' : expression,
				output: ruleData[r].output,
			};
			newRuleData.push(expRule);
		} else if (ruleData[r].ruletype == 'businessmap') {
			const mapid = document.getElementById('bmapid_'+ruleData[r].sequence).value;
			const mapRule = {
				sequence: parseInt(ruleData[r].sequence),
				mapid: mapid  == '' ? '' : mapid,
				output: ruleData[r].output,
			}
			newRuleData.push(mapRule);
		} else {
			const module = document.getElementById('dtmodule-'+ruleData[r].sequence).value;
			const orderbyrule = document.getElementById('orderbyrule-'+ruleData[r].sequence).value;
			const returnfields = document.getElementById('returnfields-'+ruleData[r].sequence).value;
			const decisionTable = {
				sequence: parseInt(ruleData[r].sequence),
				decisionTable: {
					module: module,
					conditions: {
						condition: condGroup[ruleData[r].sequence]
					},
					orderby: orderbyrule.slice(0, -1),
					searches: {
						search: {
							condition: srchData[ruleData[r].sequence]
						}
					},
					output: returnfields.slice(0, -1),
				},
				output: ruleData[r].output,
			}
			newRuleData.push(decisionTable);
		}
	}
	newDecisionTableMap.rules = newRuleData;
	saveMapAction('mapid={$MapID}&tmodule={$targetmodule}&content='+encodeURI(JSON.stringify(newDecisionTableMap)));
}
</script>
{include file='Components/ComponentsCSS.tpl'}
{include file='Components/ComponentsJS.tpl'}
<script src="modules/com_vtiger_workflow/resources/functionselect.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/cbMap/generatemap/DecisionTable.js" type="text/javascript" charset="utf-8"></script>
