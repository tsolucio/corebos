<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaëtan KRONEISEN technique@expert-web.fr
 ********************************************************************************/
header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=".basename($_GET['zip_filename']));
$handle = fopen('../../'.$_GET['zip_filename'], 'rb');
fpassthru($handle);
fclose($handle);
unlink('../../'.$_GET['zip_filename']);
?>