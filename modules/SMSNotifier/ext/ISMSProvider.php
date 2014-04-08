<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
interface ISMSProvider {
	
	const MSG_STATUS_DISPATCHED = "Dispatched";
	const MSG_STATUS_PROCESSING = "Processing";
	const MSG_STATUS_DELIVERED  = "Delivered";
	const MSG_STATUS_FAILED     = "Failed";
	const MSG_STATUS_ERROR      = "ERR: ";
	
	const SERVICE_SEND = "SEND";
	const SERVICE_QUERY= "QUERY";
	const SERVICE_PING = "PING";
	const SERVICE_AUTH = "AUTH";
	
	/**
	 * Get required parameters other than (username, password)
	 */
	public function getRequiredParams();
	
	/**
	 * Get service URL to use for a given type
	 *
	 * @param String $type like SEND, PING, QUERY
	 */
	public function getServiceURL($type = false);
	
	/**
	 * Set authentication parameters
	 *
	 * @param String $username
	 * @param String $password
	 */
	public function setAuthParameters($username, $password);
	
	/**
	 * Set non-auth parameter.
	 *
	 * @param String $key
	 * @param String $value
	 */
	public function setParameter($key, $value);
	
	/**
	 * Handle SMS Send operation
	 *
	 * @param String $message
	 * @param mixed $tonumbers One or Array of numbers
	 */
	public function send($message, $tonumbers);
	
	/**
	 * Query for status using messgae id
	 *
	 * @param String $messageid
	 */
	public function query($messageid);
	
	
}
?>
