<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function ftpBackupFile($source_file, $ftpserver, $ftpuser, $ftppassword) {
	global $log;
	$FTPOK = 0;
	$NOCONNECTION = 1;
	$NOLOGIN = 2;
	$NOUPLOAD = 3;
	$log->debug("Entering ftpBackupFile(".$source_file.", ".$ftpserver.", ".$ftpuser.", ".$ftppassword.") method ...");
	// set up basic connection
	list($host,$port) = explode(':', $ftpserver);
	if (empty($port)) {
		$conn_id = @ftp_connect($ftpserver);
	} else {
		$conn_id = @ftp_connect($host, $port);
	}
	if (!$conn_id) {
		$log->debug('Exiting ftpBackupFile method ...');
		return $NOCONNECTION;
	}

	// login with username and password
	$login_result = @ftp_login($conn_id, $ftpuser, $ftppassword);

	if (!$login_result) {
		ftp_close($conn_id);
		 $log->debug('Exiting ftpBackupFile method ...');
		return $NOLOGIN;
	}

	// upload the file
	$destination_file=basename($source_file);
	$upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

	// check upload status
	if (!$upload) {
		ftp_close($conn_id);
		 $log->debug('Exiting ftpBackupFile method ...');
		return $NOUPLOAD;
	}

	// close the FTP stream
	ftp_close($conn_id);
	$log->debug('Exiting ftpBackupFile method ...');
	return $FTPOK;
}
?>
