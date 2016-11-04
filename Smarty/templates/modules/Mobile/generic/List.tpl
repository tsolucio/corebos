<!DOCTYPE html>
<head>
	<title>{$_MODULE->label()} {$MOD.LBL_LIST}</title> 
	<link REL="SHORTCUT ICON" HREF="../../themes/images/crm-now_icon.ico">	
	<link rel="stylesheet" href="Css/eventCalendar.css">
	<link rel="stylesheet" href="Css/eventCalendar_theme_responsive.css">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<script src="Js/jquery-1.11.2.min.js"></script>
	<link rel="stylesheet" href="Css/jquery.mobile.structure-1.4.5.min.css" />
	<script src="Js/jquery.mobile-1.4.5.min.js"></script>
	<link href="Css/mobiscroll.custom-2.6.2.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="Css/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="Css/theme.css" />
	<!-- <link rel="stylesheet" href="Css/crmnow.min.css" /> -->
	<script src="Js/mobiscroll.custom-2.6.2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="Mobile.js"></script>
	<script src="Js/getScrollcontent.js"></script>
	<script src="Js/lang/{$LANGUAGE}.lang.js"></script>
	<script type="text/javascript">value_offset = {$_PAGER->_limit}; module = '{$_MODULE->name()}'; view = {$_VIEW}; viewName = '{$_VIEWNAME}';</script>
	<script>
	{literal}
	var search = '';
	var tmp_src = '';
	var timer = 0;
	// initialize
	$( document ).delegate("#list_page", "pageinit", function() {
		scroller(module, view, '');
		$('#inputer').bind('input', locker);
		$('#inputer').bind('remove', locker);
		$('#inputer').bind('change', locker);
		$('#inputer').bind('blur', locker);
		$('#inputer').bind('keyup', locker);
		$('#inputer').bind('cut', locker);
		$('#inputer').bind('paste', locker);
	});

	function scroller(module, view, search) {
		$('#content').scrollPagination({

			nop     : value_offset, // The number of posts per scroll to be loaded
			offset  : 0, // Initial offset, begins at 10 like in Config
			error   : mobiscroll_arr.ALERT_POSTS , // When the user reaches the end this is the message that is
										// displayed. You can change this if you want.
			delay   : 500, // When you scroll down the posts will load after a delayed amount of time.
						   // This is mainly for usability concerns. You can alter this as you see fit
			scroll  : true, // The main bit, if set to false posts will not load as the user scrolls. 
						   // but will still load if the user clicks.
			module	: module, //just as an example
			view	: view,
			viewName: viewName,
			search  : search,
		});
	}

	function locker() {
		if (tmp_src == document.getElementById('inputer').value){ 
			return;
		}
		tmp_src = document.getElementById('inputer').value;
		var tmp_src2 = document.getElementById('inputer').value;
		
		window.setTimeout(function(){doSearch(tmp_src2)}, 1000);
	};
	
	function doSearch(src) {
		if (src == document.getElementById('inputer').value) {
			document.getElementById('content').innerHTML = '';
			scroller(module, view, document.getElementById('inputer').value);
		}
	};
{/literal}
{if $_MODULE->name() eq 'Calendar'}
{literal}
	$(document).ready(function() {
		$("#eventCalendarNoCache").eventCalendar({
			eventsjson: 'events.json.php',
			showDescription: true,
			eventsLimit: 36,
			monthNames: cal_config_arr.monthNames,
			dayNames: cal_config_arr.dayNames,
			dayNamesShort: cal_config_arr.dayNamesShort,
			txt_noEvents: cal_config_arr.txt_noEvents,
			txt_SpecificEvents_prev: "",
			txt_SpecificEvents_after: cal_config_arr.txt_SpecificEvents_after,
			txt_next: cal_config_arr.txt_next,
			txt_prev: cal_config_arr.txt_prev,
			txt_NextEvents: cal_config_arr.txt_NextEvents,
			txt_GoToEventUrl: cal_config_arr.txt_GoToEventUrl,
			txt_NumAbbrevTh: cal_config_arr.txt_NumAbbrevTh,
			txt_NumAbbrevSt: cal_config_arr.txt_NumAbbrevSt,
			txt_NumAbbrevNd: cal_config_arr.txt_NumAbbrevNd,
			txt_NumAbbrevRd: cal_config_arr.txt_NumAbbrevRd,
			txt_loading: "loading...",
			cacheJson: true
		});
{/literal}
		var CALENDARSELECT = $('#calendarselect').val();
{literal}
		if (CALENDARSELECT=='on') {
			$('#content').css('display','none');
			$('#inputer').css('display','none');
			$('#viewname-button').css('display','none');
			$('#viewname').css('display','none');
			$('#eventCalendarNoCache').css('display','block');
		}
		else {
			$('#eventCalendarNoCache').css('display','none');
			$('#content').css('display','block');
			$('#inputer').css('display','block');
			$('#viewname-button').css('display','block');
			$('#viewname').css('display','block');
		}
		$('#calslider').change(function() {
			var myswitch = $(this);
			var show     = myswitch[0].selectedIndex == 1 ? true:false;
			if(show) {
				$('#eventCalendarNoCache').css('display','none');
				$('#content').css('display','block');
				$('#inputer').css('display','block');
				$('#viewname-button').css('display','block');
				$('#viewname').css('display','block');
				document.getElementById('calendarselect').value = 'on';
			} 
			else {
				$('#content').css('display','none');
				$('#inputer').css('display','none');
				$('#viewname-button').css('display','none');
				$('#viewname').css('display','none');
				$('#eventCalendarNoCache').css('display','block');
				document.getElementById('calendarselect').value = '';
			}
		});	
	});	
{/literal}
{/if}
	</script>

</head>
<body>
<div data-role="page" data-theme="b"  id="list_page">

	<!-- Calendar Settings -->
	{if $_MODULE->name() eq 'Calendar'}
	<input type="hidden" name="calendarselect" id="calendarselect" value="{$CALENDARSELECT}">
	{/if}
	<!-- header -->
	<div data-role="header" class="ui-bar" data-theme="b" data-position="fixed" class="ui-grid-b ui-responsive">
		{if $_MODULE->name() eq 'Calendar'}
			<div style="position: absolute;right: 35px;text-align: right;"> 
				<div style="position:relative; top:0;right:45px;">
					<select name="slider" id="calslider" data-role="slider" data-mini='true'> 
						<option value="off">{$MOD.LBL_OFF}</option> 
						<option value="on">{$MOD.LBL_ON}</option> 
					</select>
				</div>
				<div style="position:relative; top:-48px;right:5px;">
					<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
				</div>
			</div>
		{/if}
		{if $_MODULE->name() neq 'Calendar' AND $_MODULE->name() neq 'Quotes' AND  $_MODULE->name() neq 'SalesOrder' AND  $_MODULE->name() neq 'Invoice' AND  $_MODULE->name() neq 'PurchaseOrder'}
			<!-- without prefetch
			<a  href="?_operation=create&module={$_MODULE->name()}&record=''"  data-mini='true' data-role='button' data-inline='true' >{$MOD.LBL_NEW}</a>
			 -->
			<div>
			<a  href="?_operation=create&module={$_MODULE->name()}&record=''"  data-mini='true' data-role='button' data-inline='true' data-transition="turn">{$MOD.LBL_NEW}</a>
			<a  href="?_operation=create&module={$_MODULE->name()}&record=''&quickcreate=1"  data-mini='true' data-role='button' data-inline='true' data-transition="turn">{$MOD.LBL_QUICKCREATE}</a>
			</div>
		{elseif $_MODULE->name() eq 'Calendar'}
			<!-- select task or event -->
			<a href="?_operation=createActivity&lang={$LANGUAGE}" class="ui-btn ui-corner-all" data-mini='true' data-role='button' data-inline='true' data-rel="dialog" data-transition="turn" data-prefetch>{$MOD.LBL_NEW}</a>
		{/if}
		
		<h4>{$_MODULE->label()}</h4>
		{if $_MODULE->name() neq 'Calendar'}
			<div style="position: absolute;top: 0;right: 35px;text-align: right;">
				<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
			</div>
		{/if} 
	</div>
	<!-- /header -->
	{if $_MODULE->name() eq 'Calendar'}	
	<div id="eventCalendarNoCache" class="ui-input-text ui-body-c" width="100%"></div>
	{/if}
	<div data-role="content">
		<SELECT NAME="viewname" id="viewname" class="select" data-native-menu="false" style ="display:none" onchange="showDefaultCustomView(this,'{$_MODULE->name()}','{$_CATEGORY}')" >{$_CUSTOMVIEW_OPTION}</SELECT>
	</div>
	<!-- Search input -->
	<input id="inputer" class="ui-input-text ui-body-c" placeholder="Filter ..." data-type="search">
	<div data-role="collapsible-set" data-mini="true">
		<ul class="ui-listview ui-group-theme-c" data-role="listview" data-theme="c" data-filter="false">
			{literal}
			<style type="text/css">
			#content .content li a.ui-btn {
				margin: 0px;
				display: block;
				position: relative;
				text-align: left;
				text-overflow: ellipsis;
				overflow: hidden;
				white-space: nowrap;
				border-width: 1px 0px 0px;
				border-style: solid;
			}
			#content .content li:last-child a.ui-btn {
				border-bottom-width:1px;
			}
			</style>
			{/literal}
			<!-- graphical calendar -->
			<div id="content"></div>
		</ul>
	</div>
	{include file="modules/Mobile/generic/PanelMenu.tpl"}
</div>

</body>
<script src="Js/moment.js" type="text/javascript"></script>
<script src="Js/jquery.eventCalendar.min.js" type="text/javascript"></script>
