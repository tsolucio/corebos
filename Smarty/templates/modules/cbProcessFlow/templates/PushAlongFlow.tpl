<div id="processflowpushalong" class="{if $SHOW_GRAPH_AS=='MERMAID'}mermaid{else}slds-grid slds-wrap{/if}">
{$FLOWGRAPH}
</div>
<script>
window.processflowmoveto{$pflowid} = function (tostate, forrecord, askifsure, minfo) {
	if (askifsure) {
		$ans = confirm(alert_arr.WANT_TO_CONTINUE);
	} else {
		$ans = true;
	}
	if ($ans) {
		if (minfo) {
			let params='&minfo='+minfo+'&tostate='+tostate+'&fieldName={$fieldName}&bpmmodule={$module}&uitype={$uitype}&editmode={$isInEditMode}&pflowid={$pflowid}&bpmrecord='+forrecord;
			window.open('index.php?action=cbProcessInfoAjax&file=bpmpopup&module=cbProcessInfo'+params, null, cbPopupWindowSettings + ',dependent=yes');
		} else {
			{if $isInEditMode}
			document.getElementById('{$fieldName}').value = tostate;
			{else}
			var txtBox = 'txtbox_{$fieldName}';
			document.getElementById(txtBox).value = tostate;
			document.getElementById('cbcustominfo2').value = '{$pflowid}';
			dtlViewAjaxSave('{$fieldName}', '{$module}', '{$uitype}', '', '{$fieldName}', forrecord);
			{/if}
		}
	}
	return false;
}
{if $SHOW_GRAPH_AS=='MERMAID'}
mermaid.initialize({
	securityLevel: 'loose'
});
mermaid.init();
{/if}
function updatePushAlongGraph(change_field, action_field, new_value, old_value) {
	// Create object which gets the values of all input, textarea, select and button elements from the form
	var myFields = document.forms['EditView'].elements;
	var sentForm = new Object();
	for (var f=0; f<myFields.length; f++) {
		if (myFields[f].type=='checkbox') {
			if (myFields[f].checked) {
				sentForm[myFields[f].name] = 'on';
			}
		} else if (myFields[f].type=='radio' && myFields[f].checked) {
			sentForm[myFields[f].name] = myFields[f].value;
		} else if (myFields[f].type == 'select-multiple') {
			var myFieldValue = Array.prototype.map.call(myFields[f].selectedOptions, function (x) {
				return x.value;
			}).join(' |##| ');
			sentForm[myFields[f].name.substring(0, myFields[f].name.length-2)] = myFieldValue;
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
		{if $SHOW_GRAPH_AS=='MERMAID'}
			// document.getElementById('processflowpushalong').innerHTML = graph.substring(48, graph.indexOf('</div> <script>'));
			// document.getElementById('processflowpushalong').setAttribute('data-processed', false);
			// mermaid.init();
			mermaid.render('processflowpushalongthrowaway', graph.substring(48, graph.indexOf('</div> <script>')), (svg) => {
				document.getElementById('processflowpushalong').innerHTML = svg;
			});
		{else}
			document.getElementById('processflowpushalong').innerHTML = graph;
		{/if}
	});
}
</script>
