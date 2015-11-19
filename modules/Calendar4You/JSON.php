<?php

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
	
	// Execute code when autocomplete is requested via 'Accounts' parameter in GET request
	if ( isset($_REQUEST['searchmodule']) && $_REQUEST['searchmodule'] == 'Accounts' ) {
	
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
		
		echo json_encode($allaccounts, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	
	// Execute code when autocomplete is requested via 'SalesOrders' parameter in GET request
	} elseif ( isset($_REQUEST['searchmodule']) && $_REQUEST['searchmodule'] == 'SalesOrders' ) {
		
		// Start the empty 'allsalesorders' array
		$allsalesorders = array();
		
		// Get the term from the jQuery autocomplete GET request
		$term = trim(strip_tags($_GET['term'])); 
		
		// Get the salesorders that match part of the search term
		// Check to see if there was an account ID passed in the URL
		if (isset($_REQUEST['accountid']) && $_REQUEST['accountid'] != "") {
			// If account ID was set, use it in the query
			$soAccountID = $_REQUEST['accountid'];
			$soresults = $adb->query("SELECT subject, salesorderid, salesorder_no, accountid FROM vtiger_salesorder WHERE salesorder_no LIKE '%$term%' AND accountid = $soAccountID");
			// Show a message when this account has no sales orders
			if ( ($adb->num_rows($soresults) == 0) ) {
				// Create a JSON array that shows a message for this case
				$JsonSO = array("value"=>"",
								"label"=>"Er zijn voor",
								"subject"=>"dit account",
								"accountname"=>"geen verkooporders"
								);
				// Push the current account with message about no results to 'allsalesorders'
				$allsalesorders[] = $JsonSO;
			}
		} else {
			// If no account was selected
			$soresults = $adb->query("SELECT subject, salesorderid, salesorder_no, accountid FROM vtiger_salesorder WHERE salesorder_no LIKE '%$term%'");
		}
		
		// Only if there are rows to return
		if ( ($adb->num_rows($soresults) > 0) ) {
		
			// Loop salesorders
			while($so=$adb->fetch_array($soresults)) {
				
				// Create empty JSON array for this salesorder
				$JsonSO = array();
				
				// Start filling it
				$JsonSO['value'] = $so[salesorderid];
				$JsonSO['label'] = $so[salesorder_no];
				$JsonSO['subject'] = $so[subject];
						
				// 'vtiger_salesorder' table only has account id,
				// so query the accounts table also for the account name
				$soAccountRes = $adb->pquery("SELECT accountname FROM vtiger_account WHERE accountid=?", array($so[accountid]));
				
				// Loop the results for the account name
				while ($account=$adb->fetch_array($soAccountRes)) {
					// Add the account name and address as the JSON label
					$JsonSO['accountname'] = $account[accountname];
				}
					
				// Push the current account with address info to the 'allsalesorder' array
				$allsalesorders[] = $JsonSO;
			}
			
		}
		
		echo json_encode($allsalesorders, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	}

?>