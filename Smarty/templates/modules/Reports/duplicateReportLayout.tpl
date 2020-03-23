<!-- Save Report As.. UI -->
{assign var='MODAL' value=[
	'label'=>$MOD.LBL_SAVE_REPORT_AS,
	'ariaDescribe'=>$MOD.LBL_SAVE_REPORT_AS,
	'hideID'=>'duplicateReportLayout',
	'modalID' => 'duplicateReportLayout'
]}
{extends file='Components/Modal.tpl'}
{block name=ModalContent}
<div class="slds-form-element">
	<label class="slds-form-element__label" for="newreportname"><abbr class="slds-required" title="required">* </abbr>{$MOD.LBL_REPORT_NAME}</label>
	<div class="slds-form-element__control"><input type="text" name="newreportname" id="newreportname" class="slds-input slds-page-header__meta-text" required=""></div>
</div>
<div class="slds-form-element">
	<label class="slds-form-element__label" for="newreportname"><abbr class="slds-required" title="required">* </abbr>{$MOD.LBL_REP_FOLDER}</label>
	<div class="slds-form-element__control">
		<select name="reportfolder" id="reportfolder" class="slds-select slds-page-header__meta-text">
		{foreach item=folder from=$REP_FOLDERS}
			{if $FOLDERID eq $folder.id}
				<option value="{$folder.id}" selected>{$folder.name}</option>
			{else}
				<option value="{$folder.id}">{$folder.name}</option>
			{/if}
		{/foreach}
		</select>
	</div>
</div>
<div class="slds-form-element">
	<label class="slds-form-element__label" for="newreportname">{$MOD.LBL_DESCRIPTION}</label>
	<div class="slds-form-element__control">
		<textarea name="newreportdescription" id="newreportdescription" class="slds-textarea slds-page-header__meta-text">{if isset($REPORTDESC)}{$REPORTDESC}{/if}</textarea>
	</div>
</div>
{/block}
{block name=ModalFooter}
	<button type="button" class="slds-button slds-button_neutral" onClick="fninvsh('duplicateReportLayout');">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
	<button type="button" class="slds-button slds-button_brand" onClick="duplicateReport({$REPORTID});">{$APP.LBL_SAVE_BUTTON_LABEL}</button>
{/block}