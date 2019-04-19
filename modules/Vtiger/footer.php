<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
if (isset($adb) && !empty($current_user->id)) {
	$COMMONFTRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, array('FOOTERSCRIPT'), array('MODULE'=>$currentModule));
	foreach ($COMMONFTRLINKS['FOOTERSCRIPT'] as $fscript) {
		echo '<script type="text/javascript" src="' . $fscript->linkurl . '"></script>';
	}
}
cbEventHandler::do_action('corebos.footer');
?>
</td></tr>
<tr><td colspan="2" align="center">
</td></tr></table>
</body>
</html>
