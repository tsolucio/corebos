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
<script src="modules/com_vtiger_workflow/resources/emailtaskscript.js" type="text/javascript" charset="utf-8"></script>
<div id='_progress_' style='float: right; display: none; position: absolute; right: 35px; font-weight: bold;'>
<span id='_progressmsg_'>...</span><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border='0' align='absmiddle'>
</div>

<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{'LBL_EMAIL_FROMNAME'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="fromname" value="{$task->fromname}" id="save_fromname" class="form_input" style='width: 250px;'>
			<span id="task-emailfieldsfrmname-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsfrmname" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{'LBL_EMAIL_FROMEMAIL'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="fromemail" value="{$task->fromemail}" id="save_fromemail" class="form_input" style='width: 250px;'>
			<span id="task-emailfieldsfrmemail-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsfrmemail" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{'LBL_EMAIL_REPLYTO'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="replyto" value="{$task->replyto}" id="save_replyto" class="form_input" style='width: 250px;'>
			<span id="task-emailfieldsreplyto-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsreplyto" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> {'LBL_EMAIL_RECIPIENT'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="recepient" value="{$task->recepient}" id="save_recepient" class="form_input" style='width: 250px;'>
			<span id="task-emailfields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfields" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b> {'LBL_EMAIL_CC'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="emailcc" value="{$task->emailcc}" id="save_emailcc" class="form_input" style='width: 250px;'>
			<span id="task-emailfieldscc-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldscc" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b> {'LBL_EMAIL_BCC'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="emailbcc" value="{$task->emailbcc}" id="save_emailbcc" class="form_input" style='width: 250px;'>
			<span id="task-emailfieldsbcc-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsbcc" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> {'LBL_EMAIL_SUBJECT'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="subject" value="{$task->subject}" id="save_subject" class="form_input" style='width: 350px;'>
			<span id="task-subjectfields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-subjectfields" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="small">
	<tr>
		<td style='padding-top: 10px;'>
			<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id='task-fieldnames' class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select>
		</td>
		<td>&nbsp</td>
		<td style='padding-top: 10px;'>
			<b>{$MOD.LBL_SELECT}&nbsp</b>
		</td>
		<td style='padding-top: 10px;'>
			<select class="small" id="task_timefields">
					<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
					{foreach key=META_LABEL item=META_VALUE from=$META_VARIABLES}
					<option value="{$META_VALUE}">{$META_LABEL|@getTranslatedString:$MODULE_NAME}</option>
					{/foreach}
			</select>
		</td>
		<td align="right" style='padding-top: 10px;'>
			<span class="helpmessagebox" style="font-style: italic;">{$MOD.LBL_WORKFLOW_NOTE_CRON_CONFIG}</span>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<span id="_messagediv_" style="display: none;z-index:22;" class="cb-alert-info"></span>
			<div id="file-uploader" class="dropzone mm-dz-div slds-m-top_xx-small" style="display: none;">
				<span class="dz-message mmdzmessage"><img alt="{'Drag attachment here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_32.png"></span>
				<span class="dz-message mmdzmessage" id="file-uploader-message">&nbsp;{'Drag attachment here or click to upload'|@getTranslatedString}</span>
			</div>
		</td>
		<td valign="top" align="left" style="white-space:nowrap;">
			<input type="hidden" id="attachmentCount" name="attachmentCount" value="{if isset($task->attachmentids)}{$task->attachmentids|substr_count:','}{else}0{/if}" >
			<input type="hidden" id="attachmentids"  name="attachmentids" value="{if isset($task->attachmentids)}{$task->attachmentids}{/if}" >
			<button onclick="jQuery('#file-uploader').show();attachmentManager.getDocuments();return false;" class="crmbutton small edit slds-m-left_xx-small slds-m-top_xx-small">{'LBL_SELECT_DOCUMENTS'|@getTranslatedString:'MailManager'}</button><br>
			<button onclick="jQuery('#file-uploader').toggle();return false;" class="crmbutton small edit slds-m-left_xx-small slds-m-top_xx-small">{'LBL_Attachments'|@getTranslatedString:'MailManager'}</button><br>
			<span class="slds-m-left_xx-small slds-m-top_x-small"><b>{'LBL_AttachmentInField'|@getTranslatedString:$MODULE_NAME}</b></span><br>
			<select id='attfieldnames' name='attfieldnames' class="small slds-m-left_xx-small slds-m-top_xx-small"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select><br>
		</td>
	</tr>
</table>
<table>
	<tr>
		<td>&nbsp</td>
	</tr>
	<tr>
		<td><b>{$MOD.LBL_MESSAGE}:</b></td>
	</tr>
</table>
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<p style="border:1px solid black;">
	<textarea style="width:90%;height:200px;" name="content" rows="55" cols="40" id="save_content" class="detailedViewTextBox"> {$task->content} </textarea>
</p>
<script type="text/javascript" defer="1">
	var textAreaName = 'save_content';
	CKEDITOR.replace( textAreaName,	{ldelim}
		extraPlugins : 'uicolor',
		uiColor: '#dfdff1'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>
