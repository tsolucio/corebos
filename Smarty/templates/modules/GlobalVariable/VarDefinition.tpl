{literal}
<script type="text/javascript" src="modules/GlobalVariable/tablesorter/jquery.tablesorter.min.js"></script>
<style>
	.gvdefstable table { border-collapse: collapse; text-align: left; width: 100%; }
	.gvdefstable {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #006699; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; width: 96%; margin: auto; margin-top: 10px;}
	.gvdefstable table td, .gvdefstable table th { padding: 3px 10px; }
	.gvdefstable table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #006699), color-stop(1, #00557F) );background:-moz-linear-gradient( center top, #006699 5%, #00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#006699', endColorstr='#00557F');background-color:#006699; color:#ffffff; font-size: 15px; font-weight: bold; border-left: 1px solid #0070A8; }
	.gvdefstable table thead th:first-child { border: none; }
	.gvdefstable table tbody td { color: #00496B; border-left: 1px solid #E1EEF4;font-size: 12px;font-weight: normal; }
	.gvdefstable table tbody .alt { background: #E1EEF4; color: #00496B; }
	.gvdefstable table tbody td:first-child { border-left: none; }
	.gvdefstable table tbody tr:hover { background-color: #DCDCDC }
	.gvdefstable table tbody tr:last-child td { border-bottom: none; }
</style>
{/literal}
<div class="cb-alert-info">{$GlobalVariableDefinitonsHeader.UseDescriptionMessage}</div>
<div class='gvdefstable'>
<table id="gvdefstable">
<thead>
	<tr>
		<th>{'Name'|@getTranslatedString}</th>
		<th>{'LBL_STATUS'|@getTranslatedString}</th>
		<th>{$GlobalVariableDefinitonsHeader.valuetype}</th>
		<th>{$GlobalVariableDefinitonsHeader.values}</th>
		<th>{$GlobalVariableDefinitonsHeader.definition}</th>
		<th>{$GlobalVariableDefinitonsHeader.category}</th>
	</tr>
</thead>
<tbody>
{foreach item=def key=var from=$GlobalVariableDefinitons}
	<tr class="{cycle values=" ,alt"}">
	<td><b><a name="{$var}"></a>{$var}</b></td>
	<td>{$def.status}</td>
	<td>{$def.valuetype}</td>
	<td>{$def.values}</td>
	<td>{$def.definition}</td>
	<td>{$def.category}</td>
	</tr>
{/foreach}
</tbody>
</table>
</div>
{literal}
<script type="text/javascript">
<!--
jQuery('#gvdefstable').tablesorter();
//-->
</script>
{/literal}
