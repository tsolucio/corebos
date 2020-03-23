<div class="slds-card slds-m-around--x-small" style="height: 75vh;">
<div id="view" class="workflows-list">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
</div>
{include file='applicationmessage.tpl'}
<form id="frmEditView" name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php">
<input type="hidden" name="module" value="com_vtiger_workflow">
<input type="hidden" name="action" value="ImportWFSave">
<div class="slds-p-around_small">
	<div class="slds-form-element">
		<span class="slds-form-element__label" id="file-selector-primary-label">{'Import'|@getTranslatedString} {'com_vtiger_workflow'|@getTranslatedString:'com_vtiger_workflow'}</span>
		<div class="slds-form-element__control">
			<div class="slds-file-selector slds-file-selector_files">
				<div class="slds-file-selector__dropzone">
					<input type="file" class="slds-file-selector__input slds-assistive-text" accept="text/csv" name="wfimportfile" id="wfimportfile" aria-labelledby="file-selector-primary-label file-selector-secondary-label" />
					<label class="slds-file-selector__body" for="wfimportfile" id="file-selector-secondary-label">
						<span class="slds-file-selector__button slds-button slds-button_neutral">
							<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#upload"></use>
							</svg>{'LBL_UPLOAD'|@getTranslatedString:'Users'}</span>
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-form-element slds-p-top_small">
		<button class="slds-button slds-button_success">{'LBL_SEND'|@getTranslatedString}</button>
	</div>
</div>
</div>
