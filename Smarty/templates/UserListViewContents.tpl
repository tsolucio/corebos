{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<!-- BunnyJs Script Files -->
<link rel="stylesheet" href="include/bunnyjs/css/svg-icons.css">
<script src="include/bunnyjs/utils-dom.min.js"></script>
<script src="include/bunnyjs/ajax.min.js"></script>
<script src="include/bunnyjs/template.min.js"></script>
<script src="include/bunnyjs/pagination.min.js"></script>
<script src="include/bunnyjs/url.min.js"></script>
<script src="include/bunnyjs/utils-svg.min.js"></script>
<script src="include/bunnyjs/spinner.min.js"></script>
<script src="include/bunnyjs/datatable.min.js"></script>
<script src="include/bunnyjs/datatable.icons.min.js"></script>
<script src="include/bunnyjs/element.min.js"></script>
<script src="include/bunnyjs/datatable.scrolltop.min.js"></script>
<!-- BunnyJs Script Files -->
<script type="text/javascript" src="include/js/ListView.js"></script>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
<tr>
	<td class="big"><strong>{$MOD.LBL_USERS_LIST}</strong></td>
	<td class="small" align=right>&nbsp;</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
<tr>
	<th class="big" nowrap>{'LBL_TOTAL'|getTranslatedString:'Users'} {$TOTALUSERS}</th>
	<th class="big" nowrap>{'LBL_ADMIN'|getTranslatedString:'Users'} : {$TOTALADMIN}</th>
	<th class="big" nowrap>{'Active'|getTranslatedString:'Users'} : {$TOTALACTIVE}</th>
	<th class="big" nowrap>{'Inactive'|getTranslatedString:'Users'} : {$TOTALINACTIVE}</th>
	<td class="big" nowrap align="right">
		<div align="right">
		<input title="{$CMOD.LBL_NEW_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_NEW_USER_BUTTON_KEY}" type="submit" name="button" value="{$CMOD.LBL_NEW_USER_BUTTON_LABEL}" class="crmButton create small">
		<input title="{$CMOD.LBL_EXPORT_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_EXPORT_USER_BUTTON_KEY}" type="button" onclick="return selectedRecords('Users')" value="{$CMOD.LBL_EXPORT_USER_BUTTON_LABEL}" class="crmButton small cancel">
		</div>
	</td>
</{$APP.LBL_EXPORT}
{if !empty($ERROR_MSG)}
<tr>
	{$ERROR_MSG}
</tr>
{/if}
</tr>
</table>
<div id="view" class="workflows-list">
<datatable url="index.php?module=Users&action=UsersAjax&file=getJSON" template="userlist_row_template">
	<header>
			<div class="slds-grid slds-gutters" style="width: 650px;">
				<div class="slds-col">
					<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;">
						<label class="slds-form-element__label" for="lookup-339">{'LBL_STATUS'|getTranslatedString:'Users'}</label>
						<div class="slds-form-element__control slds-grid slds-box_border">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<select name="userstatus" id="userstatus" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
									aria-owns="userstatus" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
									<option value="all" selected="true">{$APP.LBL_ALLPICKLIST}</option>
									{$LIST_USER_STATUS}
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element" style="width: 162px; margin-bottom: 6px;">
						<label class="slds-form-element__label" for="text-input-id-1">
						{'LBL_USERNAME'|getTranslatedString:'Settings'}</label>
						<div class="slds-form-element__control">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<input type="text"  name="namerole_search" id="namerole_search" class="slds-input" style="height: 30px;"/>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element" style="width: 162px;">
						<label class="slds-form-element__label" for="text-input-id-1">
						{'LBL_EMAIL'|getTranslatedString:'Settings'}</label>
						<div class="slds-form-element__control">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<input type="text"  name="email_search" id="email_search" class="slds-input" style="height: 30px;"/>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element slds-lookup" data-select="single" style="width: 162px; margin-bottom: 6px;">
						<label class="slds-form-element__label" for="lookup-339">{'LBL_ADMIN'|getTranslatedString:'Settings'}</label>
						<div class="slds-form-element__control slds-grid slds-box_border">
							<div class="slds-input-has-icon slds-input-has-icon_right slds-grow">
								<svg aria-hidden="true" class="slds-input__icon">
									<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#search"></use>
								</svg>
								<select name="adminstatus" id="adminstatus" class="slds-lookup__search-input slds-input_bare" type="search" style="height: 30px;"
									aria-owns="adminstatus" role="combobox" aria-activedescendent="" aria-expanded="false" aria-autocomplete="list">
									<option value="all" selected="true">{$APP.LBL_ALLPICKLIST}</option>
									{$LIST_ADMIN_STATUS}
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="slds-col">
					<div class="slds-form-element slds-lookup slds-text-align_right" style="width: 162px; margin-bottom: 6px;">
						<br><br><span class="slds-text-title_bold">{'LBL_TOTAL_FILTERED'|getTranslatedString:'Users'}&nbsp;:&nbsp;<span id="current_rows"></span></span>
					</div>
				</div>
			</div>
		</header>
		<footer>
			<pagination limit=12 outer></pagination>
		</footer>
		<table class="rptTable">
			<tr>
			{foreach key=dtkey item=dtheader from=$LIST_HEADER}
				<th pid="{$dtkey}" class="rptCellLabel">{$dtheader}</th>
			{/foreach}
			</tr>
		</table>
	</datatable>
</div>
<table id="userlist_row_template" hidden>
	<tr>
		{foreach key=dtkey item=dtheader from=$LIST_FIELDS}
			{if $dtheader eq 'id'}
			<td class="rptData">
				<a av="href:edituser">
					<span>
						<img border="0" title="{'LBL_EDIT'|@getTranslatedString}" alt="{'LBL_EDIT'|@getTranslatedString}" style="cursor: pointer;" src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
					</span>
				</a>
				<a av="href:RecordDel" data-handler="deleteUser">
					<span av="id:id">
						<img border="0" title="{'LBL_DELETE'|@getTranslatedString}" alt="{'LBL_DELETE'|@getTranslatedString}" src="{'delete.gif'|@vtiger_imageurl:$THEME}" style="cursor: pointer;"/>
					</span>
				</a>
				<span class="slds-icon_container slds-icon-utility-wifi" av="id:userid" title="{'LOGGED IN'|@getTranslatedString}">
					<svg class="slds-icon slds-icon-text-default slds-icon_xx-small" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#wifi"></use>
					</svg>
					<span class="slds-assistive-text">{'LOGGED IN'|@getTranslatedString}</span>
				</span>
				<a av="href:Record">
					<span>
						<img src="{'logout.png'|@vtiger_imageurl:$THEME}" data-handler="logoutUser" border="0" alt="{$APP.LBL_LOGOUT}" title="{$APP.LBL_LOGOUT}" style="cursor:pointer;width:16px;"/>
					</span>
				</a>
				<a av="href:duplicateuser">
					<span>
						<img border="0" alt="{$APP.LBL_DUPLICATE_BUTTON}" title="{$APP.LBL_DUPLICATE_BUTTON}" src="{'settingsActBtnDuplicate.gif'|@vtiger_imageurl:$THEME}" style="cursor: pointer;"/>
					</span>
				</a>
			</td>
			{elseif $dtheader eq 'user_name'}
			<td class="rptData">
				<a av="href:viewusername"><span v="username"></span></a>
				<table>
					<tr>
						<td style="padding:0; margin:0;">
							<a av="href:viewuser"><span v="firstname"></span>&nbsp;<span v="lastname"></span></a>&nbsp;&#10088;&nbsp;<a av="href:viewrole"><span v="rolename"></span></a>&nbsp;&#10089;
						</td>
					</tr>
				</table>
			</td>
			{elseif $dtheader eq 'status'}
				<td v="statustag" class="rptData"></td>
			{elseif $dtheader eq 'email1'}
				<td class="rptData"><a av="href:sendmail"><span v="sendmail"></span></a></td>
			{else}
				<td v="{$dtkey}" class="rptData"></td>
			{/if}
		{/foreach}
	</tr>
</table>

<script>
{literal}
Template.define('userlist_row_template', {
	deleteUser:function(obj,data) {
		obj.addEventListener('click', function (event) {
			event.preventDefault();
			jQuery.ajax({
					method:'POST',
					url:'index.php?module=Users&action=UsersAjax&file=UserDeleteStep1&record='+data.id
				}).done(function(response) {
					document.getElementById('tempdiv').innerHTML= response;
					positionDivToCenter('tempdiv');
				}
			);
		});
	},
	logoutUser:function(obj, data) {
		obj.addEventListener('click', function (event) {
			event.preventDefault();
			jQuery.ajax({
					method:'POST',
					url:'index.php?module=Users&action=UsersAjax&file=LogoutUser&logoutuserid='+data.id
				}).done(function(response) {
					document.getElementById('status').style.display='none';
					alert(response);
				}
			);
		});
	}
});
DataTable.onRedraw(document.getElementsByTagName('datatable')[0], function (data) {
	for (index in data.data) {
		if (((data.data[index].Status == 'Active') && data.data[index].iscurrentuser && data.data[index].isblockeduser) || (data.data[index].Status == 'Inactive')) {
			document.getElementById(data.data[index].id).style.display = 'none';
		}
		if (!data.data[index].loggedin) {
			document.getElementById(data.data[index].userid).style.display = 'none';
		}
	}
	document.getElementById('current_rows').innerHTML = data.listtotalrecord;
});
{/literal}
Pagination._config.langFirst = "{$APP.LNK_LIST_START}";
Pagination._config.langLast = "{$APP.LNK_LIST_END}";
Pagination._config.langPrevious = "< {$APP.LNK_LIST_PREVIOUS}";
Pagination._config.langNext = "{$APP.LNK_LIST_NEXT} >";
{literal}
Pagination._config.langStats = "{from}-{to} {/literal}{$APP.LBL_LIST_OF}{literal} {total} ({/literal}{$APP.Page}{literal} {currentPage} {/literal}{$APP.LBL_LIST_OF}{literal} {lastPage})";
DataTableConfig.loadingImg = 'themes/images/loading.svg';
DataTableConfig.searchInputName = 'adminstatus';
DataTableConfig.searchInputName = 'namerole_search';
DataTableConfig.searchInputName = 'userstatus';
</script>
{/literal}
