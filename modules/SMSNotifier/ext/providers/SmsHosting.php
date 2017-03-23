<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
include_once dirname ( __FILE__ ) . '/../ISMSProvider.php';
//include_once 'vtlib/Vtiger/Net/Client.php';		// not used

class SmsHosting implements ISMSProvider {

	private $_username;
	private $_password;
	private $_parameters = array();
	public $helpURL = 'https://www.smshosting.it/en';
	public $helpLink = 'SmsHosting';

	const SERVICE_URI = 'https://api.smshosting.it/rest/api';
	private static $REQUIRED_PARAMETERS = array ( 'from', 'prefix' );

	function __construct ( ) {
	}

	/**
	 * Function to get provider name
	 * @return <String> provider name
	 */
	public function getName() {
		return $this->helpLink;
	}

	public function setAuthParameters ( $username, $password ) {
		$this->_username = $username;
		$this->_password = $password;
	}
	
	public function setParameter ( $key, $value ) {
		$this->_parameters [ $key ] = $value;
	}

	public function getParameter ( $key, $defvalue = false ) {
		if ( isset ( $this->_parameters [ $key ] ) ) {
			return $this->_parameters [ $key ] ;
		}
		return $defvalue;
	}

	public function getRequiredParams ( ) {
		return self::$REQUIRED_PARAMETERS;
	}

	public function getServiceURL ( $type = false ) {
		if ( $type ) {
			switch ( strtoupper ( $type ) ) {
				case self::SERVICE_AUTH: return self::SERVICE_URI . '/http/auth';
				case self::SERVICE_SEND: return self::SERVICE_URI . '/sms/send';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/http/querymsg';
			}
		}
		return false;
	}

	protected function prepareParameters ( ) {
		$params = array ( 'username' => $this->_username, 'password' => $this->_password );
		foreach ( self::$REQUIRED_PARAMETERS as $key ) {
			$params [ $key ] = $this->getParameter ( $key );
		}
		return $params;
	}

	public function send ( $message, $tonumbers ) {
		if ( !is_array ( $tonumbers ) ) {
			$tonumbers = array ( $tonumbers );
		}

		$params = $this->prepareParameters( );

		# format prefix
		if ( $params [ 'prefix' ] ) {
			$params [ 'prefix' ] = preg_replace ( '/[^0-9]/', '', $params [ 'prefix' ] );		// prefix has to be numeric (not alphanumeric)...
			$params [ 'prefix' ] = intval ( $params [ 'prefix' ] );		//... and integer (without initial 0)
		}

		# add prefix to recipient numbers
		foreach ( $tonumbers as $num ) {
			$key = ( array_keys ( $tonumbers, $num ) );	// $tonumbers array keys extraction
			$key = $key [ 0 ];		// the key of every different value 
			$num = trim ( $num );		// delete spaces
			$num = preg_replace ("/[^0-9]/ ",'', $num);		// delete from recipient all chars but numbers ...
			$num = $params [ 'prefix' ].$num;		// ... add the prefix ...
			$tonumbers [ $key ] = $num;		// ... recreate recipients array with 'formatted' numbers
		}
		$from = $params [ 'from' ] ;	// sender alphanumeric string or number

		$to = implode(',', $tonumbers);

		$response = $this->SmsHosting_SEND ( $from , $to, $message, $statusCallback=NULL, $unicode=NULL );
		$response = json_decode ( $response );

		$results = array ( ) ;

		# response without errors
		if ( $response && !$response->errorCode ) {
			foreach ( $response->sms as $sms ) {
				if ( $sms->status == "INSERTED" ) {
					$result [ 'id' ] = $sms->id;
					$result [ 'to' ] = $sms->to;
					$result [ 'error' ] = false;
					$result [ 'status' ] = self::MSG_STATUS_DISPATCHED;
					$result ['statusmessage'] = 'sent';
				} else {
					$result [ 'to' ] = $sms->to;
					$result [ 'error' ] = true;
					$result [ 'status' ] = self::MSG_STATUS_FAILED;
					$result [ 'statusmessage' ] = 'not sent'; // Complete error message
				}
				$results [] = $result;
			}
		} elseif ( $response->errorCode != NULL ) {
			foreach ( $tonumbers as $recipient ) {
				$result [ 'to' ] = $recipient;
				$result [ 'error' ] = true;
				$result [ 'status' ] = self::MSG_STATUS_FAILED;
				$result [ 'statusmessage' ] = 'not sent'; // Complete error message
				$results [] = $result;
			}
		}
		return $results;
	}

	public function query ( $messageid ) {
		$result = array ( 'error' => false, 'needlookup' => 1 );
		$result['status'] = self::MSG_STATUS_DISPATCHED;
		$result['needlookup'] = 0;
		return $result;
	}

	function SmsHosting_SEND ( $from, $to, $text, $statusCallback, $unicode ) {
		
		if ( $unicode === null ) {
			$containsUnicode = max ( array_map ( 'ord', str_split ( $text ) ) ) > 127;
		} else {
			$containsUnicode = ( bool ) $unicode;
		}
		
		// URL Encode
		$from				= urlencode ( $from );
		$to					= urlencode ( $to );
		$text					= urlencode ( $text );
		$statusCallback	= urlencode ( $statusCallback );
		
		// Send away!
		$post = array (
				'from'				=> $from,
				'to'					=> $to,
				'text'					=> $text,
				'statusCallback'	=> $statusCallback,
				'type'					=> $containsUnicode ? 'unicode' : 'text'
		);
		
		$complete_uri = $this->getServiceURL ( self::SERVICE_SEND );
		
		// submit HTTP request
		$response = $this->do_POST ( $complete_uri, $post );
		
		// check response
		if ( $response ) {
			return $response;
		} else {
			return false;
		}
	}

	function do_POST ( $complete_uri, $data ) {
		$post = "";
		foreach ( $data as $k => $v ) {
		$post .= "&$k=$v";
		}
		
		if ( function_exists ( 'curl_version' ) ) {
		
		$to_smsh = curl_init ( $complete_uri );
		curl_setopt ( $to_smsh, CURLOPT_POST, true );
		curl_setopt ( $to_smsh, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $to_smsh, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $to_smsh, CURLOPT_USERPWD, $this->_username.":". $this->_password );
		curl_setopt ( $to_smsh, CURLOPT_POSTFIELDS, $post );
		
		$from_smsh = curl_exec ( $to_smsh );
		
		curl_close ( $to_smsh );
		} elseif ( ini_get ( 'allow_url_fopen' ) ) {
			// No CURL available, so try the awesome file_get_contents
		
			$opts = array(
					'http' => array(
							'method' => 'POST',
							'ignore_errors' => true,
							'header' => "Authorization: Basic ".base64_encode ( $this->_username.":".$this->_password ) . "\r\nContent-type: application/x-www-form-urlencoded",
							'content' => $post
					)
			);
			$context = stream_context_create ( $opts ) ;
			$from_smsh = file_get_contents ( $complete_uri, false, $context ) ;
		} else {
		// No way of sending a HTTP post : (
		return false;
		}
		return $from_smsh;
	}
}
?>
