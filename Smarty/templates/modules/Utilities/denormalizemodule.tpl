<script type="text/javascript">
$(document).ready(function() {
	handleOperationChange();
	document.getElementById("denorm_op").addEventListener("change", function() {
		handleOperationChange();
	});
	document.getElementById("saveBtn").addEventListener("click", function() {
		VtigerJS_DialogBox.showbusy();
	});
});

function handleOperationChange() {
	$denorm_operation =  document.getElementById("denorm_op").value;
	switch($denorm_operation) {
	case 'denorm':
		document.getElementById("denormDiv").style.display = "";
		document.getElementById("denormalizedDiv").style.display = "none";
		break;
	case 'undo_denorm':
		document.getElementById("denormalizedDiv").style.display = "";
		document.getElementById("denormDiv").style.display = "none";
		break;
	default:
		break;
	}
}
</script>
{include file='Buttons_List.tpl'}
<div class="loader"></div>
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
		<form role="form" style="margin:0 100px;" method="post">
		<input type="hidden" name="module" value="Utilities">
		<input type="hidden" name="action" value="integration">
		<input type="hidden" name="_op" value="setconfigdenormalization">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="denorm_op">
				<abbr class="slds-required" title="required">* </abbr>{'SELECT_OPERATION'|@getTranslatedString:$MODULE} <span class="slds-badge .slds-theme_success">{'DENORMALIZED'|@getTranslatedString:$MODULE} {$totaldenormodulelist}/{$totalmodulelist}</span></label>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
				<select class="slds-select" id="denorm_op" name="denorm_op" required="">
					<option {if (isset($denormop) && $denormop eq "denorm")}{"selected"}{/if} value = 'denorm'>{'DENORMALIZE'|@getTranslatedString:$MODULE}</option>
					<option {if (isset($denormop) && $denormop eq "undo_denorm")}{"selected"}{/if}  value = 'undo_denorm'>{'UNDO_DENORMALIZE'|@getTranslatedString:$MODULE}</option>
				</select>
				</div>
			</div>
		</div><br>
		<div id="denormalizedDiv" class="slds-form-element" style="display:none; overflow:scroll">
			<legend class="slds-form-element__legend slds-form-element__label">{'DENORMALIZED_LIST'|@getTranslatedString:$MODULE}({'Select'|@getTranslatedString:$MODULE} & {'SAVE_TO_UNDO'|@getTranslatedString:$MODULE})</legend>
			<div class="slds-form-element__control">
			<div class="slds-checkbox_button-group" style="width: 50%;">
			{foreach key=denormodindex item=denormodulename from=$denormodulelist}
				<span class="slds-button slds-checkbox_button">
					<input type="checkbox" id="denorm_mod{$denormodindex}" value="{$denormodindex}" name="denorm_mod[]" />
					<label class="slds-checkbox_button__label" for="denorm_mod{$denormodindex}">
						<span class="slds-checkbox_faux">{$denormodulename}</span>
					</label>
				</span>
			{/foreach}
			</div>
			</div>
		</div>
		<div id="denormDiv" class="slds-form-element slds-m-top_small">
		<label class="slds-form-element__label" for="denor_mods">{'SelectDenormalize'|@getTranslatedString:'Utilities'}</label>
		<div class="slds-form-element__control">
			<select class="slds-select" id="denor_mods" name='denor_mods[]' multiple="">
				{foreach key=modindex item=modulename from=$modulelist}
					<option value="{$modindex}" {if !empty($denormodulelist[$modindex])}selected{/if}>{$modulename}</option>
				{/foreach}
			</select>
		</div>
		</div>
		<div class="slds-m-top_large">
			<button id="saveBtn" type="submit" class="slds-button slds-button_brand">{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
		</div>
		</form>
	{/if}
	</div>
</div>
</section>