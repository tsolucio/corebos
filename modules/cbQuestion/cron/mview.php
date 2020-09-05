<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
require_once 'vtlib/Vtiger/Module.php';
include_once 'modules/cbQuestion/cbQuestion.php';
require_once 'data/CRMEntity.php';

global $adb, $current_user;
$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbQuestion');
$current_user = Users::getActiveAdminUser();

$qs = $adb->pquery(
	'select cbquestionid, qname
		from vtiger_cbquestion
		inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cbquestionid
		where vtiger_crmentity.deleted=0 and mviewcron=?',
	array('1')
);
while ($cbq = $adb->fetch_array($qs)) {
	$vname = str_replace(' ', '_', $cbq['qname']);
	$adb->query('DROP TABLE '.$vname);
	$sql = cbQuestion::getSQL($cbq['cbquestionid']);
	$adb->query('CREATE TABLE '.$vname.' AS '.$sql);
}
?>