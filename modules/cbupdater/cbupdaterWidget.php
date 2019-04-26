<?php
/*************************************************************************************************
* Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*************************************************************************************************
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

global $currentModule;

$focus = CRMEntity::getInstance($currentModule);
$record = vtlib_purify($_REQUEST['record']);

if ($record) {
	$focus->id = $record;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($record, $currentModule);
	if ($focus->column_fields['execstate']=='Pending' || $focus->column_fields['execstate']=='Continuous') {
		if (isset($focus->column_fields['blocked']) && $focus->column_fields['blocked']!='1') {
			echo '<a href="index.php?module=cbupdater&action=dowork&idstring='.$record.'">'.getTranslatedString("Apply", $currentModule).'</a>';
		}
		if ($focus->column_fields['systemupdate']=='1') {
			echo '<br><strong>'.getTranslatedString('systemupdate', $currentModule).'</strong>';
		}
	} elseif ($focus->column_fields['systemupdate']=='1') {
		echo '<strong>'.getTranslatedString('systemupdate', $currentModule).'</strong>';
	} else {
		if (isset($focus->column_fields['blocked']) && $focus->column_fields['blocked']!='1') {
			echo '<a href="index.php?module=cbupdater&action=dowork&doundo=1&idstring='.$record.'">'.getTranslatedString("Undo", $currentModule).'</a>';
		}
	}
	if (isset($focus->column_fields['blocked'])) {
		echo '<br><a href="index.php?module=cbupdater&action=cbupdaterAjax&file=setBlock&record='.$record.'&block=';
		if ($focus->column_fields['blocked']!='1') {
			echo '1">'.getTranslatedString('Block Changeset', $currentModule).'</a>';
		} else {
			echo '0">'.getTranslatedString('UnBlock Changeset', $currentModule).'</a>';
		}
	}
} else {
	echo getTranslatedString('LBL_RECORD_NOT_FOUND');
}
?>