<!DOCTYPE html>
<title>Home</title> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8"> 
<link REL="SHORTCUT ICON" HREF="resources/images/favicon.ico">	
<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="resources/css/jquery.mobile.icons.min.css" >
<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css" >
<link rel="stylesheet" href="resources/css/theme.css" >
<script  type="text/javascript" src="resources/jquery-ui.min.js"></script>
<script>  
    // rename to avoid conflict with jquery mobile
    $.fn.uislider = $.fn.slider;
</script>
<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
<link rel="stylesheet" href="resources/css/jquery-ui.min.css">
<script type="text/javascript" src="resources/jquery.blockUI.js" ></script>
<script type="text/javascript" src="resources/crmtogo.js" ></script>
<script type="text/javascript" src="resources/settings.js" ></script>
<div data-role="page" data-theme="b" id="home_page">
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed" class="ui-grid-b ui-responsive">
		<a href="index.php?_operation=logout" class="ui-btn ui-corner-all ui-icon-power ui-btn-icon-notext" >Logout</a>
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
			<table style="width:100%">
				<tr >
					<td>
						<input type="text" data-inline="true" name="query_string" value="{$QUERY_STRING}">
					</td>
					<td>
						<a href="#"  onclick="fn_submit();" target="_self"  class="ui-btn ui-btn-inline ui-icon-search ui-btn-icon-notext ui-corner-all ui-shadow"></a>
					</td>
				</tr>
 			</table>
       </form>
	</div>
    <div data-role="collapsible-set"   data-mini="true">	
        <ul data-role="listview" data-theme="c" id="homesortable">
		{foreach item=_MODULE from=$_MODULES}
			{if $_MODULE->active()}
			<li id={$_MODULE->name()}><a href="index.php?_operation=listModuleRecords&module={$_MODULE->name()}" class="ui-btn ui-btn-icon-right ui-icon-carat-r" target="_blank">{$_MODULE->label()}</a></li>
			{/if}
		{/foreach}	
		</ul>
	</div>
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<a href="?_operation=configCRMTOGO" class="ui-btn ui-corner-all ui-icon-gear ui-btn-icon-notext" data-iconpos="left" data-transition="slidedown" data-prefetch></a>
	</div>
</div>