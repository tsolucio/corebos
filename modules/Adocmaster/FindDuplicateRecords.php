<?php
 /*************************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : Adocmaster
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('database/DatabaseConnection.php');

global $mod_strings, $app_strings, $app_list_strings;
global $current_language, $currentModule, $current_userid, $theme;

require_once('themes/'.$theme.'/layout_utils.php');

$req_module = vtlib_purify($_REQUEST['module']);
$focus = CRMEntity::getInstance($req_module);

$return_module=vtlib_purify($_REQUEST['module']);
$delete_idstring=vtlib_purify($_REQUEST['idlist']);	
$parenttab = getParenttab();

$smarty = new vtigerCRM_Smarty;

$ids_list = array();
$errormsg = '';
if(isset($_REQUEST['del_rec']))
{
	$url = getBasic_Advance_SearchURL();
	$delete_id_array=explode(",",$delete_idstring,-1);

	foreach ($delete_id_array as $id)
	{
		if(isPermitted($req_module,'Delete',$id) == 'yes') {
			$sql="update vtiger_crmentity set deleted=1 where crmid=?";
            $result = $adb->pquery($sql, array($id));
			DeleteEntity($req_module,$return_module,$focus,$id,"");
		}	
		else {
        	$ids_list[] = $id;
	}
}
	if(count($ids_list) > 0) {
		$ret = getEntityName($req_module,$ids_list);
		if(count($ret) > 0)
		{
	       		$errormsg = implode(',',$ret);
		}
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tbody><tr>
			<td rowspan='2' width='11%'><img src='themes/$theme/images/denied.gif' ></td>
			<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
				<span class='genHeaderSmall'>$app_strings[LBL_DUP_PERMISSION] $req_module $errormsg</span></td>
			</tr>
			<tr>
			<td class='small' align='right' nowrap='nowrap'>
			<a href='javascript:window.location.reload();'>$app_strings[LBL_GO_BACK]</a><br>
			</td>
			</tr>
			</tbody></table>
			</div>";
		echo "</td></tr></table>";
		exit;
	}
}

include("include/saveMergeCriteria.php");
$ret_arr=getDuplicateRecordsArr($req_module);

$fld_values=$ret_arr[0];
$total_num_group=count($fld_values);
$fld_name=$ret_arr[1];
$ui_type=$ret_arr[2];

$smarty->assign("NAVIGATION",$ret_arr["navigation"]);//Added for page navigation
$smarty->assign("MODULE",$req_module);
$smarty->assign("NUM_GROUP",$total_num_group);
$smarty->assign("FIELD_NAMES",$fld_name);
$smarty->assign("CATEGORY",$parenttab);
$smarty->assign("ALL_VALUES",$fld_values);
if(isPermitted($req_module,'Delete','') == 'yes')
	$button_del = $app_strings['LBL_MASS_DELETE'];
$smarty->assign("DELETE",$button_del);

$smarty->assign("MOD", return_module_language($current_language,$req_module));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MODE",'view');
if(isset($_REQUEST['button_view']))
{	
	$smarty->assign("VIEW",'true');
}	
if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("FindDuplicateAjax.tpl");
else
	$smarty->display('FindDuplicateDisplay.tpl');

?>