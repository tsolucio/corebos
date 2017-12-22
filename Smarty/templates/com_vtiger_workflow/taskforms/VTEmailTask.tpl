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

<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>{'LBL_EMAIL_FROMNAME'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo' width=85%><input type="text" name="fromname" value="{$task->fromname}" id="save_fromname" class="form_input slds-input">
			<span id="task-emailfieldsfrmname-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsfrmname" class="small slds-select" style="display: none; width:50%;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" nowrap="nowrap"><b>{'LBL_EMAIL_FROMEMAIL'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="fromemail" value="{$task->fromemail}" id="save_fromemail" class="form_input slds-input">
			<span id="task-emailfieldsfrmemail-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsfrmemail" class="small slds-select" style="display: none; width:50%;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" nowrap="nowrap"><b>{'LBL_EMAIL_REPLYTO'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="replyto" value="{$task->replyto}" id="save_replyto" class="form_input slds-input">
			<span id="task-emailfieldsreplyto-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsreplyto" class="small slds-select" style="display: none; width:50%;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" nowrap="nowrap"><b><font color=red>*</font> {'LBL_EMAIL_RECIPIENT'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="recepient" value="{$task->recepient}" id="save_recepient" class="form_input slds-input">
			<span id="task-emailfields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfields" class="small slds-select" style="display: none; width:50%;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" nowrap="nowrap"><b> {'LBL_EMAIL_CC'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="emailcc" value="{$task->emailcc}" id="save_emailcc" class="form_input slds-input">
			<span id="task-emailfieldscc-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldscc" class="small slds-select" style="display: none; width:50%;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" nowrap="nowrap"><b> {'LBL_EMAIL_BCC'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="emailbcc" value="{$task->emailbcc}" id="save_emailbcc" class="form_input slds-input">
			<span id="task-emailfieldsbcc-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfieldsbcc" class="small slds-select" style="display: none; width:50%;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" nowrap="nowrap"><b><font color=red>*</font> {'LBL_EMAIL_SUBJECT'|@getTranslatedString:$MODULE_NAME}</b></td>
		<td class='dvtCellInfo'><input type="text" name="subject" value="{$task->subject}" id="save_subject" class="form_input slds-input">
			<span id="task-subjectfields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-subjectfields" class="small slds-select" style="display: none; width:50%;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
</table>
<br/>
<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout detailview_table">
	<tr>
		<td class="dvtCellInfo" width="30%">
			<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id='task-fieldnames' class="small slds-select" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select>
		</td>
		<td class="dvtCellLabel" width="10%">
			<b>{$MOD.LBL_SELECT}</b>
		</td>
		<td class="dvtCellInfo" width="30%">
			<select class="small slds-select" id="task_timefields">
					<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
					{foreach key=META_LABEL item=META_VALUE from=$META_VARIABLES}
					<option value="{$META_VALUE}">{$META_LABEL|@getTranslatedString:$MODULE_NAME}</option>
					{/foreach}
			</select>
		</td>
		<td align="left" width="30%">
			<span class="helpmessagebox" style="font-style: italic;">{$MOD.LBL_WORKFLOW_NOTE_CRON_CONFIG}</span>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="dvtCellInfo">
			<span id="_messagediv_" style="display: none;z-index:22;" class="cb-alert-info"></span>
			<div id="file-uploader" class="dropzone mm-dz-div slds-m-top--xx-small" style="display: none;">
				<span class="dz-message mmdzmessage"><img alt="{'Drag attachment here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_32.png"></span>
				<span class="dz-message mmdzmessage" id="file-uploader-message">&nbsp;{'Drag attachment here or click to upload'|@getTranslatedString}</span>
			</div>
		</td>
		<td class="dvtCellInfo" valign="top" align="left" style="white-space:nowrap;">
			<input type="hidden" id="attachmentCount" name="attachmentCount" value="{if isset($task->attachmentids)}{$task->attachmentids|substr_count:','}{else}0{/if}" >
			<input type="hidden" id="attachmentids"  name="attachmentids" value="{if isset($task->attachmentids)}{$task->attachmentids}{/if}" >
			<button onclick="jQuery('#file-uploader').show();attachmentManager.getDocuments();return false;" class="slds-button slds-button--small slds-button--brand slds-m-left--xx-small slds-m-top--xx-small">{'LBL_SELECT_DOCUMENTS'|@getTranslatedString:'MailManager'}</button><br>
			<button onclick="jQuery('#file-uploader').toggle();return false;" class="slds-button slds-button--small slds-button--info slds-m-left--xx-small slds-m-top--xx-small">{'LBL_Attachments'|@getTranslatedString:'MailManager'}</button><br><br/>
			<span class="slds-m-left--xx-small slds-m-top--x-small"><b>{'LBL_AttachmentInField'|@getTranslatedString:$MODULE_NAME}</b></span><br><br/>
			<select id='attfieldnames' name='attfieldnames' class="small slds-select slds-m-left--xx-small slds-m-top--xx-small"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select><br/>
		</td>
	</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
	<tr>
		<td class="big" style="padding:.5rem 0 0 0;">
			<div class="forceRelatedListSingleContainer">
				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
					<div class="slds-card__header slds-grid">
						<header class="slds-media slds-media--center slds-has-flexi-truncate">
							<div class="slds-media__body">
								<h2>
									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
										<b>{$MOD.LBL_MESSAGE}:</b>
									</span>
								</h2>
							</div>
						</header>
					</div>
				</article>
			</div>
			<div class="slds-truncate">
				<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
				<p style="padding: 0 .5rem;">
					<textarea style="width:90%;height:200px;" name="content" rows="55" cols="40" id="save_content" class="detailedViewTextBox"> {$task->content} </textarea>
				</p>
			</div>
		</td>
	</tr>
</table>

<script type="text/javascript" defer="1">
	var textAreaName = 'save_content';
	CKEDITOR.replace( textAreaName,	{ldelim}
		extraPlugins : 'uicolor',
		uiColor: '#dfdff1'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>
