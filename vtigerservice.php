<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
if (isset($_REQUEST['service'])) {
	if ($_REQUEST['service'] == 'outlook') {
		include 'soap/vtigerolservice.php';
	} elseif ($_REQUEST['service'] == 'customerportal') {
		include 'soap/customerportal.php';
	} else {
		echo 'No Service Configured for '. vtlib_purify($_REQUEST['service']);
	}
} else {
	echo '<h1>Soap Services</h1>';
	echo "<li>Outlook Plugin EndPoint URL -- Click <a href='vtigerservice.php?service=outlook'>here</a></li>";
	echo "<li>Customer Portal EndPoint URL -- Click <a href='vtigerservice.php?service=customerportal'>here</a></li>";
}
?>