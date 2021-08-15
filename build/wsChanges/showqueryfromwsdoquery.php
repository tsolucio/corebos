<?php
$operationInfo = array(
	'name'    => 'showqueryfromwsdoquery',
	'include' => 'include/Webservices/showqueryfromwsdoquery.php',
	'handler' => 'showqueryfromwsdoquery',
	'prelogin'=> 0,
	'type'    => 'POST',
	'parameters' => array(
		array('name' => 'query','type' => 'String'),
	)
);