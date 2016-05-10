<!DOCTYPE html>
<head>
	<title>Home</title> 
	<link REL="SHORTCUT ICON" HREF="../../themes/images/crm-now_icon.ico">	
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<script src="Js/jquery-1.11.2.min.js"></script>
	<link rel="stylesheet" href="Css/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="stylesheet" href="Css/theme.css" />
	<script src="Js/jquery.mobile-1.4.5.min.js"></script>
	<script type="text/javascript" src="Mobile.js"></script>
</head>
<body>
<div data-role="page" data-theme="b" id="home_page">
	<div data-role="header" data-theme="b" data-position="fixed" class="ui-grid-b ui-responsive">
		<a  href="index.php?_operation=logout" data-mini='true' data-role='button' data-inline='true'>Logout</a>
		<h4>{$MOD.LBL_MOD_LIST}</h4>
	</div><!-- /header -->
	<div  data-role="fieldcontain" data-mini="true">
		{literal}
		<script type="text/javascript">
			function fn_submit() {
				document.form.submit();
			}
		</script>
		{/literal}
		<form  name="form"  method="post" action="?_operation=globalsearch&module={$_MODULES[0]->name()}" target="_blank">
			<input type="hidden" name="parenttab" value="{$CATEGORY}" style="margin:0px">
			<input type="hidden" name="search_onlyin" value="{$SEARCHIN}" style="margin:0px">
			<input type="text" data-inline="true" name="query_string" value="{$QUERY_STRING}"  >
			<a data-role="button" data-inline="true" class="ui-grid-b ui-responsive" data-mini='true' href="#" onclick="fn_submit();" target="_self" >{$MOD.LBL_SEARCH}</a>
        </form>
	</div>
    <div data-role="collapsible-set"   data-mini="true">	
        <ul data-role="listview" data-theme="c" >
		{foreach item=_MODULE from=$_MODULES}
			<li data-icon="false"><a href="index.php?_operation=listModuleRecords&module={$_MODULE->name()}" target="_blank">{$_MODULE->label()}</a></li>
		{/foreach}	
		</ul>
	</div>
</div>		
</body>

