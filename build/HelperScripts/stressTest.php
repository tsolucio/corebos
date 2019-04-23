<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS.
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
// Turn on debugging level
$Vtiger_Utils_Log = true;
echo "\n";
$start = microtime(true);
include_once 'build/cbHeader.inc';
require_once 'modules/Contacts/Contacts.php';
$time_elapsed_us = microtime(true) - $start;
echo "Load time: $time_elapsed_us\n";

$num2create = 100;
$_REQUEST['assigntype'] == 'U';
$recs2del = array();
$start = microtime(true);
for ($i=0; $i<$num2create; $i++) {
	$focus = new Contacts();
	$focus->column_fields['firstname'] = 'firstname'.$i;
	$focus->column_fields['lastname'] = 'lastname'.$i;
	$focus->email_opt_out = 'off';
	$focus->do_not_call = 'off';
	$focus->column_fields['assigned_user_id'] = $current_user->id;
	$focus->save("Contacts");
	$recs2del[] = $focus->id;
	if (($i % 10) == 0) {
		echo $i."\n";
		foreach ($recs2del as $c) {
			$f2 = new Contacts();
			$f2->retrieve_entity_info($c, 'Contacts');
			DeleteEntity('Contacts', 'Contacts', $f2, $c, $c);
		}
		$recs2del = array();
	}
}
foreach ($recs2del as $c) {
	$f2 = new Contacts();
	$f2->retrieve_entity_info($c, 'Contacts');
	DeleteEntity('Contacts', 'Contacts', $f2, $c, $c);
}
$time_elapsed_us = microtime(true) - $start;
echo "Operation time: $time_elapsed_us\n";

?>
