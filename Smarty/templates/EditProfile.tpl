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
<script type="text/javascript" src="modules/Settings/profilePrivileges.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>
			{include file='SetMenu.tpl'}
                               <!-- DISPLAY -->
                               <form action="index.php" method="post" name="profileform" id="form">
                               <input type="hidden" name="module" value="Users">
                               <input type="hidden" name="parenttab" value="Settings">
                               <input type="hidden" name="action" value="{$ACTION}">
                               <input type="hidden" name="mode" value="{$MODE}">
                               <input type="hidden" name="profileid" value="{$PROFILEID}">
                               <input type="hidden" name="profile_name" value="{$PROFILE_NAME}">
                               <input type="hidden" name="profile_description" value="{$PROFILE_DESCRIPTION}">
                               <input type="hidden" name="parent_profile" value="{$PARENTPROFILEID}">
                               <input type="hidden" name="radio_button" value="{$RADIOBUTTON}">
                               <input type="hidden" name="return_action" value="{$RETURN_ACTION}">
                        
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
                                <td><!-- tabs -->
                                    <!-- Headers -->
                                    <table border="0" cellpadding="5" cellspacing="0" width="100%">
                                      <tbody><tr>
                                        <td><table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
                                            <tbody><tr>
                                              <td class="cblds-p_none"><!-- Module name heading -->
                                                  <table class="small" border="0" cellpadding="2" cellspacing="0">
                                                    <tbody><tr>
                                                      <td valign="top" class="cblds-p_none"><img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}"> </td>
                                                      <td class="prvPrfBigText cblds-p_none"><b> {if $MODE eq 'create'}{$CMOD.LBL_STEP_2_2} : {/if}{$CMOD.LBL_DEFINE_PRIV_FOR} &lt;{$PROFILE_NAME}&gt; </b><br>
                                                      <font class="small">{$CMOD.LBL_USE_OPTION_TO_SET_PRIV}</font> </td>
                                                      <td class="small cblds-p_none" style="padding-left: 10px;" align="right"></td>
                                                    </tr>
                                                </tbody></table></td>
                                              <td class="cblds-t-align_right" align="right" valign="bottom">&nbsp;
												{if $ACTION eq 'SaveProfile'}
                                                <input type="button" value=" {$CMOD.LBL_FINISH_BUTTON} " name="save" class="crmButton create small" title="{$CMOD.LBL_FINISH_BUTTON}" onclick="saveprofile('create')"/>&nbsp;&nbsp;
                                                {else}
                                                        <input type="button" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " name="save" class="crmButton small save" title="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="saveprofile('edit')" />&nbsp;&nbsp;
                                                {/if}
                                                <input type="button" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " name="Cancel" class="crmButton cancel small" title="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="window.history.back();" />
						</td>
                                            </tr>
                                          </tbody></table>
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
                                                          <td class="cblds-p_none" valign="top">{$GLOBAL_PRIV.0}</td>
                                                          <td class="cblds-p_none"><b>{$CMOD.LBL_VIEW_ALL}</b> </td>
                                                        </tr>
                                                        <tr>
                                                          <td class="cblds-p_none" valign="top"></td>
                                                          <td class="cblds-p_none" width="100%" >{$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_VIEW}</td>
                                                        </tr>
                                                        <tr>
                                                          <td>&nbsp;</td>
                                                        </tr>
							<tr>
							<td class="cblds-p_none" valign="top">{$GLOBAL_PRIV.1}</td>
							<td class="cblds-p_none"><b>{$CMOD.LBL_EDIT_ALL}</b> </td>
							</tr>
                                                        <tr>
                                                          <td class="cblds-p_none"valign="top"></td>
                                                          <td class="cblds-p_none"> {$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_EDIT}</td>
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
			          <td colspan="7" class="small colHeader cblds-p-v_mediumsmall"><div align="center"><strong>{$CMOD.LBL_EDIT_PERMISSIONS}</strong></div></td>
			        </tr>
			        <tr id="gva">
			          <td colspan="2" class="small colHeader cblds-p-v_mediumsmall"><strong> {$CMOD.LBL_TAB_MESG_OPTION} </strong><strong></strong></td>
			          <td class="small colHeader cblds-p-v_mediumsmall"><div align="center"><strong>{$CMOD.LBL_CREATE}</strong></div></td>
			          <td class="small colHeader cblds-p-v_mediumsmall"><div align="center"><strong>{$CMOD.Edit}</strong></div></td>
			          <td class="small colHeader cblds-p-v_mediumsmall"> <div align="center"><strong>{$CMOD.LBL_VIEW}</strong></div></td>
			          <td class="small colHeader cblds-p-v_mediumsmall"> <div align="center"><strong>{$CMOD.LBL_DELETE}</strong></div></td>
			          <td class="small colHeader cblds-p-v_mediumsmall" nowrap="nowrap">{$CMOD.LBL_FIELDS_AND_TOOLS_SETTINGS}</td>
			        </tr>

				<!-- module loops-->
			        {foreach key=tabid item=elements from=$TAB_PRIV}
			        <tr>
					{assign var=modulename value=$TAB_PRIV[$tabid][0]}
					{assign var="MODULELABEL" value=$modulename|@getTranslatedString:$modulename}
			          <td class="small cellLabel" width="3%"><div align="right">
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
				<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" id="img_{$tabid}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onclick="fnToggleVIew('{$tabid}_view')" border="0" height="16" width="40" style="display:block;">
				{/if}
				</div></td>
				  </tr>
		                  <tr class="hideTable" id="{$tabid}_view" className="hideTable">
				          <td colspan="7" class="small settingsSelectedUI">
						<table class="small" border="0" cellpadding="2" cellspacing="0" width="100%">
			        	    	<tbody>
						{if !empty($FIELD_PRIVILEGES[$tabid])}
						<tr>
							{if $modulename eq 'Calendar'}
								<td class="small colHeader cblds-p-v_mediumsmall" colspan="7" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN} ({$APP.Tasks})</td>
							{else}
								<td class="small colHeader cblds-p-v_mediumsmall" colspan="7" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN}</td>
							{/if}
					        </tr>
						{foreach item=row_values from=$FIELD_PRIVILEGES[$tabid]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top">{$element.2}{$element.1}{$element.3}</td>
					              <td>{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{/if}
						{if $modulename eq 'Calendar'}
						<tr>
							<td class="small colHeader" colspan="7" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN} ({$APP.Events})</td>
						</tr>
						{foreach item=row_values from=$FIELD_PRIVILEGES[16]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top">{$element.2}{$element.1}{$element.3}</td>
					              <td>{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{/if}
						{if !empty($UTILITIES_PRIV[$tabid])}
					        <tr>
					              <td colspan="7" class="small colHeader" valign="top">{$CMOD.LBL_TOOLS_TO_BE_SHOWN}</td>
						</tr>
						{foreach item=util_value from=$UTILITIES_PRIV[$tabid]}
							<tr>
							{foreach item=util_elements from=$util_value}
					              		<td valign="top">{$util_elements.1}</td>
						                <td>{$APP[$util_elements.0]}</td>
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
				<td align="left" class="cblds-p_none"><font color="red" size=5>*</font>{$CMOD.LBL_MANDATORY_MSG}</td>
			</tr>
			<tr>
				<td align="left" class="cblds-p_none"><font color="blue" size=5>*</font>{$CMOD.LBL_DISABLE_FIELD_MSG}</td>
			</tr>
		</table>
		<tr>
		<td style="border-top: 2px dotted rgb(204, 204, 204);" align="right" class="cblds-t-align_right">
		<!-- wizard buttons -->
		<table border="0" cellpadding="2" cellspacing="0" style="display: inline-block;">
		<tbody>
			<tr><td>
				{if $ACTION eq 'SaveProfile'}
					<input type="button" value=" {$CMOD.LBL_FINISH_BUTTON} " name="save" class="crmButton create small" title="{$CMOD.LBL_FINISH_BUTTON}" onclick="saveprofile('create')"/>&nbsp;&nbsp;
				{else}
					<input type="button" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " name="save" class="crmButton small save" title="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="saveprofile('edit')"/>&nbsp;&nbsp;
				{/if}
				</td><td>
					<input type="button" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " name="Cancel" class="crmButton cancel small"onClick="window.history.back();" title="{$APP.LBL_CANCEL_BUTTON_LABEL}" /></td>

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
