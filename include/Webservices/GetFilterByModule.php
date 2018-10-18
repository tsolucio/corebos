<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of SIGPAC.
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
*************************************************************************************************/

function getfiltersbymodule($module, $user) {
	global $log;
	$log->debug('Entering function vtws_getfiltersbymodule');

	$focus = CRMEntity::getInstance($module);
	$linkfields=array($focus->list_link_field);
	if ($module=='Contacts' || $module=='Leads') {
		$linkfields=array('firstname', 'lastname');
	}

	$customView = new CustomView($module);
	$viewid = $customView->getViewId($module);
	list($customviews, $customview_html) = cbws_getCustomViewCombo($viewid, $module, $customView);

	return array(
		'html'=>$customview_html,
		'filters'=>$customviews,
		'linkfields'=>$linkfields,
	);
}

/** to get the customviewCombo for the class variable customviewmodule
 * @param $viewid :: Type Integer
 * $viewid will make the corresponding selected
 * @returns  $customviewCombo :: Type String
 */
function cbws_getCustomViewCombo($viewid, $module, $customView, $markselected = true) {
	global $adb, $current_user, $app_strings;
	$tabid = getTabid($module);
	$_REQUEST['action'] = '';
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';

	$shtml_user = '';
	$shtml_pending = '';
	$shtml_public = '';
	$shtml_others = '';
	$filters = array();

	$selected = 'selected';
	if ($markselected == false) {
		$selected = '';
	}

	$ssql = 'select vtiger_customview.*, vtiger_users.first_name,vtiger_users.last_name
		from vtiger_customview
		inner join vtiger_tab on vtiger_tab.name = vtiger_customview.entitytype
		left join vtiger_users on vtiger_customview.userid = vtiger_users.id ';
	$ssql .= ' where vtiger_tab.tabid=?';
	$sparams = array($tabid);

	if ($is_admin == false) {
		$ssql .= " and (vtiger_customview.status=0 or vtiger_customview.userid = ? or vtiger_customview.status = 3 or vtiger_customview.userid in (
			select vtiger_user2role.userid
			from vtiger_user2role
			inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
			inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
			where vtiger_role.parentrole like '" . $current_user_parent_role_seq . "::%'))";
		$sparams[] = $current_user->id;
	}
	$ssql .= ' ORDER BY viewname';
	$result = $adb->pquery($ssql, $sparams);
	while ($cvrow = $adb->fetch_array($result)) {
		if ($cvrow['viewname'] == 'All') {
			$cvrow['viewname'] = $app_strings['COMBO_ALL'];
		}

		$option = '';
		$filter = array(
			'name' => $cvrow['viewname'],
			'status' => $cvrow['status'],
		);
		$viewname = $cvrow['viewname'];
		if ($cvrow['status'] == CV_STATUS_DEFAULT || $cvrow['userid'] == $current_user->id) {
			$disp_viewname = $viewname;
		} else {
			$userName = getFullNameFromArray('Users', $cvrow);
			$disp_viewname = $viewname . ' [' . $userName . '] ';
		}

		//$advft_criteria = json_encode($customView->getAdvFilterByCvid($cvrow['cvid']));
		$advft_criteria = $customView->getAdvFilterByCvid($cvrow['cvid']);
		$advft = array();
		$groupnum = 1;
		foreach ($advft_criteria as $groupinfo) {
			if ($groupnum==1) {
				$groupcolumns = $groupinfo['columns'];
				foreach ($groupcolumns as $columnindex => $columninfo) {
					$columnname = $columninfo['columnname'];
					$comparator = $columninfo['comparator'];
					$value = $columninfo['value'];
					$columncondition = $columninfo['column_condition'];

					$columns = explode(":", $columnname);
					$name = $columns[1];

					$advft[$columnindex]['columname'] = $name;
					$advft[$columnindex]['comparator'] = $comparator;
					if ($value == 'yes') {
						$advft[$columnindex]['value'] = 1;
					} elseif ($value == 'no') {
						$advft[$columnindex]['value'] = 0;
					} else {
						$advft[$columnindex]['value'] = $value;
					}
					$advft[$columnindex]['column_condition'] = $columncondition;
				}
				$groupnum++;
			}
		}
		$advft_criteria = json_encode($advft);
		$filter['advcriteria'] = $advft_criteria;
		$filter['stdcriteria'] = $customView->getCVStdFilterSQL($cvrow['cvid']);
		$option = "<option value='".$cvrow['cvid']."'>" . $disp_viewname . '</option>';
		// Add the option to combo box at appropriate section
		if ($option != '') {
			if ($cvrow['status'] == CV_STATUS_DEFAULT || $cvrow['userid'] == $current_user->id) {
				$shtml_user .= $option;
			} elseif ($cvrow['status'] == CV_STATUS_PUBLIC) {
				$shtml_public .= $option;
			} elseif ($cvrow['status'] == CV_STATUS_PENDING) {
				$shtml_pending .= $option;
			} else {
				$shtml_others .= $option;
			}
		}
		$filters[$cvrow['cvid']] = $filter;
	}
	$shtml = $shtml_user;

	$shtml = $shtml . $shtml_public . $shtml_others;
	return array($filters, $shtml);
}
?>
