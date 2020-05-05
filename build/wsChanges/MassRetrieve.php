<?php
$operationInfo = array(
	'name'    => 'MassRetrieve',
	'include' => 'include/Webservices/MassRetrieve.php',
	'handler' => 'vtws_massretrieve',
	'prelogin'=> 0,
	'type'    => 'POST',
	'parameters' => array(
		array('name' => 'ids', 'type' => 'String')
	)
);