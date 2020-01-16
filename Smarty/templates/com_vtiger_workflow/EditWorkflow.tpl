{include file='com_vtiger_workflow/Header.tpl'}

{include file='com_vtiger_workflow/EditWorkflowIncludes.tpl'}

{include file='com_vtiger_workflow/WorkflowTemplatePopup.tpl'}

{include file='com_vtiger_workflow/NewTaskPopup.tpl'}
<!--Error message box popup-->
{include file='com_vtiger_workflow/ErrorMessageBox.tpl'}
<!--Done popups-->

<table border=0 cellspacing=0 cellpadding=20 width="99%" class="settingsUI">
<tr>
<td valign=top>
<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr>
<td class="small" valign=top align=left>
<div id="view" class="workflows-edit">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<form name="EditView" action="index.php" method="POST" id="edit_workflow_form" onsubmit="VtigerJS_DialogBox.block();">
		{include file='com_vtiger_workflow/EditWorkflowMeta.tpl'}

		{include file='com_vtiger_workflow/EditWorkflowBasicInfo.tpl'}
		<br>
		{include file='com_vtiger_workflow/EditWorkflowTriggerTypes.tpl'}
		<br>
		{include file='com_vtiger_workflow/ListConditions.tpl' RecordSetTab=1}
	</form>

	{if $saveType eq "edit"}
		<br>
		{include file='com_vtiger_workflow/ListTasks.tpl'}
	{/if}
</div>
<div id="dump" style="display:None;"></div>
{include file='com_vtiger_workflow/Footer.tpl'}