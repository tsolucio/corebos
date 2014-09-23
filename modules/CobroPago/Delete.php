<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;
$focus = CRMEntity::getInstance($currentModule);
$focus->id = $_REQUEST['record'];
$focus->retrieve_entity_info($_REQUEST['record'],$currentModule);

if(!$focus->permissiontoedit())
{
        $log->debug("You don't have permission to deleted");
        echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
        echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

        <table border='0' cellpadding='5' cellspacing='0' width='98%'>
        <tbody><tr>
        <td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
        <td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span clas
        s='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
        </tr>
        <tr>
        <td class='small' align='right' nowrap='nowrap'>
        <a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
        </td>
        </tr>
        </tbody></table>
        </div>";
        echo "</td></tr></table>";
        exit;
}
$record = vtlib_purify($_REQUEST['record']);
$module = vtlib_purify($_REQUEST['module']);
$return_module = vtlib_purify($_REQUEST['return_module']);
$return_action = vtlib_purify($_REQUEST['return_action']);
$return_id = vtlib_purify($_REQUEST['return_id']);
$parenttab = getParentTab();

//Added to fix 4600
$url = getBasic_Advance_SearchURL();

DeleteEntity($currentModule, $return_module, $focus, $record, $return_id);

header("Location: index.php?module=$return_module&action=$return_action&record=$return_id&parenttab=$parenttab&relmodule=$module".$url);

?>