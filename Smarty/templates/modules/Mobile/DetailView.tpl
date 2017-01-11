{strip}
<!DOCTYPE html>
<head>
	<title>{$_MODULE->label()} {$MOD.LBL_DETAILS}</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta charset="utf-8">
	<link REL="SHORTCUT ICON" HREF="resources/images/favicon.ico">	
	<link rel="stylesheet" href="resources/css/jquery.mobile-1.4.5.min.css">	
	<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
	<script type="text/javascript" src="resources/getScrollcontent.js"></script>
	<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css" >
	<link rel="stylesheet" href="resources/css/theme.css" >
	<script type="text/javascript" src="resources/crmtogo.js"></script>
	<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
</head>
<body>
<div data-role="page" data-theme="b" id="detail_page">
	<input type="hidden" name="recordid" id="recordid" value="{$_RECORD->id()}">
	{if $_MODULE->name() neq 'Accounts' && $_MODULE->name() neq 'Contacts'&& $_MODULE->name() neq 'Potentials' && $_MODULE->name() neq 'HelpDesk' && $_MODULE->name() neq 'Assets'}
	<div  data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		{if $_MODULE->name() neq 'Quotes' AND  $_MODULE->name() neq 'SalesOrder' AND  $_MODULE->name() neq 'Invoice' AND  $_MODULE->name() neq 'PurchaseOrder' AND  $_MODULE->name() neq 'Documents' AND  $_MODULE->name() neq 'Products'}
			<div class="ui-btn-left" data-role="controlgroup" data-type="horizontal">
				<a href="?_operation=edit&module={$_MODULE->name()}&record={$_RECORD->id()}" class="ui-btn ui-corner-all ui-icon-edit ui-btn-icon-notext" data-transition="slideup" >{$MOD.LBL_EDIT}</a>
				<a href="#"  onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
			</div>
		{else}
			<a href="#"  onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
		{/if}
			<h2></h2>
			<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
	</div>
	{else}
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
	    <div class="ui-btn-left" data-role="controlgroup" data-type="horizontal">
			<a href="?_operation=edit&module={$_MODULE->name()}&record={$_RECORD->id()}" class="ui-btn ui-corner-all ui-icon-edit ui-btn-icon-notext" data-transition="slideup" >{$MOD.LBL_EDIT}</a>
			<a href="?_operation=getrelatedlists&module={$_MODULE->name()}&record={$_RECORD->id()}" class="ui-btn ui-corner-all ui-icon-bars ui-btn-icon-notext" data-transition="slideup" >{$MOD.LBL_RELATED_LISTS}</a>
			<a href="#"  onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
		</div>
		<h2></h2>
			<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
	</div>
	{/if}
	<div>
	{if $COMMENTDISPLAY eq true}
		<div data-role="collapsible" data-collapsed="true" data-mini="true">
			<h3>{$MOD.LBL_COMMENTS}</h3>
				<div id="comment_content">
				{include file='modules/Mobile/Comments.tpl'}
				</div>
			<div data-role="main" class="ui-content">
			  <div class="ui-field-contain">
				<textarea name="comment_text" id="comment_text"></textarea>
			  </div>
				<a class="ui-btn ui-btn-inline ui-shadow ui-corner-all ui-icon-comment ui-btn-icon-left" data-rel="dialog" id="savecomment" href="#">{$MOD.LBL_SAVE}</a>
			</div>
		</div>
	{/if}
		{foreach item=_BLOCK key=_BLOCKLABEL from=$_RECORD->blocks()}
			{assign var=_FIELDS value=$_BLOCK->fields()}	
			<div data-role="collapsible" data-collapsed="false" data-mini="true">
				<h3>{$_BLOCKLABEL|@getTranslatedString:$_MODULE->name()}</h3>
				{foreach item=_FIELD from=$_FIELDS}
					<div class="ui-grid-a">
						<div class="ui-block-a">
							{if $_MODULE->name() eq 'Calendar' || $_MODULE->name() eq 'Events'}
								{if $_FIELD->name() eq 'date_start'}
									{'Start Date'|@getTranslatedString:$_MODULE->name()}:
								{elseif $_FIELD->name() neq 'reminder_time' && $_FIELD->name() neq 'recurringtype' && $_FIELD->name() neq 'duration_hours' && $_FIELD->name() neq 'duration_minutes' && $_FIELD->name() neq 'notime' && $_FIELD->name() neq 'location'}
									{if ($_FIELD->name() neq 'eventstatus' && $_FIELD->name() neq 'taskstatus') || $_FIELD->valueLabel() neq ''}
										{$_FIELD->label()}:
									{/if}
								{/if}
							{else}
								{if $_FIELD->uitype() eq '69' && $_FIELD->valueLabel() neq ''}
									<img  src="{$_FIELD->valueLabel()}"></img>
								{else}
									{$_FIELD->label()}:
								{/if}
							{/if}
						</div>
						<div class="ui-block-b">
							{if $_FIELD->isReferenceType() && $_FIELD->uitype() neq '53' && $_FIELD->uitype() neq '52'}
								{if $_FIELD->valueLabel() neq ''}
									<a class="ui-btn  ui-corner-all ui-icon-carat-r ui-btn-icon-right" href="index.php?_operation=fetchRecord&record={$_FIELD->value()}" data-theme="c">
										<span class="ui-btn-inner">
											<span class="ui-btn-text">{$_FIELD->valueLabel()}</span>
										</span>
									</a>
								{/if}
							{else}
								{if ($_MODULE->name() eq 'Calendar' || $_MODULE->name() eq 'Events') && $_FIELD->name() neq 'reminder_time' && $_FIELD->name() neq 'recurringtype' && $_FIELD->name() neq 'duration_hours' && $_FIELD->name() neq 'duration_minutes' && $_FIELD->name() neq 'notime' && $_FIELD->name() neq 'location'}
									{if $_FIELD->name() eq 'date_start' ||$_FIELD->name() eq 'due_date'}
										{$_FIELD->valueLabel()}
									{else}
										{if $_FIELD->uitype() eq '56'}
											{if $_FIELD->valueLabel() eq '1'}
												{$MOD.LBL_YES}
											{else}
												{$MOD.LBL_NO}
											{/if}
										{else}
											{if ($_FIELD->name() neq 'eventstatus' && $_FIELD->name() neq 'taskstatus') || $_FIELD->valueLabel() neq ''}
												{$_FIELD->valueLabel()|@getTranslatedString:$_MODULE->name()}
											{/if}
										{/if}
									{/if}
								{elseif $_MODULE->name() neq 'Calendar' && $_MODULE->name() neq 'Events'}
									{if $_FIELD->uitype() eq '56'}
										{if $_FIELD->valueLabel() eq '1'}
											{$MOD.LBL_YES}
										{else}
											{$MOD.LBL_NO}
										{/if}
									{else}
										{if $_FIELD->name() eq 'phone' || $_FIELD->name() eq 'homephone'|| $_FIELD->name() eq 'mobile'|| $_FIELD->name() eq 'otherphone' }
											{assign var=phoneinput value=$_FIELD->valueLabel()}	
											<a href="tel:{$phoneinput|regex_replace:"/\A\+/":"00"|regex_replace:"/[^0-9]+/":""}">{$_FIELD->valueLabel()}</a>
										{elseif $_FIELD->name() eq 'skype'}
											<a href="skype:{$_FIELD->valueLabel()}">{$_FIELD->valueLabel()} </a>
										{elseif $_FIELD->uitype() eq 'crm_app_map'}
											<a  href="http://maps.google.com/maps?q={$_FIELD->valueLabel()}"  target="_blank" class="ui-btn  ui-corner-all ui-icon-location ui-btn-icon-right" data-rel="dialog">Google Maps: {$_FIELD->label()}
											</a>
										{elseif $_FIELD->uitype() eq '5' || $_FIELD->uitype() eq '23'}
											{$_FIELD->valueLabel()}
										{elseif $_FIELD->uitype() eq '9'}
											{$_FIELD->valueLabel()}{if $_FIELD->name() eq 'probability'} %{/if}
										{elseif $_FIELD->uitype() eq '17'}
											{$_FIELD->valueLabel()}
										{elseif $_FIELD->uitype() eq '69'}
											<!-- do nothing here for image -->
										{elseif $_FIELD->uitype() eq '70'}
											{$_FIELD->valueLabel()}
										{elseif $_FIELD->uitype() eq '71'}
											{$_FIELD->valueLabel()}
										{elseif $_FIELD->uitype() eq '28'}
											<a id="filedownload" href="#" data-ajax="false">{$_FIELD->valueLabel()} </a>
										{else}
											{$_FIELD->valueLabel()}
										{/if}
									{/if}
								{/if}
							{/if}
						</div>	
					</div>	
	            {/foreach}
			</div>	
		{/foreach}
	</div>
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<a href="?_operation=deleteConfirmation&module={$_MODULE->name()}&record={$_RECORD->id()}&&lang={$LANGUAGE}" class="ui-btn ui-corner-all ui-icon-delete ui-btn-icon-notext" data-rel="dialog" data-iconpos="left" data-prefetch>{$MOD.LBL_DELETE}</a>
	</div>
	{include file="modules/Mobile/PanelMenu.tpl"}
</div>
</body>
{/strip}