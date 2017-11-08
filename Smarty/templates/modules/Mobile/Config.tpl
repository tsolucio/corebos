{strip}
<!DOCTYPE html>
<head>
<title>{$MOD.LBL_CONFIG}</title> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8"> 
<link REL="SHORTCUT ICON" HREF="resources/images/favicon.ico">	
<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="resources/css/jquery.mobile.icons.min.css" >
<script  type="text/javascript" src="resources/jquery-ui.min.js"></script>
<script>  
    // rename to avoid conflict with jquery mobile
    $.fn.uislider = $.fn.slider;
</script>
<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css" >
<script type="text/javascript" src="resources/jquery.blockUI.js" ></script>
<link rel="stylesheet" href="resources/css/theme.css" >
<link rel="stylesheet" href="resources/css/theme.css" >
<link rel="stylesheet" href="resources/css/jquery-ui.min.css">
<script type="text/javascript" src="resources/settings.js" ></script>
</head>
<body>
<div data-role="page" data-theme="b" id="settings_page">
	<div id="header" data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed" class="ui-grid-b ui-responsive">
		<a href="#"  onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
		<h4>{$MOD.LBL_CONFIG}</h4>
		<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
	</div>
    <form>
	<div role="main" class="ui-content">
 		<div>
			{$MOD.LBL_SETTINGS_COMMENT}
		</div>
		<div class="ui-field-contain">
			<ul data-role="listview" data-divider-theme="b" data-inset="true" id="sortable">
            <li data-role="list-divider" role="heading">{$MOD.LBL_ACTIVE_MODULE}</li>
			{foreach item=_MODULE from=$_MODULES}
				<li data-theme="c" id={$_MODULE->name()}>
					<div data-role="fieldcontain">
						<label for="flip_{$_MODULE->name()}"><span style="display: inline-block" class="ui-icon ui-icon-arrowthick-2-n-s"></span>{$_MODULE->label()}:</label>
						{if $_MODULE->active() eq 1}
							<input data-role="flipswitch" data-on-text="{$MOD.LBL_ON}" data-off-text="{$MOD.LBL_OFF}" name="flip_{$_MODULE->name()}" id="flip_{$_MODULE->name()}" checked="" type="checkbox" >
						{else}
							<input data-role="flipswitch" data-on-text="{$MOD.LBL_ON}" data-off-text="{$MOD.LBL_OFF}" name="flip_{$_MODULE->name()}" id="flip_{$_MODULE->name()}" type="checkbox">		 
						{/if}
					</div>
				</li>
			{/foreach}
			</ul>
			<div>
				<fieldset data-role="controlgroup" id="themecolor">
					<legend>{$MOD.LBL_THEME_SELECTION}</legend>
						{assign var=$COLOR_HEADER_FOOTER|cat:"theme" value='checked="checked"'}
						<input type="radio" name="radio-choice-2" id="radio-choice-21" value="a" data-theme="c" {if isset($atheme)}{$atheme}{/if} />
						<label for="radio-choice-21">{$MOD.LBL_THEME_COLOR_A}</label>

						<input type="radio" name="radio-choice-2" id="radio-choice-22" value="b" data-theme="c" {if isset($btheme)}{$btheme}{/if} />
						<label for="radio-choice-22">{$MOD.LBL_THEME_COLOR_B}</label>

						<input type="radio" name="radio-choice-2" id="radio-choice-23" value="c" data-theme="c" {if isset($ctheme)}{$ctheme}{/if} />
						<label for="radio-choice-23">{$MOD.LBL_THEME_COLOR_C}</label>
				</fieldset>
			</div>		
			<div>
				<label for="slider-1">{$MOD.LBL_NAVI_SELECTION}</label>
				<input type="range" name="navislider" id="navislider" data-theme="b" data-track-theme="c" value="{$NAVISETTING}" min="5" max="25" data-highlight="true">
			</div>
		</div>
	</div>	
	</form>
	<div  id="footer" data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<h3></h3>
	</div>
	{include file="modules/Mobile/PanelMenu.tpl"}
</div>
</body>
{/strip}