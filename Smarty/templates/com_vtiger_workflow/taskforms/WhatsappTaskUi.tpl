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

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="small">
	<tr>
		<td style="padding-top: 10px; width: 100%" colspan=2>
		<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
			<tr>
				<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> {'LBL_RECEPIENTS'|@getTranslatedString:'SMSNotifier'}</b></td>
				<td class='dvtCellInfo'>
				<input type="text" name="phone" value="{$task->phone}" id="phone" class="form_input" style='width: 350px;'>
				<select id="task_phonefields" class="small">
					<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
				</select></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top: 10px; width: 80%;">
			<span id="_messagediv_" style="display: none;z-index:22;" class="cb-alert-info"></span>
			<div id="file-uploader" class="dropzone mm-dz-div slds-m-top_xx-small" style="display: none;">
				<span class="dz-message mmdzmessage"><img alt="{'Drag attachment here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_32.png"></span>
				<span class="dz-message mmdzmessage" id="file-uploader-message">&nbsp;{'Drag attachment here or click to upload'|@getTranslatedString}</span>
			</div>
		</td>
		<td valign="top" align="left" style="white-space:nowrap; width: 20%;">
			<input type="hidden" id="attachmentCount" name="attachmentCount" value="{if isset($task->attachmentids)}{$task->attachmentids|substr_count:','}{else}0{/if}" >
			<input type="hidden" id="attachmentids"  name="attachmentids" value="{if isset($task->attachmentids)}{$task->attachmentids}{/if}" >
			<button onclick="jQuery('#file-uploader').show();attachmentManager.getDocuments();return false;" class="crmbutton small edit slds-m-left_xx-small slds-m-top_xx-small">{'LBL_SELECT_DOCUMENTS'|@getTranslatedString:'MailManager'}</button><br>
			<button onclick="jQuery('#file-uploader').toggle();return false;" class="crmbutton small edit slds-m-left_xx-small slds-m-top_xx-small">{'LBL_Attachments'|@getTranslatedString:'MailManager'}</button><br>
		</td>
	</tr>
</table>
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<p style="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="small">
		<tr>
			<td class='' style="padding-top: 10px; width: 50%">
				<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
				<select id='task-fieldnames' class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select>
			</td>
			<td>&nbsp</td>
			<td style="padding-top: 10px; width: 50%;">
				<select class="small" id="task_timefields" name="metavariable">
					<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
					{foreach key=META_LABEL item=META_VALUE from=$META_VARIABLES}
					<option value="{$META_VALUE}">{$META_LABEL|@getTranslatedString:$MODULE_NAME}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class='' style="padding-top: 10px; width: 50%">
				<b>{$MOD.LBL_MESSAGE}</b>
			</td>
		</tr>
	</table>
	<textarea style="width:90%;height:200px;" name="messageBody" rows="55" cols="40" id="save_content" class="detailedViewTextBox"> {$task->messageBody} </textarea>
</p>
<script type="text/javascript" defer="1">
	var textAreaName = 'save_content';
	CKEDITOR.replace( textAreaName,	{ldelim}
		customConfig: '../../modules/com_vtiger_workflow/resources/Whatsappckeditor.js'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>