<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

$ImportConfig = array(
	'importTypes' => array(
						'csv' => array('reader' => 'Import_CSV_Reader', 'classpath' => 'modules/Import/readers/CSVReader.php'),
						'vcf' => array('reader' => 'Import_VCard_Reader', 'classpath' => 'modules/Import/readers/VCardReader.php'),
						'default' => array('reader' => 'Import_File_Reader', 'classpath' => 'modules/Import/readers/FileReader.php')
					),

	'userImportTablePrefix' => 'vtiger_import_',
	// Individual batch limit - Specified number of records will be imported at one shot and the cycle will repeat till all records are imported
	'importBatchLimit' => '250',
	// Threshold record limit for immediate import. If record count is more than this, then the import is scheduled through cron job
	'immediateImportLimit' => '1000',
);


?>
