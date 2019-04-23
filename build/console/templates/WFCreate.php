<?php
require_once "modules/com_vtiger_workflow/VTWorkflowManager.inc";
require_once "modules/com_vtiger_workflow/VTTaskManager.inc";
require_once "modules/com_vtiger_workflow/VTWorkflowApplication.inc";
require_once "include/events/SqlResultIterator.inc";

$wm = new VTWorkflowManager($adb);
$wf = $wm->newWorkflow("MODULE");
$wf->description = "DESC";
$wf->test = "";
$wf->executionConditionAsLabel("ON_EVERY_SAVE");
$wm->save($wf);


$tm = new VTTaskManager($adb);
$taskType ="VTEntityMethodTask" ;
$workflowId =$wf->id;
$task = $tm->createTask($taskType, $workflowId);
$task->summary ="NAME";
$task->active=true;
$task->methodName ="DESC";
$task->subject="NAME";
$tm->saveTask($task);