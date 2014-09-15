<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

/**
 * Description of VtigerBackupException
 *
 * @author MAK
 */
class VtigerBackupException extends Exception{
	public $code;
	public $message;

	function VtigerBackupException($errCode,$msg){
		$this->code = $errCode;
		$this->message = $msg;
	}
}

class VtigerBackupErrorCode {
	public static $ZIP_CREATE_FAILED = 'CONNECT_ERROR';
	public static $TABLE_NAME_ERROR = 'TABLE_LIST_FETCH_ERROR';
	public static $SQL_EXECUTION_ERROR = 'SQL_EXECUTION_ERROR';
	public static  $FTP_CONNECT_FAILED = 'FTP_CONNECT_FAILED';
	public static  $FTP_LOGIN_FAILED = 'FTP_LOGIN_FAILED';
}

?>