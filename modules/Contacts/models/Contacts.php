<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'modules/WSAPP/synclib/models/SyncRecordModel.php';

class Google_Contacts_Model extends WSAPP_SyncRecordModel {

	/**
	 * return id of Google Record
	 * @return <string> id
	 */
	public function getId() {
		return $this->data['entity']['id']['$t'];
	}

	/**
	 * return modified time of Google Record
	 * @return <date> modified time
	 */
	public function getModifiedTime() {
		return $this->vtigerFormat($this->data['entity']['updated']['$t']);
	}

	public function getNamePrefix() {
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$name']) && isset($this->data['entity']['gd$name']['gd$namePrefix'])
			&& isset($this->data['entity']['gd$name']['gd$namePrefix']['$t'])
		) {
			$namePrefix = $this->data['entity']['gd$name']['gd$namePrefix']['$t'];
		} else {
			$namePrefix = '';
		}
		return $namePrefix;
	}

	/**
	 * return first name of Google Record
	 * @return <string> $first name
	 */
	public function getFirstName() {
		$fname='';
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$name']) && isset($this->data['entity']['gd$name']['gd$givenName'])
			&& isset($this->data['entity']['gd$name']['gd$givenName']['$t'])
		) {
			$fname = $this->data['entity']['gd$name']['gd$givenName']['$t'];
		}
		return $fname;
	}

	/**
	 * return Lastname of Google Record
	 * @return <string> Last name
	 */
	public function getLastName() {
		$lname='';
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$name']) && isset($this->data['entity']['gd$name']['gd$familyName'])
			&& isset($this->data['entity']['gd$name']['gd$familyName']['$t'])
		) {
			$lname = $this->data['entity']['gd$name']['gd$familyName']['$t'];
		}
		return $lname;
	}

	/**
	 * return Emails of Google Record
	 * @return <array> emails
	 */
	public function getEmails() {
		$arr=array();
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$email'])) {
			$arr = $this->data['entity']['gd$email'];
		}
		$emails = array();
		if (is_array($arr)) {
			foreach ($arr as $email) {
				if (isset($email['rel'])) {
					$labelEmail = parse_url($email['rel'], PHP_URL_FRAGMENT);
				} else {
					$labelEmail = $email['label'];
				}
				$emails[$labelEmail] = $email['address'];
			}
		}
		return $emails;
	}

	/**
	 * return Phone number of Google Record
	 * @return <array> phone numbers
	 */
	public function getPhones() {
		$arr=array();
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$phoneNumber'])) {
			$arr = $this->data['entity']['gd$phoneNumber'];
		}
		$phones = array();
		if (is_array($arr)) {
			foreach ($arr as $phone) {
				$phoneNo = $phone['$t'];
				if (isset($phone['rel'])) {
					$labelPhone = parse_url($phone['rel'], PHP_URL_FRAGMENT);
				} else {
					$labelPhone = $phone['label'];
				}
				$phones[$labelPhone] = $phoneNo;
			}
		}
		return $phones;
	}

	/**
	 * return Addresss of Google Record
	 * @return <array> Addresses
	 */
	public function getAddresses() {
		$arr=array();
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$structuredPostalAddress'])) {
			$arr = $this->data['entity']['gd$structuredPostalAddress'];
		}
		$addresses = array();
		if (is_array($arr)) {
			foreach ($arr as $address) {
				$structuredAddress = array(
					'street' => ((isset($address['gd$street']) && isset($address['gd$street']['$t'])) ? $address['gd$street']['$t'] : '' ),
					'pobox' => ((isset($address['gd$pobox']['$t']) && isset($address['gd$pobox'])) ? $address['gd$pobox']['$t']: '' ),
					'postcode' => ((isset($address['gd$postcode']) && isset($address['gd$postcode']['$t'])) ? $address['gd$postcode']['$t'] : '' ),
					'city' => ((isset($address['gd$city']) && isset($address['gd$city']['$t'])) ? $address['gd$city']['$t']: '' ),
					'region' => ((isset($address['gd$region']) && isset($address['gd$region']['$t'])) ? $address['gd$region']['$t']: '' ),
					'country' => ((isset($address['gd$country']) && isset($address['gd$country']['$t'])) ? $address['gd$country']['$t']: '' ),
					'formattedAddress'=>((isset($address['gd$formattedAddress']) && isset($address['gd$formattedAddress']['$t'])) ? $address['gd$formattedAddress']['$t'] : '')
				);
				if (isset($address['rel'])) {
					$labelAddress = parse_url($address['rel'], PHP_URL_FRAGMENT);
				} else {
					$labelAddress = $address['label'];
				}
				$addresses[$labelAddress] = $structuredAddress;
			}
		}
		return $addresses;
	}

	public function getUserDefineFieldsValues() {
		$fieldValues = array();
		$userDefinedFields=array();
		if (isset($this->data['entity']) && isset($this->data['entity']['gContact$userDefinedField'])) {
			$userDefinedFields = $this->data['entity']['gContact$userDefinedField'];
		}
		if (is_array($userDefinedFields) && count($userDefinedFields)) {
			foreach ($userDefinedFields as $userDefinedField) {
				$fieldName = $userDefinedField['key'];
				$fieldValues[$fieldName] = $userDefinedField['value'];
			}
		}
		return $fieldValues;
	}

	public function getUrlFields() {
		$websiteFields=array();
		if (isset($this->data['entity']) && isset($this->data['entity']['gContact$website'])) {
			$websiteFields = $this->data['entity']['gContact$website'];
		}
		$urls = array();
		if (is_array($websiteFields)) {
			foreach ($websiteFields as $website) {
				$url = $website['href'];
				if (isset($website['rel'])) {
					$fieldName = $website['rel'];
				} else {
					$fieldName = $website['label'];
				}
				$urls[$fieldName] = $url;
			}
		}
		return $urls;
	}

	public function getBirthday() {
		$birth='';
		if (isset($this->data['entity']) && isset($this->data['entity']['gContact$birthday']) && isset($this->data['entity']['gContact$birthday']['when'])) {
			$birth=$this->data['entity']['gContact$birthday']['when'];
		}
		return $birth;
	}

	public function getTitle() {
		$title='';
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$organization']) && isset($this->data['entity']['gd$organization'][0])
			&& isset($this->data['entity']['gd$organization'][0]['gd$orgTitle']) && isset($this->data['entity']['gd$organization'][0]['gd$orgTitle']['$t'])
		) {
			$title=$this->data['entity']['gd$organization'][0]['gd$orgTitle']['$t'];
		}
		return $title;
	}

	public function getAccountName() {
		$orgName='';
		if (isset($this->data['entity']) && isset($this->data['entity']['gd$organization']) && isset($this->data['entity']['gd$organization'][0])
			&& isset($this->data['entity']['gd$organization'][0]['gd$orgName']) && isset($this->data['entity']['gd$organization'][0]['gd$orgName']['$t'])
		) {
			$orgName = $this->data['entity']['gd$organization'][0]['gd$orgName']['$t'];
		}
		return $orgName;
	}

	public function getDescription() {
		$desc='';
		if (isset($this->data['entity']) && isset($this->data['entity']['content']) && isset($this->data['entity']['content']['$t'])) {
			$desc=$this->data['entity']['content']['$t'];
		}
		return $desc;
	}

	/**
	 * Returns the Google_Contacts_Model of Google Record
	 * @param <array> $recordValues
	 * @return Google_Contacts_Model
	 */
	public static function getInstanceFromValues($recordValues) {
		return new Google_Contacts_Model($recordValues);
	}

	/**
	 * converts the Google Format date to
	 * @param <date> $date Google Date
	 * @return <date> Vtiger date Format
	 */
	public static function vtigerFormat($date) {
		list($date, $timestring) = explode('T', $date);
		list($time, $tz) = explode('.', $timestring);
		return $date . ' ' . $time;
	}
}
?>
