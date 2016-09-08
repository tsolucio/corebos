<!DOCTYPE html>
<head>
	<title>{$_MODULE->label()} Detail</title> 
	<link REL="SHORTCUT ICON" HREF="../../themes/images/crm-now_icon.ico">	
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<script src="Js/jquery-1.11.2.min.js"></script>
	<link rel="stylesheet" href="Css/jquery.mobile.structure-1.4.5.min.css" />
	<script src="Js/jquery.mobile-1.4.5.min.js"></script>
	<link href="Css/mobiscroll.custom-2.6.2.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="Css/theme.css" />
	<link rel="stylesheet" href="Css/jquery.mobile.icons.min.css" />
	<script src="Js/mobiscroll.custom-2.6.2.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="Mobile.js"></script>
	<script src="Js/getScrollcontent.js"></script>
	<script src="Js/lang/{$LANGUAGE}.lang.js"></script>
	{literal}
	<style>
	.button-wrap {
		margin-left: 5px;
		margin-right: 5px;
	}
	</style>
	{/literal}
</head>
<body>
{assign var=dateFormat value="$DATEFORMAT"}
{assign var=timeStr value="$HOURFORMAT"}
<div data-role="page" data-theme="b">
	{if $_MODULE->name() neq 'Accounts'}
	<div data-role="header" class="ui-bar" data-mini='true' data-theme="b">
		<div class="ui-grid-b" data-role="controlgroup" data-type="horizontal">
		{if $_MODULE->name() neq 'Quotes' AND  $_MODULE->name() neq 'SalesOrder' AND  $_MODULE->name() neq 'Invoice' AND  $_MODULE->name() neq 'PurchaseOrder'}
		<a href="?_operation=edit&module={$_MODULE->name()}&record={$_RECORD->id()}" data-mini='true' data-role="button" data-prefetch>{'LBL_EDIT'|@getTranslatedString:'Mobile'}</a>
		{/if}
		<a data-role="button" data-inline="true" href="index.php?_operation=listModuleRecords&module={$_MODULE->name()}" data-mini='true' rel=external>{'LBL_LISTVIEW'|@getTranslatedString:'Mobile'}</a>
		</div>
	</div>
	{else}
	<div data-role="header" class="ui-bar" data-theme="b">
		<div class="ui-grid-b" data-role="controlgroup" data-type="horizontal">
				<a href="?_operation=edit&module={$_MODULE->name()}&record={$_RECORD->id()}" data-role="button"  data-prefetch>{'LBL_EDIT'|@getTranslatedString:'Mobile'}</a>
				<a href="?_operation=getrelatedlists&module={$_MODULE->name()}&record={$_RECORD->id()}" data-role="button"  data-prefetch>{'LBL_RELATED_LISTS'|@getTranslatedString:'Mobile'}</a>
				<a href="index.php?_operation=listModuleRecords&module={$_MODULE->name()}"  data-role="button"   rel=external>{'LBL_LISTVIEW'|@getTranslatedString:'Mobile'}</a>
			
		</div>
	</div>
	{/if}
	<div>
	{if isset($_COMMENTS) && ($COMMENTDISPLAY eq true)}
		<div data-role="collapsible" data-collapsed="true" data-mini="true">
			<h3>{'ModComments'|@getTranslatedString:'ModComments'}</h3>
				<div id="comment_content">
				{include file='modules/Mobile/generic/Comments.tpl'}
				</div>
			<div data-role="main" class="ui-content">
			  <div class="ui-field-contain">
				<textarea name="comment_text" id="comment_text"></textarea>
			  </div>
			  <input type="submit" data-inline="true" value="{'LBL_SAVE'|@getTranslatedString:'Mobile'}" onClick="addComment('{$_RECORD->id()}');">
			</div>
		</div>
	{/if}
		{foreach item=_BLOCK key=_BLOCKLABEL from=$_RECORD->blocks()}
			{assign var=_FIELDS value=$_BLOCK->fields()}	
			<div data-role="collapsible" data-collapsed="false" data-mini="true">
				<h3>{$_BLOCKLABEL}</h3>
				{foreach item=_FIELD from=$_FIELDS}
					<div class="ui-grid-a">
						<div class="ui-block-a">
							{if $_MODULE->name() eq 'Calendar' || $_MODULE->name() eq 'Events'}
								{if $_FIELD->name() eq 'date_start'}
									{'Start Date & Time'}:
								{elseif $_FIELD->name() neq 'reminder_time' && $_FIELD->name() neq 'time_end' && $_FIELD->name() neq 'recurringtype' && $_FIELD->name() neq 'duration_hours' && $_FIELD->name() neq 'duration_minutes' && $_FIELD->name() neq 'notime' && $_FIELD->name() neq 'location'}
									{if ($_FIELD->name() neq 'eventstatus' && $_FIELD->name() neq 'taskstatus') || $_FIELD->valueLabel() neq ''}
										{$_FIELD->label()}:
									{/if}
								{/if}
							{else}
								{$_FIELD->label()}:
							{/if}
						</div>
						<div class="ui-block-b">
							{if $_FIELD->isReferenceType() && $_FIELD->uitype() neq '53'}
								{if $_FIELD->valueLabel() neq ''}
									<a class=" ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c " href="index.php?_operation=fetchRecordWithGrouping&record={$_FIELD->value()}" data-role="button" data-corners="true" data-shadow="true" data-wrapperels="span" data-theme="c">
										<span class="ui-btn-inner">
											<span class="ui-btn-text">{$_FIELD->valueLabel()|@getTranslatedString:$_MODULE->name()}</span>
										</span>
									</a>
								{/if}
							{else}
								{if ($_MODULE->name() eq 'Calendar' || $_MODULE->name() eq 'Events') && $_FIELD->name() neq 'reminder_time' && $_FIELD->name() neq 'time_end' && $_FIELD->name() neq 'recurringtype' && $_FIELD->name() neq 'duration_hours' && $_FIELD->name() neq 'duration_minutes' && $_FIELD->name() neq 'notime' && $_FIELD->name() neq 'location'}
									{if $_FIELD->name() eq 'date_start' ||$_FIELD->name() eq 'due_date'}
										{$_FIELD->valueLabel()|date_format:$dateFormat}
									{else}
										{if $_FIELD->uitype() eq '56'}
											{if $_FIELD->valueLabel() eq '1'}
												{'LBL_YES'|@getTranslatedString:'Mobile'}
											{else}
												{'LBL_NO'|@getTranslatedString:'Mobile'}
											{/if}
										{else}
											{if ($_FIELD->name() neq 'eventstatus' && $_FIELD->name() neq 'taskstatus') || $_FIELD->valueLabel() neq ''}
												{$_FIELD->valueLabel()}
											{/if}
										{/if}
									{/if}
								{elseif $_MODULE->name() neq 'Calendar' && $_MODULE->name() neq 'Events'}
									{if $_FIELD->uitype() eq '56'}
										{if $_FIELD->valueLabel() eq '1'}
											{'LBL_YES'|@getTranslatedString:'Mobile'}
										{else}
											{'LBL_NO'|@getTranslatedString:'Mobile'}
										{/if}
									{else}
										{if $_FIELD->name() eq 'phone' || $_FIELD->name() eq 'homephone'|| $_FIELD->name() eq 'mobile'|| $_FIELD->name() eq 'otherphone' }
											{assign var=phoneinput value=$_FIELD->valueLabel()}	
											{assign var=phoneinput value=$phoneinput|regex_replace:"/[\A\+]/":"00"}
											{assign var=phoneinput value=$phoneinput|regex_replace:"/[^0-9]/":""}
											<a href="tel:{$phoneinput}">{$_FIELD->valueLabel()}</a>
										{elseif $_FIELD->name() eq 'skype'}
											<a href="skype:{$_FIELD->valueLabel()}">{$_FIELD->valueLabel()}</a>
										{elseif $_FIELD->uitype() eq 'crm_app_map'}
											<a target="_blank" class="ui-btn-corner-all ui-btn-up-c  ui-btn ui-shadow"  href="http://maps.google.com/maps?q={$_FIELD->valueLabel()}" data-role="button" data-corners="true" data-shadow="true" data-wrapperels="span" data-theme="c" role="button">
												<span class="ui-btn-inner">
													Google Maps: {$_FIELD->label()}
												</span>
											</a>
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
	<div class="ui-grid-b">
		<a href="?_operation=deleteConfirmation&module={$_MODULE->name()}&record={$_RECORD->id()}&&lang={$LANGUAGE}" data-mini='true' data-role='button' data-inline='true' data-rel="dialog" data-transition="turn" data-prefetch>{'LBL_DELETE'|@getTranslatedString:'Mobile'}</a>
	</div> 
	{if $_MODULE->name() neq 'Accounts'}
	{/if}	
</div>
</body>