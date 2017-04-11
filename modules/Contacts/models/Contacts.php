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

    function getNamePrefix() {
        $namePrefix = $this->data['entity']['gd$name']['gd$namePrefix']['$t'];
        return $namePrefix;
    }

    /**
     * return first name of Google Record
     * @return <string> $first name
     */
    function getFirstName() {
        $fname = $this->data['entity']['gd$name']['gd$givenName']['$t'];
        return $fname;
    }

    /**
     * return Lastname of Google Record
     * @return <string> Last name
     */
    function getLastName() {
        $lname = $this->data['entity']['gd$name']['gd$familyName']['$t'];
        return $lname;
    }

    /**
     * return Emails of Google Record
     * @return <array> emails
     */
    function getEmails() {
        $arr = $this->data['entity']['gd$email'];
        $emails = array();
        if (is_array($arr)) {
            foreach ($arr as $email) {
                if(isset($email['rel']))
                    $labelEmail = parse_url($email['rel'], PHP_URL_FRAGMENT);
                else
                    $labelEmail = $email['label'];
                $emails[$labelEmail] = $email['address'];
            }
        }
        return $emails;
    }

    /**
     * return Phone number of Google Record
     * @return <array> phone numbers
     */
    function getPhones() {

        $arr = $this->data['entity']['gd$phoneNumber'];
        $phones = array();
        if(is_array($arr)) {
            foreach ($arr as $phone) {
                $phoneNo = $phone['$t'];
                if(isset($phone['rel']))
                    $labelPhone = parse_url($phone['rel'], PHP_URL_FRAGMENT);
                else
                    $labelPhone = $phone['label'];
                $phones[$labelPhone] = $phoneNo;
            }
        }
        return $phones;
    }

    /**
     * return Addresss of Google Record
     * @return <array> Addresses
     */
    function getAddresses() {
        $arr = $this->data['entity']['gd$structuredPostalAddress'];
        $addresses = array();
        if(is_array($arr)) {
            foreach ($arr as $address) {
                $structuredAddress = array(
                    'street' => $address['gd$street']['$t'],
                    'pobox' => $address['gd$pobox']['$t'],
                    'postcode' => $address['gd$postcode']['$t'],
                    'city' => $address['gd$city']['$t'],
                    'region' => $address['gd$region']['$t'],
                    'country' => $address['gd$country']['$t'],
                    'formattedAddress' => $address['gd$formattedAddress']['$t']
                );
                if(isset($address['rel']))
                    $labelAddress = parse_url($address['rel'], PHP_URL_FRAGMENT);
                else
                    $labelAddress = $address['label'];
                $addresses[$labelAddress] = $structuredAddress;
            }
        }
        return $addresses;
    }

    function getUserDefineFieldsValues() {
        $fieldValues = array();
        $userDefinedFields = $this->data['entity']['gContact$userDefinedField'];
        if(is_array($userDefinedFields) && count($userDefinedFields)) {
            foreach($userDefinedFields as $userDefinedField) {
                $fieldName = $userDefinedField['key'];
                $fieldValues[$fieldName] = $userDefinedField['value'];
            }
        }
        return $fieldValues;
    }

    function getUrlFields() {
        $websiteFields = $this->data['entity']['gContact$website'];
        $urls = array();
        if(is_array($websiteFields)) {
            foreach($websiteFields as $website) {
                $url = $website['href'];
                if(isset($website['rel']))
                    $fieldName = $website['rel'];
                else
                    $fieldName = $website['label'];
                $urls[$fieldName] = $url;
            }
        }
        return $urls;
    }

    function getBirthday() {
        return $this->data['entity']['gContact$birthday']['when'];
    }

    function getTitle() {
        return $this->data['entity']['gd$organization'][0]['gd$orgTitle']['$t'];
    }

    function getAccountName() {
        $orgName = $this->data['entity']['gd$organization'][0]['gd$orgName']['$t'];
        return $orgName;
    }

    function getDescription() {
        return $this->data['entity']['content']['$t'];
    }

    /**
     * Returns the Google_Contacts_Model of Google Record
     * @param <array> $recordValues
     * @return Google_Contacts_Model
     */
    public static function getInstanceFromValues($recordValues) {
        $model = new Google_Contacts_Model($recordValues);
        return $model;
    }

    /**
     * converts the Google Format date to
     * @param <date> $date Google Date
     * @return <date> Vtiger date Format
     */
    public function vtigerFormat($date) {
        list($date, $timestring) = explode('T', $date);
        list($time, $tz) = explode('.', $timestring);
        return $date . " " . $time;
    }

}

?>
