<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">var moduleName = '{$entityName}';</script>
<script src="modules/com_vtiger_workflow/resources/generateimagecode.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var taskSavedData = {$task->taskSavedData|json_encode};
</script>

{* Field to Save Encoded Value Input Element *}
<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-form-element__label" for="save_encoded_value">{'LBL_WHERE_TO_SAVE'|@getTranslatedString:$MODULE_NAME}</label>
			<div class="slds-form-element__control">
				<select id="save_encoded_value" name="save_encoded_value" class="slds-input slds-page-header__meta-text">
					<option value=''>{'Select Field to Save Encoded Value'|@getTranslatedString:$MODULE_NAME}</option>
				</select>
			</div>
		</div>
	</div>
</div>

{* Field with Value to Encode Input Element *}
<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-form-element__label" for="">{'LBL_WHAT_TO_ENCODE'|@getTranslatedString:$MODULE_NAME}</label>
			<div class="slds-form-element__control">
				<select id="field_to_encode" name="field_to_encode" class="slds-input slds-page-header__meta-text">
					<option value=''>{'Select Field to Encode'|@getTranslatedString:$MODULE_NAME}</option>
				</select>
			</div>
		</div>
	</div>
</div>

{* Field to Select Encode Type Input Element *}
<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form-element slds-m-top--small">
			<label class="slds-form-element__label" for="LBL_ENCODE_FORMAT">{'Encoding Type'|@getTranslatedString:$MODULE_NAME}</label>
			<div class="slds-form-element__control">
				<select id="encoding_type" name="encoding_type" class="slds-input slds-page-header__meta-text">
					<option value=''>{'Select Encoding Type'|@getTranslatedString:$MODULE_NAME}</option>
					<option value="EAN13" {if $task->encoding_type eq 'EAN13'}selected{/if}>EAN-13</option>
					<option value="EAN8" {if $task->encoding_type eq 'EAN8'}selected{/if}>EAN-8</option>
					<option value="QRCODE,L" {if $task->encoding_type eq 'QRCODE,L'}selected{/if}>QR CODE Level L</option>
					<option value="QRCODE,M" {if $task->encoding_type eq 'QRCODE,M'}selected{/if}>QR CODE Level M</option>
					<option value="QRCODE,Q" {if $task->encoding_type eq 'QRCODE,Q'}selected{/if}>QR CODE Level Q</option>
					<option value="QRCODE,H" {if $task->encoding_type eq 'QRCODE,H'}selected{/if}>QR CODE Level H</option>
				</select>
			</div>
		</div>
	</div>
</div>
