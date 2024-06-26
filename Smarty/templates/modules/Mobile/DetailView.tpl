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
	<link rel="stylesheet" href="resources/css/signature-pad.css">
	<script type="text/javascript" src="resources/jquery.blockUI.js" ></script>
	<script type="text/javascript" src="resources/crmtogo.js"></script>
	<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
	<script type="text/javascript" src="resources/signature_pad.js"></script>
</head>
<body>
<div data-role="page" data-theme="b" id="detail_page">
	<input type="hidden" name="recordid" id="recordid" value="{$_RECORD->id()}">
	<input type="hidden" name="module" id="module" value="{$_MODULE->name()}">
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<div class="ui-btn-left" data-role="controlgroup" data-type="horizontal">
	{if $_MODULE->name() neq 'Quotes' && $_MODULE->name() neq 'SalesOrder' && $_MODULE->name() neq 'Invoice' && $_MODULE->name() neq 'PurchaseOrder' && $_MODULE->name() neq 'Documents'}
		<a href="?_operation=edit&module={$_MODULE->name()}&record={$_RECORD->id()}" class="ui-btn ui-corner-all ui-icon-edit ui-btn-icon-notext" data-transition="slideup" >{$MOD.LBL_EDIT}</a>
		<a href="?_operation=getrelatedlists&module={$_MODULE->name()}&record={$_RECORD->id()}" class="ui-btn ui-corner-all ui-icon-bars ui-btn-icon-notext" data-transition="slideup" >{$MOD.LBL_RELATED_LISTS}</a>
		<a href="?_operation=duplicate&module={$_MODULE->name()}&record={$_RECORD->id()}&duplicatedfrom={$_RECORD->id()}" class="ui-btn ui-corner-all ui-icon-recycle ui-btn-icon-notext" data-transition="slideup" >{$MOD.LBL_DUPLICATE}</a>
		<a href="?_operation=listModuleRecords&module={$_MODULE->name()}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-transition="slideup" >{$_MODULE->label()}</a>
		<a href="#" onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
	{else}
		<a href="?_operation=getrelatedlists&module={$_MODULE->name()}&record={$_RECORD->id()}" class="ui-btn ui-corner-all ui-icon-bars ui-btn-icon-notext" data-transition="slideup" >{$MOD.LBL_RELATED_LISTS}</a>
		<a href="#" onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
	{/if}
		</div>
		<h2></h2>
			<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
	</div>
	<div>
	{if $_MODULE->name() eq "HelpDesk"}
		<div data-role="collapsible" id="signatureCollapsible" data-collapsed="true" data-mini="true">
			<h3>{'LBL_SIGNATURE'|@getTranslatedString:'Mobile'}</h3>
			{include file="modules/Mobile/Signature.tpl"}
		</div>
	{/if}
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
			<div data-role="collapsible" id="{$_BLOCKLABEL}" data-collapsed="false" data-mini="true">
				<h3>{$_BLOCKLABEL|@getTranslatedString:$_MODULE->name()}</h3>
				{foreach item=_FIELD from=$_FIELDS}
					<input type="hidden" name="{$_FIELD->name()}" id="{$_FIELD->name()}" value="{$_FIELD->valueLabel()}">
					<div class="ui-grid-b">
						{if ($_FIELD->uitype() eq '69' || $_FIELD->uitype() eq '69m') && $_FIELD->valueLabel() neq ''}
							<img src="{$_FIELD->valueLabel()}" style="max-width:100%">
						{else}
						<div class="ui-bar ui-bar-b ui-corner-all">
							{if $_MODULE->name() eq 'cbCalendar'}
								{if $_FIELD->name() eq 'date_start'}
									{'Start Date'|@getTranslatedString:$_MODULE->name()}:
								{elseif $_FIELD->name() neq 'reminder_time' && $_FIELD->name() neq 'recurringtype' && $_FIELD->name() neq 'duration_hours' && $_FIELD->name() neq 'duration_minutes' && $_FIELD->name() neq 'notime' && $_FIELD->name() neq 'location' && $_FIELD->name() neq 'dtstart' && $_FIELD->name() neq 'dtend'}
									{if ($_FIELD->name() neq 'eventstatus') || $_FIELD->valueLabel() neq ''}
										{$_FIELD->label()}:
									{/if}
								{/if}
							{else}
								{$_FIELD->label()}:
							{/if}
						</div>
						<div class="ui-bar ui-bar-c ui-corner-all" style="height:50px;">
							{if $_FIELD->isReferenceType() && $_FIELD->uitype() neq '53' && $_FIELD->uitype() neq '52'}
								{if $_FIELD->valueLabel() neq ''}
									<a class="ui-btn ui-btn-b ui-corner-all ui-icon-carat-r ui-btn-icon-right" href="index.php?_operation=fetchRecord&record={$_FIELD->value()}" rel="external" data-theme="a">
										<span class="ui-btn-inner">
											<span class="ui-btn-text">{$_FIELD->valueLabel()}</span>
										</span>
									</a>
								{/if}
							{else}
								{if $_MODULE->name() eq 'cbCalendar' && $_FIELD->name() neq 'reminder_time' && $_FIELD->name() neq 'recurringtype' && $_FIELD->name() neq 'duration_hours' && $_FIELD->name() neq 'duration_minutes' && $_FIELD->name() neq 'notime' && $_FIELD->name() neq 'location' && $_FIELD->name() neq 'dtstart' && $_FIELD->name() neq 'dtend'}
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
											{if $_FIELD->name() neq 'eventstatus' || $_FIELD->valueLabel() neq ''}
												{$_FIELD->valueLabel()|@getTranslatedString:$_MODULE->name()}
											{/if}
										{/if}
									{/if}
								{elseif $_MODULE->name() neq 'cbCalendar'}
									{if $_FIELD->uitype() eq '56'}
										{if $_FIELD->valueLabel() eq '1'}
											{$MOD.LBL_YES}
										{else}
											{$MOD.LBL_NO}
										{/if}
									{else}
										{if $_FIELD->name() eq 'phone' || $_FIELD->name() eq 'homephone'|| $_FIELD->name() eq 'mobile'|| $_FIELD->name() eq 'otherphone' || $_FIELD->uitype() eq '11' }
											{assign var=phoneinput value=$_FIELD->valueLabel()}
											<a href="tel:{$phoneinput|regex_replace:"/\A\+/":"00"|regex_replace:"/[^0-9]+/":""}">{$_FIELD->valueLabel()}</a>
										{elseif $_FIELD->name() eq 'skype'}
											<a href="skype:{$_FIELD->valueLabel()}">{$_FIELD->valueLabel()} </a>
										{elseif $_FIELD->uitype() eq 'crm_app_map'}
											<a href="https://maps.google.com/maps?q={$_FIELD->valueLabel()}" target="_blank" class="ui-btn ui-corner-all ui-icon-location ui-btn-icon-right" data-rel="dialog">Google Maps: {$_FIELD->label()}
											</a>
										{elseif $_FIELD->uitype() eq '13'}
											<a href="#" onclick="window.location.href ='mailto:{$_FIELD->valueLabel()}';">{$_FIELD->valueLabel()} </a>
										{elseif $_FIELD->uitype() eq '5' || $_FIELD->uitype() eq '23'}
											{$_FIELD->valueLabel()}
										{elseif $_FIELD->uitype() eq '9'}
											{$_FIELD->valueLabel()}{if $_FIELD->name() eq 'probability'} %{/if}
										{elseif $_FIELD->uitype() eq '17'}
											{if substr($_FIELD->valueLabel(), 0, 4)=='http'}
												{assign var=prefix value=''}
											{else}
												{assign var=prefix value='https://'}
											{/if}
											<a href="#" onclick="window.open('{$prefix}{$_FIELD->valueLabel()}','_blank');" rel=external> {$_FIELD->valueLabel()} </a>
										{elseif ($_FIELD->uitype() eq '69' || $_FIELD->uitype() eq '69m')}
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
						{/if}
					</div>
				{/foreach}
			</div>
		{/foreach}
	</div>
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<a href="?_operation=deleteConfirmation&module={$_MODULE->name()}&record={$_RECORD->id()}&&lang={$LANGUAGE}" class="ui-btn ui-corner-all ui-icon-delete ui-btn-icon-notext" data-rel="dialog" data-iconpos="left">{$MOD.LBL_DELETE}</a>
		{if $_MODULE->name() eq "HelpDesk" && 'Timecontrol'|vtlib_isModuleActive}
		<a href="?_operation=create&module=Timecontrol&record=''&relatedto={$_RECORD->id()}&returnto={$_RECORD->id()}" class="ui-btn ui-btn-right ui-corner-all ui-icon-clock ui-btn-icon-notext" rel="external" data-transition="slideup" data-iconpos="right">{$MOD.LBL_NEW}</a>
		{/if}
		<a style="right: 20%" href="?_operation=create&module=Documents&record=''&relations={$_RECORD->id()}&returnto={$_RECORD->id()}" class="ui-btn ui-btn-right ui-corner-all ui-icon-camera ui-btn-icon-notext" rel="external" data-transition="slideup" data-iconpos="right">{$MOD.LBL_NEW}</a>
	</div>
	{include file="modules/Mobile/PanelMenu.tpl"}
</div>
</body>
{/strip}
