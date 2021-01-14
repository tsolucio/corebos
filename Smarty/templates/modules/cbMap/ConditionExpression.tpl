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

<div class="slds-p-around_x-small slds-grid slds-gutters">
<div class="slds-col  slds-size_1-of-3 slds-form-element slds-text-align_left">
	<legend class="slds-form-element__legend slds-form-element__label">{'LBL_MODULE'|@getTranslatedString:'cbMap'}</legend>
	<div class="slds-form-element__control">
		<div class="slds-select_container">
			<select id="msmodules" required name="msmodules" class="slds-select" onchange="editpopupobj.setModule(this.value)">
				{foreach item=arr from=$MODULES}
					<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
				{/foreach}
			</select>
		</div>
	</div>
</div>
<div class="slds-col slds-size_2-of-3">
<fieldset class="slds-form-element">
<legend class="slds-form-element__legend slds-form-element__label">{'TypeOfExpression'|@getTranslatedString:'cbMap'}</legend>
<div class="slds-form-element__control">
<div class="slds-radio_button-group">
<span class="slds-button slds-radio_button">
<input type="radio" name="cemapt" id="cemaptexp" value="expression" {if $maptype=='expression'}checked {/if}/>
<label class="slds-radio_button__label" for="cemaptexp" onclick="showexpinputs('expression');">
<span class="slds-radio_faux">{'LBL_EXPRESSION'|@getTranslatedString:'com_vtiger_workflow'}</span>
</label>
</span>
<span class="slds-button slds-radio_button">
<input type="radio" name="cemapt" id="cemapttpl" value="template" {if $maptype=='template'}checked {/if}/>
<label class="slds-radio_button__label" for="cemapttpl" onclick="showexpinputs('template');">
<span class="slds-radio_faux">{'Template'|@getTranslatedString:'Documents'}</span>
</label>
</span>
<span class="slds-button slds-radio_button">
<input type="radio" name="cemapt" id="cemaptfnc" value="function" {if $maptype=='function'}checked {/if}/>
<label class="slds-radio_button__label" for="cemaptfnc" onclick="showexpinputs('function');">
<span class="slds-radio_faux">{'Function'|@getTranslatedString:'cbMap'}</span>
</label>
</span>
</div>
</div>
</fieldset>
</div>
</div>

<div class="slds-p-around_x-small slds-form-element" id="exptpldiv" {if $maptype=='function'}style="display:none;"{/if}>
	<div class="slds-p-around_x-small slds-grid slds-gutters">
		<div class="slds-col slds-size_1-of-2 slds-form-element">
			<select id='editpopup_fieldnames' class='slds-select'>
				<option value="">{'LBL_USE_FIELD_VALUE_DASHDASH'|@getTranslatedString:'com_vtiger_workflow'}</option>
			</select>
		</div>

		<div class="slds-col slds-size_1-of-2 slds-form-element">
			<select id='editpopup_functions' class='slds-select'>
				<option value="">{'LBL_USE_FUNCTION_DASHDASH'|@getTranslatedString:'com_vtiger_workflow'}</option>
			</select>
		</div>
	</div>

	<div class="slds-p-around_x-small slds-grid slds-gutters">
		<div class="slds-col slds-form-element">
			<input type="hidden" id='editpopup_field' />
			<input type="hidden" id='editpopup_field_type' />
			<textarea id="editpopup_expression" class="slds-textarea">{$mapcontent}</textarea>
		</div>
	</div>
</div>

<div class="slds-p-around_x-small slds-form-element" id="funcdiv" {if $maptype!='function'}style="display:none;"{/if}>

	<div class="slds-p-around_x-small slds-grid slds-gutters">
		<div class="slds-col slds-size_1-of-2 slds-form-element">
			<label class="slds-form-element__label" for="functionname">
			<abbr class="slds-required" title="{'REQUIRED'|@getTranslatedString:'Users'}">* </abbr>{'FunctionName'|@getTranslatedString:'cbMap'}</label>
			<div class="slds-form-element__control">
				<input type="text" name="functionname" id="functionname" required="" class="slds-input" value="{$fname}"/>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-2 slds-form-element">&nbsp;</div>
	</div>
	<div class="slds-p-around_x-small slds-grid slds-gutters slds-grid_vertical">
		<div class="slds-col slds-size_1-of-2 slds-form-element">
			<label class="slds-form-element__label" for="params">{'FunctionParams'|@getTranslatedString:'cbMap'}</label>
			<div class="slds-form-element__control" id="fparamsdiv">
				{foreach item=prm from=$fparams}
				<input type="text" name="paraminput" class="slds-m-top_xx-small slds-input" onchange="setParamInputs();" value="{$prm}"/>
				{/foreach}
				<input type="text" name="paraminput" class="slds-m-top_xx-small slds-input" onchange="setParamInputs();" value=""/>
			</div>
		</div>
		<div class="slds-col slds-size_1-of-2 slds-form-element">&nbsp;</div>
	</div>

</div>

<script>
var editpopupobj = '';

function setParamInputs() {
	const prms = document.querySelectorAll('input[name="paraminput"]');
	var n = document.getElementById('fparamsdiv');
	for (var cnt=0; cnt<prms.length; cnt++) {
		if (prms[cnt].value=='') {
			n.removeChild(prms[cnt]);
		}
	}
	var inp = document.createElement('input');
	inp.type='text';
	inp.name='paraminput';
	inp.classList.add('slds-m-top_xx-small');
	inp.classList.add('slds-input');
	inp.onchange=setParamInputs;
	inp.value='';
	n.appendChild(inp);
	inp.focus();
}

function showexpinputs(mtype) {
	document.getElementById('exptpldiv').style.display=(mtype=='function' ? 'none' : 'block');
	document.getElementById('funcdiv').style.display=(mtype=='function' ? 'block' : 'none');
}

function saveModuleMapAction() {
	let params = 'mapid={$MapID}&tmodule='+document.getElementById('msmodules').value+'&mtype=';
	const maptype = document.querySelectorAll('input[name="cemapt"]:checked')[0].value;
	params = params + maptype;
	if (maptype=='function') {
		const prms = document.querySelectorAll('input[name="paraminput"]');
		let vals = Array.from(prms).map(el => el.value);
		vals.filter(v => v!='');
		vals = vals.join().slice(0, -1);
		params += '&fname=' + document.getElementById('functionname').value + '&params=' + encodeURI(vals);
	} else {
		params += '&content='+encodeURI(document.getElementById('editpopup_expression').value);
	}
	saveMapAction(params);
}

jQuery(document).ready(function () {
	editpopupobj = fieldExpressionPopup('{$targetmodule}', jQuery);
	editpopupobj.setModule('{$targetmodule}');
});
</script>
