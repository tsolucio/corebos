{assign var='MODAL' value=['label'=>$APP.LBL_MASSTAG_FORM_HEADER, 'ariaDescribe'=>$APP.LBL_MASSTAG_FORM_HEADER, 'hideID'=>'masstag']}
{extends file='Components/Modal.tpl'}
{block name=ModalContent}
<form method="POST" id="masstag_form" name="masstag_form" action="index.php">
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="action" value="{$MODULE}Ajax">
	<input type="hidden" name="file" value="TagCloud">
	<input type="hidden" name="ajxaction" value="MASSTAG">
	<input type="hidden" name="ids" id="ids" value="">
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="add_tag">{$APP.LBL_ADD_TAG}</label>
		<div class="slds-form-element__control">
			<input type="text" name="add_tag" id="add_tag" class="slds-input" />
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="remove_tag">{$APP.LBL_REMOVE_TAG}</label>
		<div class="slds-form-element__control">
			<input type="text" name="remove_tag" id="remove_tag" class="slds-input" />
		</div>
	</div>
</form>
{/block}
{block name=ModalFooter}
<button name="cancelar" type="button" onClick="hide('masstag');" class="slds-button slds-button_text-destructive">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
<button name="Seleccionar" type="button" onclick="submitFormForAction('masstag_form', '{$MODULE}Ajax')" class="slds-button slds-button_neutral">{$APP.LBL_EXECUTE_MASSTAG}</button>
{/block}