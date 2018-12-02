
function fieldDep_AddDays(change_field, action_field, new_value, old_value, parameters) {
	var date=new_value.split('-');
	var dd, mm, y;
	switch (userDateFormat) {
	case 'mm-dd-yyyy':
		dd = parseInt(date[1]);
		mm = parseInt(date[0]);
		y = parseInt(date[2]);
		break;
	case 'dd-mm-yyyy':
		dd = parseInt(date[0]);
		mm = parseInt(date[1]);
		y = parseInt(date[2]);
		break;
	case 'yyyy-mm-dd':
		dd = parseInt(date[2]);
		mm = parseInt(date[1]);
		y = parseInt(date[0]);
		break;
	}
	//console.log(date);console.log(mm);
	var currDate=new Date();
	currDate.setFullYear(y);
	currDate.setMonth( mm-1);
	currDate.setDate(dd);
	//console.log(currDate);
	currDate.setDate(currDate.getDate() + parseInt(parameters[0]));
	//console.log(currDate);
	dd = currDate.getDate();
	mm = currDate.getMonth() + 1;
	y = currDate.getFullYear();
	document.getElementsByName(action_field).item(0).value= y+ '-'+ mm + '-'+ dd;
}

function fieldDep_SubDays(change_field, action_field, new_value, old_value, parameters) {
	var oselect_array = document.getElementsByTagName('SELECT');
	for (var i=0; i<oselect_array.length; i++) {
		oselect_array[i].style.display = 'block';
	}
}

function fieldDep_OnlyNumbers(change_field, action_field, new_value, old_value, parameters) {
alert('ddj');
}

function fieldDep_OnlyLetters(change_field, action_field, new_value, old_value, parameters) {

}

function fieldDep_GetField(change_field, action_field, new_value, old_value, parameters) {

}

function fieldDep_AssignNewValue(change_field, action_field, new_value, old_value, parameters) {

}

function fieldDep_Format(change_field, action_field, new_value, old_value, parameters) {

}

function fieldDep_ChangeLabel(change_field, action_field, new_value, old_value, parameters) {

}

