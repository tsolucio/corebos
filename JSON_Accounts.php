<?php

// error_reporting(E_ALL);
// ini_set("display_errors", "on");

	include_once('vtlib/Vtiger/Module.php');
	global $adb;
	
	// Lines 11-24 from http://www.wowww.nl/2014/02/01/jquery-autocomplete-tutorial-php-mysql/
	/* prevent direct access to this page */
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if(!$isAjax) {
	  $user_error = 'Access denied - direct call is not allowed...';
	  trigger_error($user_error, E_USER_ERROR);
	}
	ini_set('display_errors',1);
	 
	/* if the 'term' variable is not sent with the request, exit */
	if ( !isset($_REQUEST['term']) ) {
		exit;
	}
	
	// Start the empty 'allaccounts' array
	$allaccounts = array();
	
	// Get the term from the jQuery autocomplete GET request
	$term = trim(strip_tags($_GET['term'])); 
	
	// Get the accounts that match part of the search term
	$accountresults = $adb->query("SELECT accountid, account_no, accountname FROM vtiger_account WHERE accountname LIKE '%$term%'");

	// Loop accounts
	while($account=$adb->fetch_array($accountresults)) {
		
		// Create empty JSON array for this account
		$JsonAccount = array();
		
		// 'vtiger_account' table has no address data, so another query for the table that has those,
		// for the current account (in the array parameter)
		$accountaddressres = $adb->pquery("SELECT accountaddressid, ship_code, ship_street, ship_city FROM vtiger_accountshipads WHERE accountaddressid=?", array($account[accountid]));
		
		// Loop the shipping address resultset
		while ($address=$adb->fetch_array($accountaddressres)) {
			// Add the account name and address as the JSON label
			$JsonAccount['label'] = $account[accountname];
			$JsonAccount['value'] = $account[accountid];
			$JsonAccount['code'] = $address[ship_code];
			$JsonAccount['street'] = $address[ship_street];
			$JsonAccount['city'] = $address[ship_city];
		}
		
		// Push the current account with address info to the 'allaccounts' array
		$allaccounts[] = $JsonAccount;
	}
	
	// echo "<pre>";
	// print_r($allaccounts);
	// echo "</pre>";
	
	echo json_encode($allaccounts, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

?>