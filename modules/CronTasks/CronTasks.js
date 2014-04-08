/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
************************************************************************************/

function fetchSaveCron(id)
{

	var status = $("cron_status").value;
	var timeValue= $("CronTime").value;
	var time = $("cron_time").value;
	var min_freq =parseInt($("min_freq").value,10);
	if(!numValidate("CronTime","","any",true)){
		return false;
	}
	if((timeValue % 1) !=0){
		alert("only integer values are allowed");
		return false;
	}
	if((timeValue < min_freq && time == "min") || timeValue <= 0 || timeValue == '' ){
		alert($("desc").value);

	}
	else{
		$("editdiv").style.display="none";
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
			{
				queue: {
					position: 'end',
					scope: 'command'
				},
				method: 'post',
				postBody: 'action=CronTasksAjax&module=CronTasks&file=SaveCron&record='+id+'&status='+status+'&timevalue='+timeValue+'&time='+time,
				onComplete: function(response) {
					$("status").style.display="none";
					$("notifycontents").innerHTML=response.responseText;
				}
			}
			);
	}
}

function fetchEditCron(id)
{
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=CronTasksAjax&module=CronTasks&file=EditCron&record='+id,
			onComplete: function(response) {
				$("status").style.display="none";
				$("editdiv").innerHTML=response.responseText;
			}
		}
		);
}
function move_module(tabid,move){

	//$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'module=CronTasks&action=CronTasksAjax&file=CronSequence&parenttab=Settings&record='+tabid+'&move='+move,
			onComplete: function(response) {
				$("notifycontents").innerHTML=response.responseText;

			}
		}
		);
}
