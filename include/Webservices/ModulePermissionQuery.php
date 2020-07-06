<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is tsolucio.
 * Portions created by tsolucio are Copyright (C) tsolucio.
 * All Rights Reserved.
 *************************************************************************************/

function cbwsModulePermissionQuery($module, $user) {
	global $currentModule;
	$types = vtws_listtypes(null, $user);
	if (!in_array($module, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to access module is denied');
	}

	$userprivs = $user->getPrivileges();
	$query = $tableName = '';
	$join = ' ';
	$tabId = getTabid($module);
	if (!$userprivs->hasGlobalReadPermission() && !$userprivs->hasModuleReadSharing($tabId)) {
		$scope = '';
		$crmentity = CRMEntity::getInstance($module);
		$tableName = 'vt_tmp_u' . $user->id;
		$sharingRuleInfo = $userprivs->getModuleSharingRules($module, 'read');
		if (!empty($sharingRuleInfo) && (count($sharingRuleInfo['ROLE']) > 0 || count($sharingRuleInfo['GROUP']) > 0)) {
			$tableName = $tableName . '_t' . $tabId;
		} elseif (!empty($scope)) {
			$tableName .= '_t' . $tabId;
		}
		list($tsSpecialAccessQuery, $typeOfPermissionOverride, $unused1, $unused2, $SpecialPermissionMayHaveDuplicateRows) = cbEventHandler::do_filter(
			'corebos.permissions.accessquery',
			array(' ', 'none', $module, $user, true)
		);
		if ($typeOfPermissionOverride=='fullOverride') {
			$query = $crmentity->getNonAdminAccessQuery($module, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
			$join = $tsSpecialAccessQuery;
		} else {
			if ($typeOfPermissionOverride=='none' || trim($tsSpecialAccessQuery)=='') {
				$query = $crmentity->getNonAdminAccessQuery($module, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
				$join = " INNER JOIN $tableName $tableName$scope ON $tableName$scope.id = vtiger_crmentity$scope.smownerid ";
			} else {
				$tableName = "tsolucio_tmp_u{$user->id}";
				if ($currentModule == 'Reports') {
					$tsTableName = "tsolucio_tmp_u{$user->id}".str_replace('.', '', uniqid($user->id, true));
				}
				if ($typeOfPermissionOverride=='addToUserPermission') {
					$query = $crmentity->getNonAdminAccessQuery($module, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
					$join = "$query UNION ($tsSpecialAccessQuery) ";
				}
				if ($typeOfPermissionOverride=='addToUserPermission') {
					$join = " INNER JOIN {$tableName} on ({$tableName}.id=vtiger_crmentity.crmid or {$tableName}.id = vtiger_crmentity$scope.smownerid) ";
				} elseif ($typeOfPermissionOverride=='showTheseRecords') {
					$join = " INNER JOIN {$tableName} on {$tableName}.id=vtiger_crmentity.crmid ";
				} elseif ($typeOfPermissionOverride=='SubstractFromUserPermission') {
					$query = $crmentity->getNonAdminAccessQuery($module, $user, $userprivs->getParentRoleSequence(), $userprivs->getGroups());
					$join = " INNER JOIN $tableName $tableName$scope ON $tableName$scope.id = vtiger_crmentity$scope.smownerid ";
					$join .= " INNER JOIN {$tableName} on {$tableName}.id=vtiger_crmentity.crmid ";
				}
			}
		}
	}
	return array(
		'permissonTable' => $tableName,
		'permissionQuery' => $query,
		'permissionJoin' => $join,
	);
}
?>
