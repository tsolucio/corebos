<?php
$operationInfo = array(
	'name'    => 'MassDelete',
	'include' => 'include/Webservices/MassDelete.php',
	'handler' => 'MassDelete',
	'prelogin'=> 0,
	'type'    => 'POST',
	'parameters' => array(
		array("name" => "ids","type" => "String")
	)
);