{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
{literal}
<style>
.showTable{
	display:inline-table;
}
.hideTable{
	display:none;
}
</style>
{/literal}
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script type="text/javascript">
{literal}
function UpdateProfile() {
	var prof_name = document.getElementById('profile_name').value;
	var prof_desc = document.getElementById('description').value;
	if(prof_name == '') {
		document.getElementById('profile_name').focus();
{/literal}
		alert("{$APP.PROFILENAME_CANNOT_BE_EMPTY}");
{literal}
	}
	else
	{
{/literal}
		var urlstring = "module=Users&action=UsersAjax&file=RenameProfile&profileid="+{$PROFILEID}+"&profilename="+encodeURIComponent(prof_name)+"&description="+encodeURIComponent(prof_desc);
{literal}
	jQuery.ajax({
			method: 'POST',
			url: 'index.php?'+urlstring,
		}).done(function (response) {
				document.getElementById('renameProfile').style.display="none";
				window.location.reload();
			{/literal}
					alert("{$APP.PROFILE_DETAILS_UPDATED}");
			{literal}
			}
		);
	}
}
</script>
{/literal}

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
			{include file='SetMenu.tpl'}
				<form  method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
			        <input type="hidden" name="module" value="Settings">
			        <input type="hidden" name="action" value="profilePrivileges">
			        <input type="hidden" name="parenttab" value="Settings">
			        <input type="hidden" name="return_action" value="profilePrivileges">
			        <input type="hidden" name="mode" value="edit">
			        <input type="hidden" name="profileid" value="{$PROFILEID}">
				<!-- DISPLAY -->
				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tbody><tr>
					<td rowspan="2" valign="top" width="50" class="cblds-p_none"><img src="{'ico-profile.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROFILES}" title="{$MOD.LBL_PROFILES}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > <a href="index.php?module=Settings&action=ListProfiles&parenttab=Settings">{$CMOD.LBL_PROFILE_PRIVILEGES}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$PROFILE_NAME}&quot;</b></td>
				</tr>
				<tr>
					<td class="small cblds-p-v_none" valign="top">{$CMOD.LBL_PROFILE_MESG} &quot;{$PROFILE_NAME}&quot; </td>
				</tr>
				</tbody></table>
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tbody><tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody><tr>
                        <td class="cblds-p_none"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr class="small">
                              <td class="cblds-p_none cblds-p-v_medium"><img src="{'prvPrfTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
                              <td class="prvPrfTopBg cblds-p_none" width="100%"></td>
                              <td class="cblds-p_none"><img src="{'prvPrfTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
                            </tr>
                          </tbody></table>
                            <table class="prvPrfOutline" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody><tr>
                                <td class="cblds-p_none"><!-- tabs -->
                                    <!-- Headers -->
                                    <table border="0" cellpadding="5" cellspacing="0" width="100%">
                                      <tbody><tr>
                                        <td><table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
                                            <tbody><tr>
                                              <td><!-- Module name heading -->
                                                  <table class="small" border="0" cellpadding="2" cellspacing="0">
                                                    <tbody><tr>
                                                      <td valign="top"><img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}"> </td>
                                                      <td class="prvPrfBigText"><b> {$CMOD.LBL_DEFINE_PRIV_FOR} &lt;{$PROFILE_NAME}&gt; </b><br>
                                                      <font class="small">{$CMOD.LBL_USE_OPTION_TO_SET_PRIV}</font> </td>
                                                      <td class="small" style="padding-left: 10px;" align="right"></td>

                                                    </tr>
                                                </tbody></table></td>
					      <td align="right" valign="bottom" class="cblds-t-align_right">&nbsp;<input type="button" value="{$APP.LBL_RENAMEPROFILE_BUTTON_LABEL}" title="{$APP.LBL_RENAMEPROFILE_BUTTON_LABEL}" class="crmButton small edit" name="rename_profile"  onClick = "show('renameProfile');">&nbsp;<input type="submit" value="{$APP.LBL_EDIT_BUTTON_LABEL}" title="{$APP.LBL_EDIT_BUTTON_LABEL}" class="crmButton small edit" name="edit" >
                              		     </td>
                                            </tr></tbody></table>
					    <!-- RenameProfile Div start -->
					    <div class="layerPopup"  style="left:350px;width:500px;top:300px;display:none;" id="renameProfile">
						<table class="layerHeadingULine" border="0" cellpadding="3" cellspacing="0" width="100%">
						<tr style="cursor:move;">
						<td class="layerPopupHeading" id = "renameUI" align="left" width="60%">{$APP.LBL_RENAME_PROFILE}</td>
						<td align="right" width="40%" class="cblds-t-align_right"><a href="javascript:fnhide('renameProfile');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0"></a></td>
						</tr>
						</table>
					    <table align="center" border="0" cellpadding="5" cellspacing="0" width="95%">

						<tr>
						<td class="small">
							<table cellspacing="0" align="center" bgcolor="white" border="0" cellpadding="5" width="100%">
								<tr>
								<td align="right" class="cblds-t-align_right" width="25%" style="padding-right:10px;" nowrap><b>{$APP.LBL_PROFILE_NAME} :</b></td>
								<td align="left" width="75%" style="padding-right:10px;"><input id = "profile_name" name="profile_name" class="txtBox" value="{$PROFILE_NAME}" type="text"></td>
								</tr>
								<tr>
								<td align="right" class="cblds-t-align_right" width="25%" style="padding-right:10px;" nowrap><b>{$APP.LBL_DESCRIPTION} :</b></td>
								<td align="left" width="75%" style="padding-right:10px;"><textarea name="description" id = "description" class="txtBox">{$PROFILE_DESCRIPTION} </textarea></td>
								</tr>
							</table>
						</td>
						</tr>
					    </table>
					    <table class="layerPopupTransport" border="0" cellpadding="5" cellspacing="0" width="100%">
					    <tr>
						<td align = "center" class="cblds-t-align_center">
							<input name="save" value="{$APP.LBL_UPDATE}" class="crmbutton small save" onclick="UpdateProfile();" type="button" title="{$APP.LBL_UPDATE}">&nbsp;&nbsp;
							<input name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small save" onclick="fnhide('renameProfile');" type="button" title="{$APP.LBL_CANCEL_BUTTON_LABEL}">&nbsp;&nbsp;
						</td>
					    </tr>
					    </table>
					    </div>
				             <!-- RenameProfile Div end -->

                                            <!-- privilege lists -->
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                              <tbody><tr>
                                                <td style="height: 10px;" align="center"></td>
                                              </tr>
                                            </tbody></table>
                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                              <tbody><tr>
                                                <td>
						<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<tbody>
							<tr>
								<td class="cellLabel big"> {$CMOD.LBL_SUPER_USER_PRIV} </td>
							</tr>
						</tbody>
						</table>
						<table class="small" align="center" border="0" cellpadding="5" cellspacing="0" width="90%">
                                                <tbody><tr>
                                                    <td class="prvPrfTexture" style="width: 20px;">&nbsp;</td>
                                                    <td valign="top" width="97%"><table class="small" border="0" cellpadding="2" cellspacing="0" width="100%">
                                                      <tbody>
				                         <tr id="gva">
                                                          <td valign="top" class="cblds-p_none">{$GLOBAL_PRIV.0}</td>
                                                          <td class="cblds-p_none"><b>{$CMOD.LBL_VIEW_ALL}</b> </td>
                                                        </tr>
                                                        <tr >
                                                          <td valign="top" class="cblds-p_none"></td>
                                                          <td width="100%" class="cblds-p_none">{$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_VIEW}</td>
                                                        </tr>
                                                        <tr>
                                                          <td>&nbsp;</td>
                                                        </tr>
							<tr>
							<td valign="top" class="cblds-p_none">{$GLOBAL_PRIV.1}</td>
							<td class="cblds-p_none"><b>{$CMOD.LBL_EDIT_ALL}</b> </td>
							</tr>
                                                        <tr>
                                                          <td valign="top"></td>
                                                          <td > {$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_EDIT}</td>
                                                        </tr>

                                                      </tbody></table>
						</td>
                                                  </tr>
                                                </tbody></table>
<br>

			<table border="0" cellpadding="5" cellspacing="0" width="100%">
			  <tbody><tr>
			    <td class="cellLabel big"> {$CMOD.LBL_SET_PRIV_FOR_EACH_MODULE} </td>
			  </tr>
			</tbody></table>
			<table class="small" align="center" border="0" cellpadding="5" cellspacing="0" width="90%">
			  <tbody><tr>
			    <td class="prvPrfTexture" style="width: 20px;">&nbsp;</td>
			    <td valign="top" width="97%">
				<table class="small listTable" border="0" cellpadding="5" cellspacing="0" width="100%">
			        <tbody>
				<tr id="gva">
			          <td colspan="7" class="small colHeader cblds-p-v_medium"><div align="center"><strong> {$CMOD.LBL_EDIT_PERMISSIONS} </strong></div></td>
			        </tr>
			        <tr id="gva">
			          <td colspan="2" class="small colHeader cblds-p-v_medium"><strong> {$CMOD.LBL_TAB_MESG_OPTION} </strong><strong></strong></td>
			          <td class="small colHeader cblds-p-v_medium"><div align="center"><strong>{$CMOD.LBL_CREATE}</strong></div></td>
			          <td class="small colHeader cblds-p-v_medium"><div align="center"><strong>{$CMOD.Edit}</strong></div></td>
			          <td class="small colHeader cblds-p-v_medium"> <div align="center"><strong>{$CMOD.LBL_VIEW} </strong></div></td>
			          <td class="small colHeader cblds-p-v_medium"> <div align="center"><strong>{$CMOD.LBL_DELETE}</strong></div></td>
			          <td class="small colHeader cblds-p-v_medium" nowrap="nowrap"> {$CMOD.LBL_FIELDS_AND_TOOLS_SETTINGS} </td>
			        </tr>

				<!-- module loops-->
			        {foreach key=tabid item=elements from=$TAB_PRIV}
			        <tr>
					{assign var=modulename value=$TAB_PRIV[$tabid][0]}
					{assign var="MODULELABEL" value=$modulename|@getTranslatedString:$modulename}
			          <td class="smal cellLabel" width="3%"><div align="right">
					{$TAB_PRIV[$tabid][1]}
			          </div></td>
			          <td class="small cellLabel" width="40%"><p>{$MODULELABEL}</p></td>
			          <td class="small cellText" width="10%">&nbsp;<div align="center">
					{if !empty($STANDARD_PRIV[$tabid][4])}{$STANDARD_PRIV[$tabid][4]}{/if}
			          </div></td>
			          <td class="small cellText" width="10%">&nbsp;<div align="center">
					{if !empty($STANDARD_PRIV[$tabid][1])}{$STANDARD_PRIV[$tabid][1]}{/if}
			          </div></td>
			          <td class="small cellText" width="10%">&nbsp;<div align="center">
					{if !empty($STANDARD_PRIV[$tabid][3])}{$STANDARD_PRIV[$tabid][3]}{/if}
			          </div></td>
			          <td class="small cellText" width="10%">&nbsp;<div align="center">
					{if !empty($STANDARD_PRIV[$tabid][2])}{$STANDARD_PRIV[$tabid][2]}{/if}
        			  </div></td>
			          <td class="small cellText" width="17%">&nbsp;<div align="center">
				{if !empty($FIELD_PRIVILEGES[$tabid])}
				<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onclick="fnToggleVIew('{$modulename}_view')" border="0" height="16" width="40">
				{/if}
				</div></td>
				  </tr>
		                  <tr class="hideTable" id="{$modulename}_view" className="hideTable">
				          <td colspan="7" class="small settingsSelectedUI">
						<table class="small cblds-p_large" border="0" cellpadding="2" cellspacing="0" width="100%" style="border-collapse: separate;">
			        	    	<tbody>
						{if !empty($FIELD_PRIVILEGES[$tabid])}
						<tr>
							{if $modulename eq 'Calendar'}
								<td class="small colHeader cblds-p-v_medium" colspan="7" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN} ({$APP.Tasks})</td>
							{else}
								<td class="small colHeader cblds-p-v_medium" colspan="7" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN}</td>
							{/if}
						</tr>
						{foreach item=row_values from=$FIELD_PRIVILEGES[$tabid]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top" class="cblds-p_none cblds-p-v_mediumsmall">
									  <span>{$element.1}</span>
									  <span>{$element.2}</span>
								  </td>
					              <td class="cblds-p_none cblds-p-v_mediumsmall">{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{/if}
						{if $modulename eq 'Calendar'}
						<tr>
							<td class="small colHeader cblds-p-v_medium" colspan="7" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN}  ({$APP.Events})</td>
						</tr>
						{foreach item=row_values from=$FIELD_PRIVILEGES[16]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top" class="cblds-p_none cblds-p-v_mediumsmall">
									  <span>{$element.1}</span>
									  <span>{$element.2}</span>
								  </td>
					              <td class="cblds-p_none cblds-p-v_mediumsmall">{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{/if}
						{if !empty($UTILITIES_PRIV[$tabid])}
						<tr>
							<td colspan="7" class="small colHeader cblds-p-v_medium" valign="top">{$CMOD.LBL_TOOLS_TO_BE_SHOWN} </td>
						</tr>
						{foreach item=util_value from=$UTILITIES_PRIV[$tabid]}
						<tr>
							{foreach item=util_elements from=$util_value}
					              		<td valign="top" class="cblds-p_none cblds-p-v_mediumsmall">{$util_elements.1}</td>
						                <td class="cblds-p_none cblds-p-v_mediumsmall">{$APP[$util_elements.0]}</td>
							{/foreach}
				               	</tr>
						{/foreach}
						{/if}
					        </tbody>
						</table>
					</td>
			          </tr>
				  {/foreach}
			    	  </tbody>
				  </table>
			  </td>
			  </tr>
			</tbody>
			</table>
		</td>
		</tr>
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<td align="left" class="cblds-p-v_none"><font color="red" size=5>*</font>&nbsp;{$CMOD.LBL_MANDATORY_MSG}</td>
			</tr>
			<tr>
				<td align="left" class="cblds-p-v_none"><font color="blue" size=5>*</font>&nbsp;{$CMOD.LBL_DISABLE_FIELD_MSG}</td>
			</tr>
			<tr>
				<td align="left"><img src="{'locked.png'|@vtiger_imageurl:$THEME}" />&nbsp;{$CMOD.LBL_READ_ONLY_ACCESS_MSG}</td>
			</tr>
			<tr>
				<td align="left"><img src="{'unlocked.png'|@vtiger_imageurl:$THEME}" />&nbsp;{$CMOD.LBL_READ_WRITE_ACCESS_MSG}</td>
			</tr>
		</table>
		<tr>
		<td style="border-top: 2px dotted rgb(204, 204, 204);" align="right" class="cblds-t-align_right">
		<!-- wizard buttons -->
		<table border="0" cellpadding="2" cellspacing="0" style="display: inline-block;">
		<tbody>
			<tr>
				<td><input type="submit" value="{$APP.LBL_EDIT_BUTTON_LABEL}" title="{$APP.LBL_EDIT_BUTTON_LABEL}" class="crmButton small edit" name="edit"></td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
		</table>
		</td>
		</tr>
	</tbody>
	</table>
	</td>
        </tr>
        </tbody>
	</table>
      </td>
      </tr>
      </tbody></table>
      <table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
           <tbody><tr>
                <td><img src="{'prvPrfBottomLeft.gif'|@vtiger_imageurl:$THEME}"></td>
                <td class="prvPrfBottomBg" width="100%"></td>
                <td><img src="{'prvPrfBottomRight.gif'|@vtiger_imageurl:$THEME}"></td>
                </tr>
            </tbody>
      </table></td>
      </tr>
      </tbody></table>
	<p>&nbsp;</p>
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tbody><tr><td class="small cblds-t-align_right" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
	</tbody></table>
	</td>
	</tr>
	</tbody></table>
	</form>
	<!-- End of Display -->
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</div>

	</td>
	<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
	</tr>
</tbody>
</table>
<script type="text/javascript">
{literal}
function fnToggleVIew(obj){
	obj = "#"+obj;
	if(jQuery(obj).hasClass('hideTable')) {
		jQuery(obj).removeClass('hideTable');
	} else {
		jQuery(obj).addClass('hideTable');
	}
}
{/literal}
{literal}
	//for move RenameProfile
	jQuery("#renameProfile").draggable({ handle: "#renameUI" });
{/literal}
</script>

