<?php
$operationInfo = array(
	 'name'    => 'executeBusinessAction',
	 'include' => 'include/Webservices/executeBusinessAction.php',
	 'handler' => 'executeBusinessAction',
	 'prelogin'=> 0,
	 'type'    => 'GET',
	 'parameters' => array(
		array("name" => "businessactionid","type" => "String")
	 )
);