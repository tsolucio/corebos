<div id="processflowpushalong" class="mermaid">
{$FLOWGRAPH}
</div>
<script>
window.processflowmoveto{$pflowid} = function (tostate, forrecord, askifsure) {
	if (askifsure) {
		$ans = confirm(alert_arr.WANT_TO_CONTINUE);
	} else {
		$ans = true;
	}
	if ($ans) {
		{if $isInEditMode}
		document.getElementById('{$fieldName}').value = tostate;
		{else}
		var txtBox = 'txtbox_{$fieldName}';
		document.getElementById(txtBox).value = tostate;
		document.getElementById('cbcustominfo2').value = '{$pflowid}';
		dtlViewAjaxSave('{$fieldName}', '{$module}', '{$uitype}', '', '{$fieldName}', forrecord);
		{/if}
	}
	return false;
}
mermaid.initialize({
	securityLevel: 'loose'
});
mermaid.init();

function updatePushAlongGraph(change_field, action_field, new_value, old_value) {
	// Create object which gets the values of all input, textarea, select and button elements from the form
	var myFields = document.forms['EditView'].elements;
	var sentForm = new Object();
	for (var f=0; f<myFields.length; f++) {
		if (myFields[f].type=='checkbox') {
			sentForm[myFields[f].name] = myFields[f].checked;
		} else if (myFields[f].type=='radio' && myFields[f].checked) {
			sentForm[myFields[f].name] = myFields[f].value;
		} else if (myFields[f].type!='radio') {
			sentForm[myFields[f].name] = myFields[f].value;
		}
	}
	//JSONize form data
	sentForm = JSON.stringify(sentForm);
	let record = document.getElementsByName('record')[0].value;
	let flowid = {$pflowid};
	{literal}
	var params = `&${csrfMagicName}=${csrfMagicToken}&structure=${sentForm}`;
	{/literal}
	return fetch(
		'index.php?module=cbProcessFlow&action=cbProcessFlowAjax&file=pushAlongFlow&editmode=1&id='+record+'&pflowid='+flowid,
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: "same-origin",
			body: params
		}
	).then(response => response.text())
	.then(graph => {
		// document.getElementById('processflowpushalong').innerHTML = graph.substring(48, graph.indexOf('</div> <script>'));
		// document.getElementById('processflowpushalong').setAttribute('data-processed', false);
		// mermaid.init();
		mermaid.render('processflowpushalongthrowaway', graph.substring(48, graph.indexOf('</div> <script>')), (svg) => {
			document.getElementById('processflowpushalong').innerHTML = svg;
		});
	});
}
</script>
