<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Documentation.
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
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';

$current_user = Users::getActiveAdminUser();

$uitypes = array(
	'1'=>'string',
	'2'=>'string',
	'3'=>'access key',
	'4'=>'autonumber',
	'5'=>'date',
	'6'=>'datetime (internal)',
	'30'=>'timereminder',
	'50'=>'datetime',
	'63'=>'timespan',
	'70'=>'datetime',
	'7'=>'number',
	'71'=>'currency',
	'72'=>'currency',
	'9'=>'percentage',
	'10'=>'relation',
	'98'=>'role',
	'11'=>'phone',
	'8'=>'email',
	'12'=>'email',
	'13'=>'email',
	'14'=>'time',
	'115'=>'user status picklist',
	'15'=>'picklist',
	'16'=>'picklist',
	'1613'=>'module picklist',
	'1614'=>'all module picklist',
	'1615'=>'all picklist picklist',
	'17'=>'url',
	'19'=>'text',
	'20'=>'text',
	'21'=>'text',
	'22'=>'text',
	'23'=>'date',
	'24'=>'text',
	'26'=>'document folder',
	'27'=>'document location',
	'28'=>'document file name',
	'32'=>'language picklist',
	'31'=>'theme picklist',
	'33'=>'multipicklist',
	'3313'=>'module multi-list',
	'3314'=>'all module multi-list',
	'51'=>'relation (account)',
	'52'=>'user',
	'53'=>'user',
	'77'=>'user',
	'101'=>'user',
	'117'=>'currency',
	'56'=>'checkbox',
	'156'=>'is admin checkbox',
	'57'=>'relation(contact)',
	'66'=>'picklist(calendar)',
	'105'=>'image',
	'69'=>'image',
	'69m'=>'multi-image',
	'61'=>'attachment',
	'85'=>'skype',
	'83'=>'taxes',
	'255'=>'picklist (salutation)',
	'55'=>'contact first name',
	'99' => 'password',
	'106' => 'user name',
	'357' => 'entities related to email',
);

if ($argc==2 && !empty($argv[1])) {
	$mobj = Vtiger_Module::getInstance($argv[1]);
	if (!$mobj) {
		echo "\nCannot find the module: ".$argv[1]."\n\n";
		exit(1);
	}
	echo '---- '.$argv[1]." ----\n";
	foreach ($mobj->getFields() as $field) {
		echo $field->table."\t".$field->column."\t".$field->uitype.' ('.$uitypes[$field->uitype].")\n";
	}
} else {
	echo "listfields: list all the fields of the given module\n";
	echo "\n\n  php listfields.php module_name\n\n";
}
?>