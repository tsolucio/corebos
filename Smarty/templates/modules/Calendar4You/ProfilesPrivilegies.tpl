{*<!--
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<br />
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>

	<div align=center>
    {include file='SetMenu.tpl'}
    <table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
    <tbody>
    	<tr>
    		<td rowspan="2" valign="top" width="50"><img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48"></td>
    		<td class="heading2" valign="bottom">
    		
    		<b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{'VTLIB_LBL_MODULE_MANAGER'|@getTranslatedString:'Settings'}</a> > 
    	<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule=Calendar4You&parenttab=Settings">{'Calendar4You'|@getTranslatedString:'Calendar4You'}</a> > 
    		{$MOD.LBL_PROFILES}			
    	</tr>
    
    	<tr>
    		<td class="small" valign="top">{$MOD.LBL_PROFILES_DESC}</td>
    	</tr>
    </tbody>
    </table>
    <br />
    
    <div style="padding:10px;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr class="small">
                <td><img src="{'prvPrfTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
                <td class="prvPrfTopBg" width="100%"></td>
                <td><img src="{'prvPrfTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
            </tr>
        </table>
        
        <form name="profiles_privilegies" action="index.php" method="post" >
        <input type="hidden" name="module" value="Calendar4You" />
        <input type="hidden" name="action" value="ProfilesPrivilegies" />
        <input type="hidden" name="mode" value="save" />
        <table class="prvPrfOutline" border="0" cellpadding="10" cellspacing="0" width="100%">
            <tr><td>
                <table class="small" border="0" width="100%" cellpadding="2" cellspacing="0">
                    <tr>
                        <td valign="top" width="20px"><img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}"> </td>
                        <td class="prvPrfBigText"><b> {$MOD.LBL_SETPRIVILEGIES} </b></td>
                        <td align="right">
                            <input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" />
                            &nbsp;
                            <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onClick="window.history.back();" />
                        </td>    
                    </tr>
                </table>
                <br />
                <table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="90%" style="margin:0 auto;">
                    <tr>
                        <td class="colHeader" width="40%">{$MOD.LBL_PROFILES}</td>
                        <td class="colHeader" width="20%" align="center">{$MOD.LBL_CREATE_EDIT}</td>
                        <td class="colHeader" width="20%" align="center">{$MOD.LBL_VIEW}</td>
                        <td class="colHeader" width="20%" align="center">{$MOD.LBL_DELETE}</td>
                    </tr>
                    
                    {foreach item=arr from=$PERMISSIONS}
                        {foreach key=profile_name item=profile_arr from=$arr}
                            <tr>
                                <td class="cellLabel">
                                    {$profile_name}                                    
                                </td>
                                <td class="cellText" align="center">
                                    <input type="checkbox" {$profile_arr.EDIT.checked} id="{$profile_arr.EDIT.name}" name="{$profile_arr.EDIT.name}" onclick="other_chk_clicked(this, '{$profile_arr.DETAIL.name}')"/>                                    
                                </td>
                                <td class="cellText" align="center">
                                    <input type="checkbox" {$profile_arr.DETAIL.checked} id="{$profile_arr.DETAIL.name}" name="{$profile_arr.DETAIL.name}" onclick="view_chk_clicked(this, '{$profile_arr.EDIT.name}', '{$profile_arr.DELETE.name}');"/>                                    
                                </td>
                                <td class="cellText" align="center">
                                    <input type="checkbox" {$profile_arr.DELETE.checked} id="{$profile_arr.DELETE.name}" name="{$profile_arr.DELETE.name}" onclick="other_chk_clicked(this, '{$profile_arr.DETAIL.name}')"/>
                                </td>
                            </tr>
                        {/foreach}    
                    {/foreach}
                </table>
            </td></tr>
        </table>
        </form>        
    </div>
    
    </div>
	</td>
    <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
    </tr>
</tbody>
</table>
<br>

{literal}
<script type="text/javascript">
function view_chk_clicked(source_chk, edit_chk_id, delete_chk_id){
    if(source_chk.checked == false){
        document.getElementById(edit_chk_id).checked = false;
        document.getElementById(delete_chk_id).checked = false;
    }
}

function other_chk_clicked(source_chk, detail_chk){   
    if(source_chk.checked == true){
        document.getElementById(detail_chk).checked = true;
    }
}
</script>
{/literal}    