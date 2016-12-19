{strip}
<!DOCTYPE html>
<head>
	<title>{$_MODULE->label()} {$MOD.LBL_QUICKCREATE}</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta charset="utf-8">
	<link REL="SHORTCUT ICON" HREF="resources/images/favicon.ico">	
	<link rel="stylesheet" href="resources/css/jquery.mobile-1.4.5.min.css">	
	<script type="text/javascript" src="resources/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="resources/jquery.mobile-1.4.5.min.js"></script>
	<link rel="stylesheet" href="resources/css/jquery.mobile.structure-1.4.5.min.css" >
	<link rel="stylesheet" href="resources/css/jquery.mobile.icons.min.css" >
	<link rel="stylesheet" href="resources/css/theme.css" >
	<script type="text/javascript" src="resources/crmtogo.js"></script>
	<script type="text/javascript" src="resources/lang/{$LANGUAGE}.lang.js"></script>
	<style>
	</style>	
</head>
<body> 
<div data-role="page" data-theme="b" data-mini="true" id="edit_page">
	<!-- header -->
	<div data-role="header" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<div class="ui-btn-left" data-role="controlgroup" data-type="horizontal">
			<a href="#"  class="ui-btn ui-corner-all ui-icon-check ui-btn-icon-notext" >{$MOD.LBL_SAVE}</a>
			<a href="#"  onclick="window.history.back()" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext">{$MOD.LBL_CANCEL}</a>
		</div>
		<h2>{$MOD.LBL_QUICKCREATE}</h2>
		<a href="#panelmenu" data-mini='true' data-role='button' class="ui-btn ui-btn-right ui-btn-icon-notext ui-icon-grid ui-corner-all ui-icon-bars"></a>
	</div>
	<!-- /header -->
	<div data-role="collapsible-set" data-mini="true">	
		<form method="post" data-transition="pop" data-ajax="false" enctype="multipart/form-data" name="EditView" id="EditView">
			<input type="hidden" name="pagenumber" value="{$smarty.request.start|@vtlib_purify}">
			<input type="hidden" name="module" id="module" value="{$_MODULE->name()}">
			<input type="hidden" name="mobilerecord" value="{$mobilerecordid}">
			<input type="hidden" name="record" id="record" value="{$id}">
			<input type="hidden" name="mode" id="mode" value="{$mode}">
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
			{if $ORIGMODULE eq 'Events'}
				<input type="hidden" name="inviteesid" value="{$INVITEES}">
			{/if}
			{if $ORIGMODULE eq 'Calendar'}
				<input type="hidden" name="activitytype" value="Task">
			{/if}
			{foreach item=_BLOCK key=_BLOCKLABEL from=$_RECORD->blocks()}
			{assign var=_FIELDS value=$_BLOCK->fields()}
				<div data-mini="true">
					{foreach item=_FIELD from=$_FIELDS}
							{if $_FIELD->displaytype() eq '1' && ($_FIELD->quickcreate() || $_FIELD->typeofdata() eq 'M')}
								<div>
									{if $_FIELD->uitype() eq '1' || $_FIELD->uitype() eq '2' || $_FIELD->uitype() eq '55' || $_FIELD->uitype() eq '255' || $_FIELD->uitype() eq '11'  || $_FIELD->uitype() eq '13'  || $_FIELD->uitype() eq '17' || $_FIELD->uitype() eq '72' || $_FIELD->uitype() eq '22'  || $_FIELD->uitype() eq '20'}
										{if $_MODULE->name() eq 'Calendar' && $_FIELD->name() eq 'location'}
										<!-- location not available for Task -->
										{else}
											<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											<input  type="text" name="{$_FIELD->name()}" id="{$_FIELD->label()}" value="{$_FIELD->valueLabel()}" {if $_FIELD->ismandatory() eq 'M'}class="required"{/if} />
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
													<label for="{$_FIELD->name()}">{'Start Date & Time'|@getTranslatedString:$_MODULE->name()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
												{else}
													<label for="{$_FIELD->name()}">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
												{/if}
												<input data-mini="true" type="date" name="{$_FIELD->name()}" id="{$_FIELD->name()}" value="{$_FIELD->value()}" {if $_FIELD->ismandatory() eq 'M'}class="required"{/if} />
												<div id="format_note_{$_FIELD->name()}" style="margin-bottom:25px;font-style:italic;font-size:10px;display:none;">Format: YYYY-MM-DD</div>
											{/if}
											{if $_FIELD->uitype() eq '252' && $_FIELD->name() eq 'time_start'}
												<input type="hidden" name="startformat" id="startformat" value="{$dateStr}" />
												<input type="time" name="time_start" id="time_start" value="{$time_value}" class="required" />
												<div id="format_note_{$_FIELD->name()}" style="margin-bottom:25px;font-style:italic;font-size:10px;display:none;">Format: HH:MM (24 H)</div>
											{/if}
											{if $_FIELD->uitype() eq '252' && $_FIELD->name() eq 'time_end' && $ORIGMODULE eq 'Events'}
												{if $mode eq 'create'}
												<input type="hidden" name="time_end" id="time_end" value=""  />
												{else}
												<input type="time" name="time_end" id="time_end" value="{$time_value}" />
												<div id="format_note_time_end" style="margin-bottom:25px;font-style:italic;font-size:10px;display:none;">Format: HH:MM (24 H)</div>
												{/if}
											{/if}
									{/if}
									{if $_FIELD->uitype() eq '4'}
										<label for="{$_FIELD->name()}" >{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
										<input  type="text" class="ui-disabled" name="{$_FIELD->name()}" id="{$_FIELD->label()}" value="{$_FIELD->value()}"  />
									{/if}
									{if $_FIELD->uitype() eq '15'}
											<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											<select  id="{$_FIELD->name()}" name="{$_FIELD->name()}"   data-mini="true" class="select" data-native-menu="false">
												{foreach item=arr from=$_FIELD->value()}
													{if $arr.label eq $MOD.LBL_NOT_ACCESSIBLE}
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
													<option value="" style='color: #777777' disabled>{$MOD.LBL_NONE}</option>
												{/foreach}
											</select>
									{/if}
									{if $_FIELD->uitype() eq '33'}
											<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											<select  id="{$_FIELD->name()}" name="{$_FIELD->name()}" multiple data-mini="true" class="select" data-native-menu="false">
												<!-- provide content for an empty multi picklist as default -->
												<option value="_empty" selected="selected" style="display:none;"></option>
												{foreach item=arr from=$_FIELD->value()}
													{if $arr.label eq $MOD.LBL_NOT_ACCESSIBLE}
														<option value="{$arr.label}" {$arr.selected}>
															{$arr.label}
														</option>
													{else}
													<option value="{$arr.value}" {$arr.selected}>
														{$arr.label|@getTranslatedString:$_MODULE->name()}
													</option>
													{/if}
												{/foreach}
											</select>
									{/if}
									{if $_FIELD->uitype() eq '53'}
										<div>
										<label for="assign_user">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
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
												<label for="User">{$MOD.LBL_USER}</label>
												<input id="User"  type="radio"  name="assigntype" {$select_user} value="U" >
												<label for="Group">{$MOD.LBL_GROUP}</label>
												<input  id="Group" type="radio" name="assigntype" {$select_group} value="T" >
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
											<label for="textarea-a">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											<textarea name="{$_FIELD->name()}" rows="3" id="textarea-a" class="textarea">{$_FIELD->value()}
											</textarea>
										</div>
									{/if}
	 								{if $_FIELD->uitype() eq '56' && $_FIELD->name() eq 'sendnotification'}
										<div>
											{if ($_MODULE->name() eq 'Calendar' && $_FIELD->name() eq 'sendnotification') || $_MODULE->name() neq 'Calendar'}
											<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label> 
											<input type="checkbox" name="{$_FIELD->name()}" id="{$_FIELD->label()}" class="custom" />
											{/if}
										</div>
									{/if}
									{if ($_FIELD->uitype() eq '10')||  ($_FIELD->uitype() eq '51')||  ($_FIELD->uitype() eq '59')||  ($_FIELD->uitype() eq '68')}
										<div class="ui-field-contain">
											<div>
											<label for="{$_FIELD->name()}_selector">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											</div>
											<select name="{$_FIELD->name()}" id="{$_FIELD->name()}" data-native-menu="false" class="filterable-select">
												<option value="{$_FIELD->value()}" selected>{$_FIELD->valueLabel()}</option>
											</select>
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
											<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											<select  id="{$_FIELD->label()}" name="{$_FIELD->name()}" data-mini="true" class="select" data-native-menu="false">
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
									{if $_FIELD->uitype() eq '9'}
										<div>
											<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->name() eq 'probability'} %{/if}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											<input  type="text" name="{$_FIELD->name()}" id="{$_FIELD->label()}" value="{$_FIELD->valueLabel()}" {if $_FIELD->ismandatory() eq 'M'}class="required"{/if} />
										</div>
									{/if}
									{if $_FIELD->uitype() eq '69'}
										<div>
											<button id="chooseFile">{$_FIELD->label()}</button>
											<div style="display: none;">
												<input type="file" id="file-input" data-clear-btn="false" name="image" accept="image/*" capture>
											</div>
											<div id="preview">
												<img  id="contactimage" src="{$_FIELD->valueLabel()}" >
											</div>
										</div>
									{/if}
	 								{if $_FIELD->uitype() eq '71' || $_FIELD->uitype() eq '7'}
										<div>
											<label for="{$_FIELD->label()}">{$_FIELD->label()}{if $_FIELD->ismandatory() eq 'M'}*{/if}:</label>
											<input  type="text" name="{$_FIELD->name()}" id="{$_FIELD->label()}" value="{$_FIELD->valueLabel()}" {if $_FIELD->ismandatory() eq 'M'}class="required"{/if} />
										</div>
									{/if}
								</div>
							{/if}
					{/foreach}
					</p>
				</div>
			{/foreach}
	    </form>
	</div><!-- /content -->
	<div data-role="footer" data-theme="{$COLOR_HEADER_FOOTER}" data-position="fixed">
		<h1></h1>
	</div>
	{include file="modules/Mobile/PanelMenu.tpl"}
</div><!-- /page -->
</body>
{/strip}