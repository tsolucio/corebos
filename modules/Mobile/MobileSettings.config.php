<?php
$displayed_modules= array(
	'Contacts',
	'Accounts',
	'Leads',
	'Calendar',
	'Potentials',
	'HelpDesk',
	'Events',
	'Quotes',
	'SalesOrder'
);
$modules_with_comments= array(
	'Contacts',
	'Accounts',
	'Leads',
	'Potentials',
	'HelpDesk',
);

$config_settings = array(
'language' => 'ge_de',
//for Calendar: day, week, month, year
'calendarview' => 'week',
'compactcalendar' => 'on',
);
global $adb;
$sql="select * from vtiger_organizationdetails";
$result = $adb->pquery($sql, array());
//Handle for allowed organation logo/logoname likes UTF-8 Character
$companyDetails = array();
$companyDetails['name'] = $adb->query_result($result,0,'organizationname');
$companyDetails['website'] = $adb->query_result($result,0,'website');
$companyDetails['logo'] = decode_html($adb->query_result($result,0,'logoname'));

?>