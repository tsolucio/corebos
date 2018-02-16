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
);
$ImportConfig['importBatchLimit'] = GlobalVariable::getVariable('Import_Batch_Limit', '250');
$ImportConfig['immediateImportLimit'] = GlobalVariable::getVariable('Import_Scheduled_Limit', 1000);
?>
