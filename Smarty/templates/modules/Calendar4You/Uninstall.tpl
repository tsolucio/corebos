{*
/* * *******************************************************************************
* The content of this file is subject to the Calendar4You Free license.
* ("License"); You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
* Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
* All Rights Reserved.
* ****************************************************************************** */
*}
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
                                <td rowspan="2" valign="top" width="50"><img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" border="0" height="48" width="48"></td>
                                <td class="heading2" valign="bottom">
                                    <a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{'VTLIB_LBL_MODULE_MANAGER'|@getTranslatedString:'Settings'}</a> &gt;
                                    <a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule=Calendar4You&parenttab=Settings">{'Calendar4You'|@getTranslatedString:'Calendar4You'}</a> &gt;
                                    {$MOD.LBL_UNINSTALL}
                                </td>
                            </tr>
                            <tr>
                                <td class="small" valign="top">{$MOD.LBL_UNINSTALL_DESC}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br />

                    <form action="index.php" method="post" name="form" onsubmit="return isConfirmed();">
                        <input type="hidden" name="parenttab" value="Settings">
                        <input type="hidden" name="module" value="{$MODULE}"/>
                        <input type="hidden" name="action" id="action" value="UninstallModule"/>
                        {*<input type="hidden" name="action" id="action" value="{$MODULE}Ajax"/>
                        <input type="hidden" name="file" id="file" value="UninstallModule"/>*}
                        <div style="padding:10px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr class="small">
                                    <td><img src="{'prvPrfTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
                                    <td class="prvPrfTopBg" width="100%"></td>
                                    <td><img src="{'prvPrfTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
                                </tr>
                            </table>

                            <table class="prvPrfOutline" border="0" cellpadding="10" cellspacing="0" width="100%">
                                <tr><td>
                                        <table class="small" border="0" width="100%" cellpadding="2" cellspacing="0">
                                            <tr>
                                                <td valign="top" width="20px"><img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}"> </td>
                                                <td class="prvPrfBigText"><b> {$MOD.LBL_UNINSTALL_DESC}</b></td>
                                            </tr>
                                        </table>
                                        <br />

                                        <table width="100%" cellspacing="0" cellpadding="5" border="0" class="tableHeading">
                                            <tr><td class="big"><strong>{$MOD.LBL_UNINSTALL}</strong></td></tr>
                                        </table>

                                        <table cellpadding="5" cellspacing="0" width="100%">
                                            <tr>
                                                <td class="dvtCellLabel" align="center">
                                                    <input value="{$MOD.LBL_UNINSTALL}" name="uninstall_btn"  class="crmButton delete small" title="{$MOD.LBL_UNINSTALL_DESC}" type="submit" />
                                                </td>
                                            </tr>
                                        </table>
                                    </td></tr>
                            </table>
                        </div>
                    </form>

                </div>
            </td>
            <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
        </tr>
    </tbody>
</table>
<br />
<script type="text/javascript" language="javascript">
function isConfirmed()
{ldelim}
    return confirm('{$MOD.LBL_UNINSTALL_CONFIRM}');
{rdelim}
</script>