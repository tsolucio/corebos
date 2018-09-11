<?php
/*
 * @Author: Edmond Kacaj
 * @Date: 2018-09-11 11:54:44
 * @Last Modified by: edmondikacaj@gmail.com
 * @Last Modified time: 2018-09-11 12:00:14
 */

//SecondModuleMasterDetail.php

include 'XmlContent.php';
include 'All_functions.php';

$mm = $_REQUEST['mod'];

echo GetModulRelOneTomulti($mm);
