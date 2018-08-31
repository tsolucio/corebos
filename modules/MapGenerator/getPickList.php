<?php
//SecondModuleMasterDetail.php

include 'XmlContent.php';
include 'modfields.php';

$mm =$_REQUEST['mod'];
$arrayName = array();
$select="<option selected value=''>Select a Picklist</option>";
$select.=getModFields($mm,"",$arrayName,"15,33");
echo $select;
?>