<?php

include_once 'vtlib/Vtiger/Module.php';
if (coreBOS_Settings::getSetting('cbSMActive', 0)) {
	coreBOS_Settings::setSetting('cbSMActive', 0);
} else {
	coreBOS_Settings::setSetting('cbSMActive', 1);
}
?>