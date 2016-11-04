<div data-role="panel" id="panelmenu" data-position="left" data-display="overlay">
	<div id="loginTop"  style="text-align:center;"><img src="../../test/logo/{$COMPANY_DETAILS.logo}" style="width: 15em;height: 4.2em;"></div>
	<h4>{$MOD.LBL_MOD_LIST}</h4>
	<div  data-role="fieldcontain" data-mini="true">
		{literal}
		<script type="text/javascript">
			function fn_submit() {
				document.form.submit();
			}
		</script>
		{/literal}
		<form  name="form"  method="post" action="?_operation=globalsearch&module={$_MODULES[0]->name()}" target="_self">
			<input type="hidden" name="parenttab" value="{$CATEGORY}" style="margin:0px">
			<input type="hidden" name="search_onlyin" value="{$SEARCHIN}" style="margin:0px">
			<input type="text" data-inline="true" name="query_string" value="{$QUERY_STRING}"  >
			<a data-role="button" data-inline="true" class="ui-grid-b ui-responsive" data-mini='true' href="#" onclick="fn_submit();" target="_self" >{'LBL_SEARCH'|@getTranslatedString:'Mobile'}</a>
		</form>
	</div>
	<div data-role="collapsible-set"   data-mini="true">
		<ul data-role="listview" data-theme="c" >
		{foreach item=_MODULELIST from=$_MODULES}
			<li data-icon="false"><a href="index.php?_operation=listModuleRecords&module={$_MODULELIST->name()}" target="_self" >{$_MODULELIST->label()}</a></li>
		{/foreach}
		</ul>
	</div>
	<a href="index.php?_operation=logout" data-mini='true' data-role='button' data-inline='true'>Logout</a>
</div>
