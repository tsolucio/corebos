<?php
$operationInfo = array(
	'name'    => 'MassUpdate',
	'include' => 'include/Webservices/MassUpdate.php',
	'handler' => 'vtws_massupdate',
	'prelogin'=> 0,
	'type'    => 'POST',
	'parameters' => array(
		array('name' => 'elements', 'type' => 'encoded')
	)
);