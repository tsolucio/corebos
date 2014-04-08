{*<!--
/*+*******************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *
  *******************************************************************************/
-->*}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
</script>
<script src="modules/SMSNotifier/workflow/VTSMSTask.js" type="text/javascript" charset="utf-8"></script>

<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> {'LBL_RECEPIENTS'|@getTranslatedString:'SMSNotifier'}</b>
		</td>
		<td class='dvtCellInfo'><input type="text" name="sms_recepient" value="{$task->sms_recepient}" id="save_recepient" class="form_input" style='width: 250px;'>
			<span id="task-phonefields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task_phonefields" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select>
		</td>
	</tr>
</table>	

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="small">
	<tr>
		<td style='padding-top: 10px;'>
			<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="sms_fieldnames" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select>
		</td>
	</tr>
</table>
	
<p>
	<textarea name="content" rows="15" cols="40" id="save_content" class='detailedViewTextBox'>{$task->content}</textarea>
</p>

