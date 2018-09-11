
<?php
/*
 * @Author: Edmond Kacaj
 * @Date: 2018-09-11 11:42:55
 * @Last Modified by:   edmondikacaj@gmail.com
 * @Last Modified time: 2018-09-11 11:42:55
 */
//SecondModuleMasterDetail.php

include 'XmlContent.php';
include 'modfields.php';

$mm = $_REQUEST['mod'];
$arrayName = array();
$select = "<option selected value=''>Select a Picklist</option>";
$select .= getModFields($mm, "", $arrayName, "15,33");
echo $select;
?>