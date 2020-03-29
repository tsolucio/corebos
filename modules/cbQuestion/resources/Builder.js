
function copysql() {
	showSQLMsg(alert_arr.Copied, 'success');
	document.getElementById('bqsql').select();
	document.execCommand('copy');
	window.getSelection().removeAllRanges();
	hideSQLMsg();
}

function changecbqModule(newmodule) {
	document.getElementById('save_conditions').innerHTML='';
	document.getElementById('bqsql').value='';
	document.getElementById('evalid_type').value = (newmodule=='Workflow' ? 'com_vtiger_workflow' : newmodule);
	moduleName = newmodule;
	conditions = null;
	builderconditions.changeModule();
}

function showSQLMsg(msg, role) {
	role = role || 'info';
	document.getElementById('sqlmsg').innerHTML=msg;
	let msgdiv = document.getElementById('sqlmsgdiv');
	msgdiv.classList.remove('slds-theme_info','slds-theme_error','slds-theme_success','slds-theme_warning');
	msgdiv.classList.add('slds-theme_'+role);
	document.getElementById('sqlmsgicon').childNodes[1].href.baseVal='include/LD/assets/icons/utility-sprite/svg/symbols.svg#'+role;
	msgdiv.classList.remove('bld-hidden');
}

function hideSQLMsg() {
	setTimeout(
		function () {
			document.getElementById('sqlmsgdiv').classList.add('bld-hidden');
		},
		2000
	);
}
