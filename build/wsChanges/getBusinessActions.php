<?php
$operationInfo = array(
	 'name'    => 'getBusinessActions',
	 'include' => 'include/Webservices/getBusinessActions.php',
	 'handler' => 'getBusinessActions',
	 'prelogin'=> 0,
	 'type'    => 'GET',
	 'parameters' => array(
		array("name" => "view","type" => "String"),array("name" => "module","type" => "String"),array("name" => "id","type" => "String"),array("name" => "linktype","type" => "String")
	 )
);