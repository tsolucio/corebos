<?php
$db=$_POST['nameDb'];
$params['schema']=$db;
$params['table']=$_POST['tab'];
$settings = false;
#support specifying location of .ini file on command line
if(!empty($params['ini'])) {
	$settings = @parse_ini_file($params['ini'], true);
}

require_once('modules/MapGenerator/flexviews/include/flexcdc.php');
$cdc = new FlexCDC($settings);

if(empty($params['schema']) || empty($params['table'])) {
	die("usage: add_table.php --schema=<SCHEMA> --table=<TABLE>\nWhere SCHEMA is the name of the database and table is the name of the table\n");
}

if(!$cdc->create_mvlog($params['schema'], $params['table'])) {
	die("failure: Could not create the log table\n");
}

echo "\n<b>Sono state aggiunte le tabelle per i log<b>\n";

?>
