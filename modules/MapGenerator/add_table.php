
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
 *************************************************************************************************/
$db = $_POST['nameDb'];
$params['schema'] = $db;
$params['table'] = $_POST['tab'];
$settings = false;
#support specifying location of .ini file on command line
if (!empty($params['ini'])) {
    $settings = @parse_ini_file($params['ini'], true);
}

require_once 'modules/MapGenerator/flexviews/include/flexcdc.php';
$cdc = new FlexCDC($settings);

if (empty($params['schema']) || empty($params['table'])) {
    die("usage: add_table.php --schema=<SCHEMA> --table=<TABLE>\nWhere SCHEMA is the name of the database and table is the name of the table\n");
}

if (!$cdc->create_mvlog($params['schema'], $params['table'])) {
    die("failure: Could not create the log table\n");
}

echo "\n<b>Sono state aggiunte le tabelle per i log<b>\n";

?>
