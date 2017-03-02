<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

vimport('~~/modules/WSAPP/synclib/models/SyncRecordModel.php');

class Google_Calendar_Model extends WSAPP_SyncRecordModel {
    
    public $startUTC;
    public $endUTC;

    /**
     * return id of Google Record
     * @return <string> id
     */
    function getId() {
        return $this->data['entity']->getId();
    }

    /**
     * return modified time of Google Record
     * @return <date> modified time 
     */
    public function getModifiedTime() {
        return $this->vtigerFormat($this->data['entity']->getUpdated());
    }

    /**
     * return Subject of Google Record
     * @return <string> Subject
     */
    function getSubject() {
        return $this->data['entity']->getSummary();
    }

    /**
     * return Start time time in UTC of Google Record
     * @return <date> start time
     */
    function getStartTimeUTC($user = false) {
        if (isset($this->startUTC)) {
            return $this->startUTC;
        }
        if(!$user) {
            $user = Users_Record_Model::getCurrentUserModel();
        }
        $when = $this->data['entity']->getStart();
        if (empty($when)) {
            $gStart = "00:00";
        } else if ($when->getDateTime()) {
            $gStart = $when->getDateTime();
        } else {
            $gStart = "00:00";
        }
        $start = new DateTime($gStart);
        $timeZone = new DateTimeZone(date_default_timezone_get());
        $start->setTimezone($timeZone);
        $startUTC = $start->format('H:i:s');
        $gDateTime = $when->getDateTime();
        if($startUTC == '00:00:00' && empty($gDateTime)) {
            $userTimezone = $user->get('time_zone');
            $startUTCObj = DateTimeField::convertTimeZone($startUTC, $userTimezone, DateTimeField::getDBTimeZone());
            $startUTC = $startUTCObj->format('H:i:s');
        }
        $this->startUTC = $startUTC;
        return $startUTC;
    }

    /**
     * return End time time in UTC of Google Record
     * @return <date> end time
     */
    function getEndTimeUTC($user = false) {
        if (isset($this->endUTC)) {
            return $this->endUTC;
        }
        if(!$user) {
            $user = Users_Record_Model::getCurrentUserModel();
        }
        $when = $this->data['entity']->getEnd();
        if (empty($when)) {
            $gEnd = "00:00";
        } else if ($when->getDateTime()) {
            $gEnd = $when->getDateTime();
        } else {
            $gEnd = "00:00";
        }
        $end = new DateTime($gEnd);
        $timeZone = new DateTimeZone(date_default_timezone_get());
        $end->setTimezone($timeZone);
        $endUTC = $end->format('H:i:s');
        $gDateTime = $when->getDateTime();
        if($endUTC == '00:00:00' && empty($gDateTime)) {
            $userTimezone = $user->get('time_zone');
            $startUTCObj = DateTimeField::convertTimeZone($endUTC, $userTimezone, DateTimeField::getDBTimeZone());
            $endUTC = $startUTCObj->format('H:i:s');
        }
        $this->endUTC = $endUTC;
        return $endUTC;
    }

    /**
     * return start date in UTC of Google Record
     * @return <date> start date
     */
    function getStartDate($user = false) {
        if (isset($this->startDate)) {
            return $this->startdate;
        }
        if(!$user) {
            $user = Users_Record_Model::getCurrentUserModel();
        }
        $when = $this->data['entity']->getStart();
        if (empty($when)) {
            $gStart = date('Y-m-d');
        } else if ($when->getDateTime()) {
            $gStart = $when->getDateTime();
        } else if($when->getDate()) {
            $gStart = $when->getDate();
        } else {
            $gStart = date('Y-m-d');
        }
        $start = new DateTime($gStart);
        $timeZone = new DateTimeZone('UTC');
        $start->setTimezone($timeZone);
        $startDate = $start->format('Y-m-d');
        $gDateTime = $when->getDateTime();
        if($start->format('H:i:s') == '00:00:00' && empty($gDateTime)) {
            $userTimezone = $user->get('time_zone');
            $startUTCObj = DateTimeField::convertTimeZone($startDate, $userTimezone, DateTimeField::getDBTimeZone());
            $startDate = $startUTCObj->format('Y-m-d');
        }
        $this->startDate = $startDate;
        return $startDate;
    }

    /**
     * return  End  date in UTC of Google Record
     * @return <date> end date
     */
    function getEndDate($user = false) {
        if (isset($this->endUTC)) {
            return $this->endUTC;
        }
        if(!$user) {
            $user = Users_Record_Model::getCurrentUserModel();
        }
        $when = $this->data['entity']->getEnd();
        if (empty($when)) {
            $gEnd = date('Y-m-d');
        } else if ($when->getDateTime()) {
            $gEnd = $when->getDateTime();
        } else if ($when->getDate()) {
            $gEnd = $when->getDate();
        } else {
            $gEnd = date('Y-m-d');
        }
        $end = new DateTime($gEnd);
        $timeZone = new DateTimeZone('UTC');
        $end->setTimezone($timeZone);
        $endDate = $end->format('Y-m-d');
        $gDateTime = $when->getDateTime();
        if($end->format('H:i:s') == '00:00:00' && empty($gDateTime)) {
            $userTimezone = $user->get('time_zone');
            $endUTCObj = DateTimeField::convertTimeZone($endDate, $userTimezone, DateTimeField::getDBTimeZone());
            $endDate = $endUTCObj->format('Y-m-d');
        }
        $this->endDate = $endDate;
        return $endDate;
    }

    /**
     * return tilte of Google Record
     * @return <string> title
     */
    function getTitle() {
        $title = $this->data['entity']->getSummary();
        return empty($title) ? null : $title;
    }
    
    /**
     * function to get Visibility of google calendar event
     * @return <string> visibility of google event (Private or Public)
     * @return <null> if google event visibility is default
     */
    function getVisibility($user) {
        $visibility = $this->data['entity']->getVisibility();
        if(strpos($visibility, 'private') !== false)
            return 'Private';
        else if(strpos($visibility, 'public') !== false)
            return 'Public';
        else {
            $calendarsharedtype = $user->get('calendarsharedtype');
            if($calendarsharedtype == 'selectedusers' || $calendarsharedtype == 'public') {
                return 'Public';
            }
            return 'Private';
        }
    }

    /**
     * return discription of Google Record
     * @return <string> Discription
     */
    function getDescription() {
        return $this->data['entity']->getDescription();
    }

    /**
     * return location of Google Record
     * @return <string> location
     */
    function getWhere() {
        $where = $this->data['entity']->getLocation();
        return $where;
    }

    /**
     * Returns the Google_Contacts_Model of Google Record
     * @param <array> $recordValues
     * @return Google_Contacts_Model
     */
    public static function getInstanceFromValues($recordValues) {
        $model = new Google_Calendar_Model($recordValues);
        return $model;
    }

    /**
     * converts the Google Format date to 
     * @param <date> $date Google Date
     * @return <date> Vtiger date Format
     */
    public function vtigerFormat($date) {
        $origDate = $date;
        
        list($date, $timestring) = explode('T', $date);
        list($time, $tz) = explode('.', $timestring);
        
       // EDIT - if this is UTC lets change it to correct system time
        if(substr($tz,-1) == 'Z') {
            $date = new DateTime($origDate);
            $timeZone = new DateTimeZone(date_default_timezone_get());
            $date->setTimezone($timeZone);
            $date = $date->format('Y-m-d H:i:s');
            return $date;
        }
        return $date . " " . $time;
    }

}

?>
