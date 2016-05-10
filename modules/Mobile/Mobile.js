/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/

function showDefaultCustomView(selectView,module,parenttab) {
	var viewName = encodeURIComponent(selectView.options[selectView.options.selectedIndex].value);
	var gotopage = "index.php?_operation=listModuleRecords&module="+ module +"&viewName="+ viewName +"&view=1";
	window.location = gotopage;
    return true;
}
function toggleAssignType(currType) {
	if (currType=="U") {
		document.getElementById("assign_user").style.display="block"
		document.getElementById("assign_team").style.display="none"
	}
	else {
		document.getElementById("assign_user").style.display="none"
		document.getElementById("assign_team").style.display="block"

	}
}
 
 
if(typeof($) == 'undefined') {
	$ = function(id) {
		var node = document.getElementById(id);
		if(typeof(node) == 'undefined') {
			node = false;
		}
		return node;
	}
	$fnT = function(id1, id2) {
		var node1 = $(id1);
		var node2 = $(id2);
		if(node1) {
			node1.style.display = 'none';
		}
		if(node2) {
			node2.style.display = 'block';
		}
	}
	$fnFocus = function(id) {
		try {
			var node = $(id);
			node.focus();
		} 
		catch(error) {
		}
	}
	
	$fnAddClass = function(node, toadd) {
		var classValue = node.className;
		var regex = new RegExp(toadd, "g");
		if(classValue.match(regex) == null) {
			classValue += " " + toadd;
			node.className = classValue;
		}
	}
	$fnRemoveClass = function(node, toremove) {
		var classValue = node.className;
		var regex = new RegExp(toremove, "g");
		classValue = classValue.replace(regex, '');
		node.className = classValue;
	}
	
	$fnCheckboxOn = function(idprefix) {
		var nodeon = $(idprefix+'_on');
		var nodeoff = $(idprefix+'_off');
		if(nodeon) {
			$fnAddClass(nodeon.parentNode, 'hide');
		}
		if(nodeoff) {
			$fnRemoveClass(nodeoff.parentNode, 'hide');
		}
	}
	$fnCheckboxOff = function(idprefix) {
		var nodeon = $(idprefix+'_on');
		var nodeoff = $(idprefix+'_off');
		if(nodeon) {
			$fnRemoveClass(nodeon.parentNode, 'hide');
		}
		if(nodeoff) {
			$fnAddClass(nodeoff.parentNode, 'hide');
		}
	}
}
//initialization for submit functions (create + edit view)
$( document ).delegate("#edit_page", "pageinit", function() {
	$("#EditView").submit(function() {
		var mvalidation = checkmandatory();
		if (mvalidation) {
			if ($("#module").val()=='Calendar' || $("#module").val()=='Events') {
				var datetimevaild = calendarvalidation();
				if (datetimevaild == true) {
					return true;
				}
				else {
					if (datetimevaild =='error_startdatetime') {
						$("#date_start").css("background-color", "#e2e2e2");
						alert (cal_error_arr.ERROR_STARTDATETIME);
					}
					else if (datetimevaild =='error_enddate') {
						$("#due_date").css("background-color", "#e2e2e2");
						alert (cal_error_arr.ERROR_DUEDATE);
					}
					return false;
				}
			}
		}
		else {
			return false;
		}
	});
});

//function to check the mandatory fields
function checkmandatory() {
	// get a collection of all empty fields
	var emptyFields = $(":input.required").filter(function() {		 
		// $.trim to prevent whitespace-only values being counted as 'filled'
		return !$.trim(this.value).length;
	});
	// if there are one or more empty fields
	if(emptyFields.length) {
		emptyFields.css("background-color", "#e2e2e2");
		emptyFields[0].focus();
		return false;
	}
	return true;
}
//function to set hidden calendar entriescalculate the duration of an event
function calendarvalidation() {
	var starttime_arr = $("#time_start").val().split(':');
	var endtime_arr = $("#time_end").val().split(':');
	var starthour = parseFloat(starttime_arr[0]);
	var startmin  = parseFloat(starttime_arr[1]);
	var startformat = $("#startformat").val();
	var endhour = parseFloat(endtime_arr[0]);
	var endmin  = parseFloat(endtime_arr[1]);
	var endformat = startformat;
	if(startformat != '' && startformat != '24') {
		if(startformat == 'pm') {
			if(starthour == 12) {
				starthour = 12;
			}
			else {
				starthour = starthour + 12;
			}
		} 
		else {
			if(starthour == 12) {
				starthour = 0;
			}
			else {
				starthour = starthour;
			}
		}
	}
	if(endformat != ''  && startformat != '24') {
		if(endformat == 'pm') {
			if(endhour == 12) {
				endhour = 12;
			}
			else {
				endhour = endhour + 12;
			}
		} 
		else {
			if(endhour == 12) {
				endhour = 0;
			}
			else {
				endhour = endhour;
			}
		}
	}
	var dateval1=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var dateval2=getObj('due_date').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var dateformat = $("#dateformat").val();
	var dateelements1=splitDateValues(dateval1,dateformat)
	var dateelements2=splitDateValues(dateval2,dateformat)

	var dd1=dateelements1[0]
	var mm1=dateelements1[1]
	var yyyy1=dateelements1[2]

	var dd2=dateelements2[0]
	var mm2=dateelements2[1]
	var yyyy2=dateelements2[2]
	var date1=new Date()
	var date2=new Date()

	date1.setYear(yyyy1)
	date1.setMonth(mm1-1)
	date1.setDate(dd1)
	date1.setHours(starthour)
	date1.setMinutes(startmin)

	date2.setYear(yyyy2)
	date2.setMonth(mm2-1)
	date2.setDate(dd2)
	date2.setHours(endhour)
	date2.setMinutes(endmin)
			
	//check date
	if (date1 - date2 =='0') {
		//add 5 minutes for create mode
		date2.setMinutes(date2.getMinutes() + 5);
		$("#time_end")[0].value = date2.getHours()+ ":"+ date2.getMinutes();
	}
	else {
		//must be in the future
		if(new Date() > date1){
			if ($("#Status").val() =='Planned' || $("#Status").val() =='Not Started') {
				return 'error_startdatetime';
			}
		}
	}
	//end date not before start date
	var firstDate=new Date();
	firstDate.setFullYear(dateelements1[2],(dateelements1[1] - 1 ),dateelements1[0]);
	var secondDate=new Date();
	secondDate.setFullYear(dateelements2[2],(dateelements2[1] - 1 ),dateelements2[0]);     
	if (secondDate < firstDate) {
		return 'error_enddate';
	}
	//duration for events
	if ($("#origmodule").val() == 'Events') {
		diff_ms = Math.abs(date2.getTime()-date1.getTime())/(1000*60);
		hour = Math.floor(diff_ms/(60));
		minute = Math.floor(diff_ms % 60);
		//set minimum duration
		if (hour ==0 && minute ==0) {
			minute= 5;
		}
		$("#duration_hours")[0].value = hour;
		$("#duration_minutes")[0].value = minute;
	}
	return true;
}

//function to separate dates based on formating
function splitDateValues(dateval,dateformating) {
	var dateseparator="-";
	var datecomponents = new Array(3);
	switch (dateformating) {
		case "yyyy-mm-dd" :
			datecomponents[0]=dateval.substr(dateval.lastIndexOf(dateseparator)+1,dateval.length) //dd
			datecomponents[1]=dateval.substring(dateval.indexOf(dateseparator)+1,dateval.lastIndexOf(dateseparator)) //mm
			datecomponents[2]=dateval.substring(0,dateval.indexOf(dateseparator)) //yyyyy
			break;
		case "mm-dd-yyyy" :
			datecomponents[0]=dateval.substring(dateval.indexOf(dateseparator)+1,dateval.lastIndexOf(dateseparator))
			datecomponents[1]=dateval.substring(0,dateval.indexOf(dateseparator))
			datecomponents[2]=dateval.substr(dateval.lastIndexOf(dateseparator)+1,dateval.length)
			break;
		case "dd-mm-yyyy" :
			datecomponents[0]=dateval.substring(0,dateval.indexOf(dateseparator))
			datecomponents[1]=dateval.substring(dateval.indexOf(dateseparator)+1,dateval.lastIndexOf(dateseparator))
			datecomponents[2]=dateval.substr(dateval.lastIndexOf(dateseparator)+1,dateval.length)
	}
	return datecomponents;
}

function addComment(relid) {
	$.ajax({
		type: 'POST',
		url: 'index.php',
		dataType: "json",
		data: {
		  "_operation": 'addComment',
		  "parentid": relid,
		  "comment": $('#comment_text').val()
		},
		async: true,
		success: function(response) {
			$('#comment_content').prepend(response.html);
			$('#comment_text').val('');
		},
		error: function(response, error) {
			
		}
	});
}

function multiSelectCheckNoneItem(selectElement, lblnone){
	var items = $(selectElement).val();
	var count = 0;
	if(items==null){
		$(selectElement).find("option[value='"+lblnone+"']").attr('selected', true);
	}else{
		count = items.length;
		if(items.indexOf(lblnone) != -1){
			count--;
		}
		if(count>0){
			$(selectElement).find("option[value='"+lblnone+"']").attr('selected', false);
		}
	}
	$(selectElement).selectmenu('refresh', true);
}
