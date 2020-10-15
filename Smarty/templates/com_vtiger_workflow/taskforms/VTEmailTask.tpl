<link rel="stylesheet" type="text/css" href="include/dropzone/dropzone.css">
<link rel="stylesheet" type="text/css" href="include/dropzone/custom.css">
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="include/dropzone/dropzone.js"></script>
<script type="text/javascript" src="include/js/vtlib.js"></script>
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

<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b>{'LBL_EMAIL_FROMNAME'|@getTranslatedString:$MODULE_NAME}</b></span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="fromname" value="{if isset($task->fromname)}{$task->fromname}{/if}" id="save_fromname"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-emailfieldsfrmname-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="task-emailfieldsfrmname" style="display: none;" class="slds-select slds-page-header__meta-text">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b>{'LBL_EMAIL_FROMEMAIL'|@getTranslatedString:$MODULE_NAME}</b></span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="fromemail" value="{if isset($task->fromemail)}{$task->fromemail}{/if}" id="save_fromemail"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-emailfieldsfrmemail-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select id="task-emailfieldsfrmemail" style="display: none;" class="slds-select slds-page-header__meta-text">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b>{'LBL_EMAIL_REPLYTO'|@getTranslatedString:$MODULE_NAME}</b></span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="replyto" value="{if isset($task->replyto)}{$task->replyto}{/if}" id="save_replyto"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-emailfieldsreplyto-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select class="slds-select slds-page-header__meta-text" id="task-emailfieldsreplyto" style="display: none;">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b><abbr class="slds-required" title="required">* </abbr>{'LBL_EMAIL_RECIPIENT'|@getTranslatedString:$MODULE_NAME}</b></span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="recepient" value="{if isset($task->recepient)}{$task->recepient}{/if}" id="save_recepient"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-emailfields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select class="slds-select slds-page-header__meta-text" id="task-emailfields" style="display: none;">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b> {'LBL_EMAIL_CC'|@getTranslatedString:$MODULE_NAME}</b> </span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="emailcc" value="{if isset($task->emailcc)}{$task->emailcc}{/if}" id="save_emailcc" />
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-emailfieldscc-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select class="slds-select slds-page-header__meta-text" id="task-emailfieldscc" style="display: none;">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b> {'LBL_EMAIL_BCC'|@getTranslatedString:$MODULE_NAME} </b> </span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="emailbcc" value="{if isset($task->emailbcc)}{$task->emailbcc}{/if}" id="save_emailbcc"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-emailfieldsbcc-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select class="slds-select slds-page-header__meta-text" id="task-emailfieldsbcc" style="display: none;">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-gutters slds-grid_vertical-align-center">
	<div class="slds-col slds-size_2-of-12 slds-text-align_right slds-p-around_x-small">
		<span><b> <abbr class="slds-required" title="required">* </abbr>{'LBL_EMAIL_SUBJECT'|@getTranslatedString:$MODULE_NAME}</b></span>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" class="slds-input" name="subject" value="{if isset($task->subject)}{$task->subject}{/if}" id="save_subject"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<span id="task-subjectfields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
			<div class="slds-form-element__control">
				<div class="slds-select_container">
					<select class="slds-select slds-page-header__meta-text" id="task-subjectfields" style="display: none;">
						<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-grid slds-grid_vertical-align-center slds-p-horizontal_xx-large slds-border_top">
	<div class="slds-col slds-size_1-of-3 slds-p-around_x-small">
		<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}"></span>
		<div class="slds-form-element__control">
			<div class="slds-select_container">
				<select class="slds-select slds-page-header__meta-text" id='task-fieldnames' style="display: none;">
					<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
				</select>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_1-of-3  slds-p-around_x-small">
		<div class="slds-form-element__control">
			<div class="slds-select_container">
				<select class="slds-select slds-page-header__meta-text" id="task_timefields">
					<option value="">{'Select Meta Variables'|@getTranslatedString:$MODULE_NAME}</option>
					{foreach key=META_LABEL item=META_VALUE from=$META_VARIABLES}
					<option value="{$META_VALUE}">{$META_LABEL|@getTranslatedString:$MODULE_NAME}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_1-of-3 slds-text-align_right slds-p-around_x-small">
		<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_warning" role="alert">
			<h2>{$MOD.LBL_WORKFLOW_NOTE_CRON_CONFIG}</h2>
		</div>
	</div>
</div>
<div class="slds-grid slds-grid_vertical-align-center slds-p-horizontal_xx-large">
	<div class="slds-col slds-size_8-of-12 slds-p-around_x-small">
		<span id="_messagediv_" style="display: none;z-index:22;" class="cb-alert-info"></span>
		<div id="file-uploader" class="dropzone mm-dz-div slds-m-top_xx-small" style="display: none;">
			<span class="dz-message mmdzmessage"><img alt="{'Drag attachment here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_32.png"></span>
			<span class="dz-message mmdzmessage" id="file-uploader-message">&nbsp;{'Drag attachment here or click to upload'|@getTranslatedString}</span>
		</div>
	</div>
	<div class="slds-col slds-size_4-of-12 slds-p-around_x-small">
		<input class="slds-input" type="hidden" id="attachmentCount" name="attachmentCount" value="{if isset($task->attachmentids)}{$task->attachmentids|substr_count:','}{else}0{/if}" />
		<input class="slds-input" name="listofids" id="listofids" type="hidden" value="{if isset($LISTID)}{$LISTID}{/if}" />
		<input class="slds-input" type='hidden' name="msgtpopup_type" id="msgtpopup_type" value="MsgTemplate" />
		<input class="slds-input" type='hidden' name="calltype" id="calltype" value="emailworkflow"/>
		<input class="slds-input" type="hidden" id="attachmentids"  name="attachmentids" value="{if isset($task->attachmentids)}{$task->attachmentids}{/if}"/>
		<div class="slds-grid">
			<div class="slds-col">
				<input class="slds-button slds-button_success" title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" onclick="jQuery('#file-uploader').show();return vtlib_open_popup_window('','msgtpopup','MsgTemplate','{$workflow->moduleName}&relmod_id=0&parent_module={$workflow->moduleName}');" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}"/>
			</div>
			<div class="slds-col">
				<button onclick="jQuery('#file-uploader').show();attachmentManager.getDocuments();return false;" class="slds-button slds-button_success">{'LBL_SELECT_DOCUMENTS'|@getTranslatedString:'MailManager'}</button>
			</div>
		</div>
		<div class="slds-grid slds-p-top_x-small">
			<div class="slds-col">
				<button onclick="jQuery('#file-uploader').toggle();return false;" class="slds-button slds-button_success">{'LBL_Attachments'|@getTranslatedString:'MailManager'}</button>
			</div>
			{if $entityName eq 'cbCalendar'}
				<div class="slds-col">
					<label class="slds-checkbox_toggle slds-grid">
					<input type="checkbox" id= "attach_icalendar" name="attach_icalendar" aria-describedby="toggle-desc" {if ($task->attach_icalendar eq "on")}{"checked"}{/if}/>
					<span id="toggle-desc" class="slds-checkbox_faux_container" aria-live="assertive">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-checkbox_on">{'LBL_ENABLED'|@getTranslatedString}</span>
						<span class="slds-checkbox_off">{'LBL_DISABLED'|@getTranslatedString}</span>
					</span>
					<span>&nbsp;{'LBL_Attach_icalendar'|@getTranslatedString:'cbCalendar'}</span>
					</label>
				</div>
			{/if}
		</div>
		<div class="slds-grid_vertical-align-center slds-grid_vertical slds-p-top_x-small slds-grid">
			<div class="slds-col slds-size_1-of-1">
				<div class="slds-form-element">
					<span><b>{'LBL_AttachmentInField'|@getTranslatedString:$MODULE_NAME}</b></span>
					<div class="slds-form-element__control ">
						<div class="slds-select_container">
							<select class="slds-select slds-page-header__meta-text" id='attfieldnames' name='attfieldnames'>
								<option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option>
							</select>
						</div>
					</div>
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
