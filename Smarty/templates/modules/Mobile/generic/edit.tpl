<!DOCTYPE html>
<head>
	<title>{$_MODULE->label()} {$MOD.LBL_EDIT}</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link REL="SHORTCUT ICON" HREF="../../themes/images/crm-now_icon.ico">	
	<script src="Js/jquery-1.11.2.min.js"></script>
	<link rel="stylesheet" href="Css/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="stylesheet" href="Css/theme.css" />
	<!-- <link rel="stylesheet" href="Css/crmnow.min.css" /> -->
	<link rel="stylesheet" href="Css/jquery.mobile.icons.min.css" />
	<script src="Js/jquery.mobile-1.4.5.min.js"></script>
	<link href="Css/mobiscroll.custom-2.6.2.min.css" rel="stylesheet" type="text/css" />
	<script src="Js/mobiscroll.custom-2.6.2.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="Mobile.js"></script>
	<script src="Js/getScrollcontent.js"></script>
	<script src="Js/lang/{$LANGUAGE}.lang.js"></script>
</head>
<body> 
<div data-role="page" data-theme="b" data-mini="true" id="edit_page">
	<!-- header -->
	<div data-role="header" class="ui-bar" data-theme="b"  data-position="fixed">
		<h4>{'LBL_EDIT'|@getTranslatedString:'Mobile'}</h4>
		<div style="position: absolute;top: 0;right: 35px;text-align: right;">
			<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
		</div>
	</div>
	<!-- /header -->
	<div data-role="collapsible-set" data-mini="true">	
		<form method="post" data-transition="pop" data-ajax="false" name="EditView" id="EditView" action="index.php?_operation=saveRecord&module={$_MODULE->name()}&record={$_RECORD->id()}">
			<div data-role="controlgroup" data-type="horizontal" data-mini="true">
					<input data-inline="true"  type="submit" name="submit" value="{'LBL_SAVE'|@getTranslatedString:'Mobile'}" />
					<a href="#"  onclick="window.history.back()" data-mini="true" data-role="button"> {'LBL_CANCEL'|@getTranslatedString:'Mobile'}</a>
			</div>
			<input type="hidden" name="pagenumber" value="{$smarty.request.start|@vtlib_purify}">
			<input type="hidden" name="module" id="module" value="{$_MODULE->name()}">
			<input type="hidden" name="mobilerecord" value="{$mobilerecordid}">
			<input type="hidden" name="record" value="{$id}">
			<input type="hidden" name="mode" value="{$mode}">
			<input type="hidden" name="mobilemode" value="1">
			<input type="hidden" name="_operation" value="saveRecord">
			<input type="hidden" name="action">
			<input type="hidden" name="parenttab" value="Support">
			<input type="hidden" name="return_module" value="{$_MODULE->name()}">
			<input type="hidden" name="return_id" value="{$id}">
			<input type="hidden" name="return_action" value="index">
			<input type="hidden" name="return_viewname" value="{$RETURN_VIEWNAME}">
			<input type="hidden" name="createmode" value="{$CREATEMODE}" />
			<input type="hidden" name="origmodule" id="origmodule" value="{$ORIGMODULE}" />
			{if $CURRENTMODUL eq 'Calendar' || $CURRENTMODUL eq 'Events'}
				<input type="hidden" name="duration_minutes" id="duration_minutes" value="0">
			{/if}
			{if $CURRENTMODUL eq 'Events'}
				<input type="hidden" name="inviteesid" value="{$INVITEES}">
			{/if}
			{if $CURRENTMODUL eq 'Calendar'}
				<input type="hidden" name="activitytype" value="Task">
			{/if}
			{foreach item=_BLOCK key=_BLOCKLABEL from=$_RECORD->blocks()}
			{assign var=_FIELDS value=$_BLOCK->fields()}
				<div data-role="collapsible" data-collapsed="false" data-mini="true"  >
					<h3>{$_BLOCKLABEL}</h3>
					<p>
					{foreach item=_FIELD from=$_FIELDS}
							<div>   
								{if $_FIELD->uitype() eq '2' || $_FIELD->uitype() eq '9' || $_FIELD->uitype() eq '55' || $_FIELD->uitype() eq '255' || $_FIELD->uitype() eq '11' || $_FIELD->uitype() eq '1' || $_FIELD->uitype() eq '13' || $_FIELD->uitype() eq '7' || $_FIELD->uitype() eq '71' || $_FIELD->uitype() eq '17' || $_FIELD->uitype() eq '72' || $_FIELD->uitype() eq '22'}
									{if $_MODULE->name() eq 'Calendar' && $_FIELD->name() eq 'location'}
									<!-- location not available for Task -->
									{else}
										<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
										<input  type="text" name="{$_FIELD->name()}" id="{$_FIELD->label()}" value="{$_FIELD->valueLabel()|escape:'html'}" {if $_FIELD->typeofdata() eq 'M'}class="required"{/if} />
									{/if}
			   				    {/if}
								{if $_FIELD->uitype() eq '23' || $_FIELD->uitype() eq '5' || $_FIELD->uitype() eq '6' || $_FIELD->uitype() eq '252'}
										{foreach key=date_value item=time_value from=$_FIELD->value()}
											{assign var=date_val value="$date_value"}
											{assign var=time_val value="$time_value"}
										{/foreach}
										{assign var=dateFormat value="$SMARTYDATEFORMAT"}
										{assign var=dateStr value="$HOURFORMATFORMAT"}
										
										{if $_FIELD->name() neq 'time_start' &&  $_FIELD->name() neq 'time_end'}
											{if $_FIELD->name() eq 'date_start'}
												<input type="hidden" name="dateformat" id="dateformat" value="{$DATEFORMAT}" />
												<label for="{$_FIELD->name()}">{'Start Date & Time'|@getTranslatedString:$_MODULE->name()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
											{elseif $_MODULE->name() eq 'Calendar' && $_FIELD->name() eq 'due_date'}
												<input type="hidden" name="dateformat" id="dateformat" value="{$DATEFORMAT}" />
												<label for="{$_FIELD->name()}">{'End Date & Time'|@getTranslatedString:$_MODULE->name()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
											{else}
												<label for="{$_FIELD->name()}">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
											{/if}
											<input data-mini="true" type="text" name="{$_FIELD->name()}" id="{$_FIELD->name()}" value="{$_FIELD->value()|date_format:$dateFormat}" />
												{literal}
													<script type="text/javascript">
														$(document).ready(function () {
															var curr = new Date().getFullYear();
															$('#{/literal}{$_FIELD->name()}{literal}').show();
															$('#{/literal}{$_FIELD->name()}{literal}').scroller({ preset: 'date', theme: 'default', display: 'modal', mode: 'mixed', showLabel:'false', height: '40', dateFormat: '{/literal}{$DATEFORMAT}{literal}',startYear: curr, lang:'{/literal}{$LANGFORMATFORMAT}{literal}'});
														});
													</script>
												{/literal}
											{if $_MODULE->name() eq 'Calendar' && $_FIELD->name() eq 'due_date'}
												<input type="text" name="time_end" id="time_end" value="{$time_value}" />
												{literal}
													<script type="text/javascript">
														$(document).ready(function () {
															$('#time_end').show();
															$('#time_end').scroller({ preset: 'time', theme: 'default', display: 'modal', mode: 'mixed', showLabel:'false', height: '40', timeFormat: 'HH:ii', timeWheels: '{/literal}{$TIMEWHEEL}{literal}', lang:'{/literal}{$LANGFORMATFORMAT}{literal}'});
													});
													</script>
												{/literal}
											{/if}
										{/if}
										{if $_FIELD->uitype() eq '252' && $_FIELD->name() eq 'time_start'}
											<input type="hidden" name="startformat" id="startformat" value="{$dateStr}" />
											<input type="text" name="time_start" id="time_start" value="{$time_value}" />
											{literal}
												<script type="text/javascript">
													$(document).ready(function () {
														$('#time_start').show();
														$('#time_start').scroller({ preset: 'time', theme: 'default', display: 'modal', mode: 'mixed', showLabel:'false', height: '40', timeFormat: 'HH:ii', timeWheels: '{/literal}{$TIMEWHEEL}{literal}', lang:'{/literal}{$LANGFORMATFORMAT}{literal}'});
												});
												</script>
											{/literal}
										{/if}
										{if $_FIELD->uitype() eq '252' && $_FIELD->name() eq 'time_end' && $_MODULE->name() neq 'Calendar'}
											<input type="text" name="time_end" id="time_end" value="{$time_value}" />
											{literal}
												<script type="text/javascript">
													$(document).ready(function () {
														$('#time_end').show();
														$('#time_end').scroller({ preset: 'time', theme: 'default', display: 'modal', mode: 'mixed', showLabel:'false', height: '40', timeFormat: 'HH:ii', timeWheels: '{/literal}{$TIMEWHEEL}{literal}', lang:'{/literal}{$LANGFORMATFORMAT}{literal}'});
												});
												</script>
											{/literal}
										{/if}
								{/if}			
								{if $_FIELD->uitype() eq '4'}
									<label for="{$_FIELD->name()}" >{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
									<input  type="text" class="ui-disabled" name="{$_FIELD->name()}" id="{$_FIELD->label()}" value="{$_FIELD->value()|escape:'html'}"  />
							    {/if}							
								{if $_FIELD->uitype() eq '15'}
									    <label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
										<select  id="{$_FIELD->name()}" name="{$_FIELD->name()}"   data-mini="true" class="select" data-native-menu="false">
											{foreach item=arr from=$_FIELD->value()}
												{if $arr.label eq $APP.LBL_NOT_ACCESSIBLE}
													<option value="{$arr.label}" {$arr.selected}>
														{$arr.label}
													</option>
												{else}
												<option value="{$arr.value}" {$arr.selected}>
													{$arr.label|@getTranslatedString:$_MODULE->name()}
							                    </option>
												{/if}
											{foreachelse}
												<option value=""></option>
												<option value="" style='color: #777777' disabled>{$APP.LBL_NONE}</option>
											{/foreach}
										</select>
								{/if} 							
								{if $_FIELD->uitype() eq '33'}
									    <label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
										<select  id="{$_FIELD->name()}" name="{$_FIELD->name()}"  onchange="multiSelectCheckNoneItem(this, '{$MOD.LBL_NONE}');" data-mini="true" class="select" data-native-menu="false">
											{foreach item=arr from=$_FIELD->value()}
												{if $arr.label eq $APP.LBL_NOT_ACCESSIBLE}
													<option value="{$arr.label}" {$arr.selected}>
														{$arr.label}
													</option>
												{else}
												<option value="{$arr.value}" {$arr.selected}>
													{$arr.label|@getTranslatedString:$_MODULE->name()}
							                    </option>
												{/if}
											{foreachelse}
												<option value=""></option>
												<option value="" style='color: #777777' disabled>{$APP.LBL_NONE}</option>
											{/foreach}
										</select>
								{/if}       
								{if $_FIELD->uitype() eq '53'}
									<div>
									<label for="assign_user">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
									{assign var=check value=1}
									{foreach key=key_one item=arr from=$_FIELD->value()}
										{foreach key=sel_value item=value from=$arr}
											{foreach key=sel_value1 item=value1 from=$value}
												{foreach key=sel_value2 item=value2 from=$value1}
														{if $key_one eq '0' && $value1 eq 'selected'}
															{assign var=check value=$check*0}
														{else}
																{assign var=check value=$check*1}
														{/if}
												{/foreach}
											{/foreach}
										{/foreach}
									{/foreach}
 									{if $check eq 0}
										{assign var=select_user value='checked'}
										{assign var=style_user value='display:block'}
										{assign var=style_group value='display:none'}
									{else}
										{assign var=select_group value='checked'}
										{assign var=style_user value='display:none'}
										{assign var=style_group value='display:block'}
									{/if}
									<div data-role="fieldcontain" >
									    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" >
										    <label for="User">{'LBL_USER'|@getTranslatedString:'Mobile'}</label>
											<input id="User"  type="radio"  name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)" >
											<label for="Group">{'LBL_GROUP'|@getTranslatedString:'Mobile'}</label>
											<input  id="Group" type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)" >
										</fieldset>   
									</div>
									<span id="assign_user" style="{$style_user}">
										<select name="assigned_user_id"  data-mini="true"   class="select" data-native-menu="false">
											{foreach key=key_one item=arr from=$_FIELD->value()}
												{if $key_one eq '0'}
													{foreach key=sel_value1 item=arr1 from=$arr}
														{foreach key=sel_value2 item=value from=$arr1}
															<option value="{$sel_value1}" {$value}>{$sel_value2}</option>
														{/foreach}
													{/foreach}
												{/if}
											{/foreach}
										</select>
									</span>
 									<span id="assign_team" style="{$style_group}">
										<select name="assigned_group_id"  data-mini="true"   id="assing" class="select" data-native-menu="false">
											{foreach key=key_one item=arr from=$_FIELD->value()}
												{if $key_one eq '1'}
													{foreach key=sel_value1 item=arr1 from=$arr}
														{foreach key=sel_value2 item=value from=$arr1}
															<option value="{$sel_value1}" {$value}>{$sel_value2}</option>
														{/foreach}
													{/foreach}
												{/if}
											{/foreach}
										</select>
									</span>
									</div>
									<!-- end uitype 53-->
			                    {/if}
								{if $_FIELD->uitype() eq '19'||  $_FIELD->uitype() eq '21'}
								    <div>
										<label for="textarea-a">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
										<textarea name="{$_FIELD->name()}"  id="textarea-a">{$_FIELD->value()}
										</textarea>
								    </div>
                                {/if}
 								{if $_FIELD->uitype() eq '56' && $_FIELD->name() eq 'sendnotification'}
								    <div>
										{if ($_MODULE->name() eq 'Calendar' && $_FIELD->name() eq 'sendnotification') || $_MODULE->name() neq 'Calendar'}
										<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label> 
										<input type="checkbox" name="{$_FIELD->name()}" id="{$_FIELD->label()}" class="custom" />
										{/if}
								    </div>
                                {/if}
								{if ($_FIELD->uitype() eq '10')||  ($_FIELD->uitype() eq '51')||  ($_FIELD->uitype() eq '59')||  ($_FIELD->uitype() eq '68')}
								    <div>
										<!-- reference field: remove leading 2 characters (e.g. 3x919 -> 919) to maintain relationship -->
										<label for="{$_FIELD->name()}" >{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
										{assign var=strippedid value=$_FIELD->value()|regex_replace:'#[,|x].*#':''}

										<!-- reference field Search for account_id -->
										<input type="hidden" name="{$_FIELD->name()}" id="{$_FIELD->name()}" value="{$_FIELD->value()}"  />
										<input type="hidden" name="{$strippedid}" id="strippedid" value="{$strippedid}"  />
										<ul id="{$_FIELD->name()}_autocomplete" data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="{$MOD.LBL_SEARCH_AUTOCOMPLETE}"></ul>
										<script type="text/javascript">
											
										$('#{$_FIELD->name()}_autocomplete').on( "filterablecreate", function ( e, data ) {ldelim}
											
											var value= "{$_FIELD->valueLabel()}";
											{literal}
											$(this).prev('form').find('input').val(value);		
										}).on( "filterablebeforefilter", function ( e, data ) {
										  var $ul = $( this ),
												$input = $( data.input ),
												value = $input.val(),
												html = "";
										  $ul.html( "" );
										  
										  if ( value && value.length > 2 ) {
												$ul.html( "<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>" );
												$ul.listview( "refresh" );
												$.ajax({
													 url: "?_operation=getAutocomplete",
													 dataType: "json",
													 data: {
														  "term": $input.val(),
														  "relmodule":"{/literal}{if $_FIELD->uitype() eq '68'}Accounts,Contacts{else}{$_FIELD->relatedmodule()}{/if}{literal}"
													 }
												})
												.done( function ( response ) {
													 $.each( response, function ( searchResultModuleIndex, searchResultModule ) {
														 html += '<li data-role="list-divider">' + searchResultModuleIndex + "</li>";
														 $.each( searchResultModule, function ( searchResultItemIndex, searchResultItem ) {
															  html += '<li><a href="#" onclick="$(this).closest(\'ul\').prev(\'form\').find(\'input\').val($(this).text());$(this).closest(\'ul\').html(\'\');$(\'#{/literal}{$_FIELD->name()}{literal}\').val(\''+searchResultItem[0]+'\');">' + searchResultItem[1] + "</a></li>";
														 });
													 });
													 if(html.length == 0){
														alert(mobiscroll_arr.ALERT_SELECT_AN_EXISITNG_ENTRY);
													 }
													 $ul.html( html );
													 $ul.listview( "refresh" );
													 $ul.trigger( "updatelayout");
												})
												.fail(function(){
													alert(mobiscroll_arr.ERROR_SEARCH_FAILED);
												});
										  }
										});

										{/literal}	
										</script>
										
								    </div>
 							    {/if}							
 								{if $_FIELD->uitype() eq '16'}
									{if $_FIELD->name() eq 'recurringtype' || $_FIELD->name() eq 'duration_minutes' || $_FIELD->name() eq 'visibility' }
										{if $_FIELD->name() eq 'recurringtype'}
											<input type="hidden" name="recurringtype" id="recurringtype" value="{$_FIELD->value()}">
										{elseif $_FIELD->name() eq 'duration_minutes'}
											<input type="hidden" name="duration_minutes" id="duration_minutes" value="{$_FIELD->value()}">
										{elseif $_FIELD->name() eq 'visibility'}
											<input type="hidden" name="visibility" id="visibility" value="{$_FIELD->value()}">
										{/if}
									{else}
										<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->typeofdata() eq 'M'}*{/if}:</label>
										<select  id="{$_FIELD->label()}" name="{$_FIELD->name()}"   data-mini="true" class="select" data-native-menu="false">
											{foreach key=key_one item=arr from=$_FIELD->value()}
												<option value="{$arr.label}" {$arr.selected}>{$arr.label}</option>
											{/foreach}
										</select>
									 {/if}
								{/if}
								{if $_FIELD->uitype() eq '63'}
								    <div>
										<input  type="hidden" name="{$_FIELD->name()}" id="{$_FIELD->name()}" value="{$_FIELD->value()}"  />
								    </div>
 							    							
							    {/if}							
                           </div>								
					{/foreach}
					</p>
				</div>
			{/foreach}
			<div data-role="controlgroup" data-type="horizontal" data-mini="true" >
			    <input data-inline="true" onclick="this.form.action.value='Save'; " type="submit" name="submit" value="{'LBL_SAVE'|@getTranslatedString:'Mobile'}" />
				<input type="button"  onclick="window.history.back()" value="{'LBL_CANCEL'|@getTranslatedString:'Mobile'}" />
			</div>
	    </form>
	</div><!-- /content -->
	{include file="modules/Mobile/generic/PanelMenu.tpl"}
</div><!-- /page -->
</body>
{include file="modules/Mobile/generic/Footer.tpl"}