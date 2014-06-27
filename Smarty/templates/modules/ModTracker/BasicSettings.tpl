    {*<!--
    /*********************************************************************************
      ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
       * ("License"); You may not use this file except in compliance with the License
       * The Original Code is:  vtiger CRM Open Source
       * The Initial Developer of the Original Code is vtiger.
       * Portions created by vtiger are Copyright (C) vtiger.
       * All Rights Reserved.
      *
     ********************************************************************************/
    -->*}
<script language="JavaScript" type="text/javascript" src="modules/ModTracker/ModTracker.js"></script>
    <br>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
    <tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

        <div align=center>
            {include file='SetMenu.tpl'}

            <table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
            <tr>
                <td rowspan="2" valign="top" width="50"><img src="{'set-IcoLoginHistory.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.ModTracker}"
                    title="{$MOD.ModTracker}" border="0" height="48" width="48"></td>
                <td class="heading2" valign="bottom"><b>
                    <a href="index.php?module=Settings&action=index&parenttab=Settings">{$APP.LBL_SETTINGS}</a> &gt;
                    {$MOD.ModTracker}</b>
                </td>
            </tr>

            <tr>
                <td class="small" valign="top">{$MOD.LBL_CONFIGURATION_DESCRIPTION}</td>
            </tr>
            </table>

            <table border=0 cellspacing=1 cellpadding=0 width="100%" class="lvtBg">
            <tr>
                <td>
                    <table border=0 cellspacing=0 cellpadding=2 width="100%" class="small">
                    <tr>
                        <td style="padding-right:20px" nowrap align="right"></td>
                    </tr>
                    </table>

                    <table border=0 cellspacing=0 cellpadding=0 width="95%" class="small">
                    <!-- Tab Links -->
                    <tr><td>
                        <table border=0 cellspacing=0 cellpadding=3 width="100%" class="small">
                        <tr>
                            <td class="dvtTabCache" style="width:10px" nowrap></td>
                            <td class="dvtSelectedCell" align="left" nowrap>{$MOD.LBL_BASIC_SETTINGS}</td>
                            <td class="dvtTabCache" width="100%">&nbsp;</td>
                        </tr>
                        </table>
                    </td></tr>
                    <tr><td>
                        <table border=0 cellspacing=0 cellpadding=10 width="100%" class="dvtContentSpace" style='border-bottom: 0'>
                        <tr>
                            <td>
                                <div id="modTrackerContents">
                                {include file="modules/ModTracker/BasicSettingsContents.tpl"}
                                </div>
                            </td>
                        </tr>
                        </table>
                    </td></tr>
                    <!-- Tab Links -->
                    <tr><td>
                        <table border=0 cellspacing=0 cellpadding=3 width="100%" class="small">
                        <tr>
                            <td class="dvtTabCacheBottom" style="width:10px" nowrap></td>
                            <td class="dvtSelectedCellBottom" align="left" nowrap>{$MOD.LBL_BASIC_SETTINGS}</td>
                            <td class="dvtTabCacheBottom" width="100%">&nbsp;</td>
                        </tr>
                        </table>
                    </td></tr>

                    </table>

                </td>
            </tr>
            </table>

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
    </table>
    <br>