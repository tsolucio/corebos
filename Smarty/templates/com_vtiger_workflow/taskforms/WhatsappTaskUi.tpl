<link rel="stylesheet" type="text/css" href="include/dropzone/dropzone.css">
<link rel="stylesheet" type="text/css" href="include/dropzone/custom.css">
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="include/dropzone/dropzone.js"></script>
<script type="text/javascript" charset="utf-8">
Dropzone.autoDiscover = false;
var moduleName = '{$entityName}';
var __attfieldnames = '{if isset($task->attfieldnames)}{$task->attfieldnames}{/if}';
var __attdocids = '{if isset($task->attachmentids)}{$task->attachmentids}{/if}';
var __attdocidcnt = {if isset($task->attachmentids)}(__attdocids.match(/,/g) || []).length{else}0{/if};
var __attinfo = {$task->dzattinfo|json_encode};
</script>
<script src="modules/com_vtiger_workflow/resources/whatsappworkflowtaskscript.js" type="text/javascript" charset="utf-8"></script>
<div id='_progress_' style='float: right; display: none; position: absolute; right: 35px; font-weight: bold;'>
<span id='_progressmsg_'>...</span><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border='0' align='absmiddle'>
</div>

<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b> {'LBL_RECEPIENTS'|@getTranslatedString:'SMSNotifier'} </b></span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="phone" value="{$task->phone}" id="phone" />
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="task_phonefields" class="slds-select slds-page-header__meta-text">
						<option value=''> {$MOD.LBL_SELECT_OPTION_DOTDOTDOT} </option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-gutters slds-grid_vertical-align-center slds-p-horizontal_x-large">
	<div class="slds-col slds-size_8-of-12">
		<div id="file-uploader" class="dropzone mm-dz-div slds-m-top_xx-small" style="display: none;">
			<span class="dz-message mmdzmessage"><img alt="{'Drag attachment here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_32.png"></span>
			<span class="dz-message mmdzmessage" id="file-uploader-message">&nbsp;{'Drag attachment here or click to upload'|@getTranslatedString}</span>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-text-align_right slds-p-around_x-small">
		<input type="hidden" id="attachmentCount" name="attachmentCount" value="{if isset($task->attachmentids)}{$task->attachmentids|substr_count:','}{else}0{/if}" >
		<input type="hidden" id="attachmentids"  name="attachmentids" value="{if isset($task->attachmentids)}{$task->attachmentids}{/if}" >
		<button onclick="jQuery('#file-uploader').show();attachmentManager.getDocuments();return false;" class="slds-button slds-button_success" >{'LBL_SELECT_DOCUMENTS'|@getTranslatedString:'MailManager'}</button>
		<button onclick="jQuery('#file-uploader').toggle();return false;" class="slds-button slds-button_success" >{'LBL_Attachments'|@getTranslatedString:'MailManager'}</button>
	</div>
</div>

<div class="slds-grid slds-gutters slds-grid_vertical-align-center slds-p-horizontal_x-large">
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
				<div class="slds-select_container">
					<select  id='task-fieldnames' style="display: none;" class="slds-select slds-page-header__meta-text">
						<option value=''> {$MOD.LBL_SELECT_OPTION_DOTDOTDOT} </option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="task_timefields" name="metavariable" class="slds-select slds-page-header__meta-text">
						<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
						{foreach key=META_LABEL item=META_VALUE from=$META_VARIABLES}
						<option value="{$META_VALUE}">{$META_LABEL|@getTranslatedString:$MODULE_NAME}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="slds-page-header">
	<div class="slds-grid slds-gutters">
		<div class="slds-col slds-size_1-of-1">
			<h1>
				<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_MESSAGE}">
					<span class="slds-tabs__left-icon">
						<span class="slds-icon_container" title="{$MOD.LBL_MESSAGE}">
							<svg class="slds-icon slds-icon_small" style="color:green;" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#email"></use>
							</svg>
						</span>
					</span>
						{$MOD.LBL_MESSAGE}
				</span>
			</h1>
		</div>
	</div>
</div>

<div class="slds-grid slds-gutters">
	<div class="slds-col slds-size_1-of-1">
		<div class="slds-form-element__control">
			<textarea class="slds-textarea" name="messageBody" rows="55" cols="40" id="save_content"> {$task->messageBody} </textarea>
		</div>
	</div>
</div>

<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" defer="1">
	var textAreaName = 'save_content';
	CKEDITOR.replace( textAreaName,	{ldelim}
		customConfig: '../../modules/com_vtiger_workflow/resources/Whatsappckeditor.js'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>
