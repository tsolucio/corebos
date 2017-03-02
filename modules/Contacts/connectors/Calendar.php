<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

vimport('~~/modules/WSAPP/synclib/connectors/TargetConnector.php');
vimport('~~/libraries/google-api-php-client/src/Google/Client.php');
vimport('~~/libraries/google-api-php-client/src/Google/Service/Calendar.php');

Class Google_Calendar_Connector extends WSAPP_TargetConnector {
    
    const maxBatchRequestCount = 50;

    protected $apiConnection;
    protected $totalRecords;
    protected $maxResults = 100;
    protected $createdRecords;
    
    protected $client;
    protected $service;

    public function __construct($oauth2Connection) {
        $this->apiConnection = $oauth2Connection;
        $this->client = new Google_Client();
        $this->client->setClientId($oauth2Connection->getClientId());
        $this->client->setClientSecret($oauth2Connection->getClientSecret());
        $this->client->setRedirectUri($oauth2Connection->getRedirectUri());
        $this->client->setScopes($oauth2Connection->getScope());
        $this->client->setAccessType($oauth2Connection->getAccessType());
        $this->client->setApprovalPrompt($oauth2Connection->getApprovalPrompt());
        $this->client->setAccesstoken($oauth2Connection->getAccessToken());
        $this->service = new Google_Service_Calendar($this->client);
    }

    public function getName() {
        return 'GoogleCalendar';
    }

    /**
     * Tarsform Google Records to Vtiger Records
     * @param <array> $targetRecords 
     * @return <array> tranformed Google Records
     */
    public function transformToSourceRecord($targetRecords, $user = false) {
        $entity = array();
        $calendarArray = array();
        foreach ($targetRecords as $googleRecord) {
            if ($googleRecord->getMode() != WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                if(!$user)
                    $user = Users_Record_Model::getCurrentUserModel();
                $entity['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $user->id);
                $entity['subject'] = $googleRecord->getSubject();
                $entity['date_start'] = $googleRecord->getStartDate($user);
                $entity['location'] = $googleRecord->getWhere();
                $entity['time_start'] = $googleRecord->getStartTimeUTC($user);
                $entity['due_date'] = $googleRecord->getEndDate($user);
                $entity['time_end'] = $googleRecord->getEndTimeUTC($user);
                $entity['eventstatus'] = "Planned";
                $entity['activitytype'] = "Meeting";
                $entity['description'] = $googleRecord->getDescription();
                $entity['duration_hours'] = '00:00';
                $entity['visibility'] = $googleRecord->getVisibility($user);
                if (empty($entity['subject'])) {
                    $entity['subject'] = 'Google Event';
                }
            }
            $calendar = $this->getSynchronizeController()->getSourceRecordModel($entity);

            $calendar = $this->performBasicTransformations($googleRecord, $calendar);
            $calendar = $this->performBasicTransformationsToSourceRecords($calendar, $googleRecord);
            $calendarArray[] = $calendar;
        }

        return $calendarArray;
    }

    /**
     * Pull the events from google
     * @param <object> $SyncState
     * @return <array> google Records
     */
    public function pull($SyncState, $user = false) {
        return $this->getCalendar($SyncState, $user);
    }
    
    /**
     * Function to convert datetime to RFC 3339 timestamp
     * @param <String> $date
     * @return <DateTime>
     */
    function googleFormat($date) {
        $datTime = new DateTime($date);
        $timeZone = new DateTimeZone('UTC');
        $datTime->setTimezone($timeZone);
        $googleFormat = $datTime->format('Y-m-d\TH:i:s\Z');
        return $googleFormat;
    }

    /**
     * Pull the events from google
     * @param <object> $SyncState
     * @return <array> google Records
     */
    public function getCalendar($SyncState, $user = false) {
        if($this->apiConnection->isTokenExpired()) {
            $this->apiConnection->refreshToken();
            $this->client->setAccessToken($this->apiConnection->getAccessToken());
            $this->service = new Google_Service_Calendar($this->client);
        }
        $query = array(
            'maxResults' => $this->maxResults,
            'orderBy' => 'updated',
            'singleEvents' => true,
        );
        
        if (Google_Utils_Helper::getSyncTime('Calendar', $user)) {
            $query['updatedMin'] = $this->googleFormat(Google_Utils_Helper::getSyncTime('Calendar', $user));
            //shows deleted by default
        }
        
        try {
            $feed = $this->service->events->listEvents('primary',$query);
        } catch (Exception $e) {
            if($e->getCode() == 410) {
                $query['showDeleted'] = false;
                $feed = $this->service->events->listEvents('primary',$query);
            }
        }
        
        
        $calendarRecords = array();
        if($feed) {
            $calendarRecords = $feed->getItems();
            if($feed->getNextPageToken()) $this->totalRecords = $this->maxResults + 1;
        }
        
        if (count($calendarRecords) > 0) {
            $maxModifiedTime = date('Y-m-d H:i:s', strtotime(Google_Calendar_Model::vtigerFormat(end($calendarRecords)->getUpdated())) + 1);
        }

        $googleRecords = array();
        foreach ($calendarRecords as $i => $calendar) {
            $recordModel = Google_Calendar_Model::getInstanceFromValues(array('entity' => $calendar));
            $deleted = false;
            if ($calendar->getStatus() == 'cancelled') {
                $deleted = true;
            }
            if (!$deleted) {
                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE);
            } else {
                $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode(WSAPP_SyncRecordModel::WSAPP_DELETE_MODE);
            }
            $googleRecords[$calendar->getId()] = $recordModel;
        }
        $this->createdRecords = count($googleRecords);
        if (isset($maxModifiedTime)) {
            Google_Utils_Helper::updateSyncTime('Calendar', $maxModifiedTime, $user);
        } else {
            Google_Utils_Helper::updateSyncTime('Calendar', false, $user);
        }
        return $googleRecords;
    }

    /**
     * Push the vtiger records to google
     * @param <array> $records vtiger records to be pushed to google
     * @return <array> pushed records
     */
    public function push($records) {
        //TODO : use batch requests
        foreach ($records as $record) {
            $entity = $record->get('entity');
            if($this->apiConnection->isTokenExpired()) {
                $this->apiConnection->refreshToken();
                $this->client->setAccessToken($this->apiConnection->getAccessToken());
                $this->service = new Google_Service_Calendar($this->client);
            }
            try {
                if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                    $newEntity = $this->service->events->update('primary',$entity->getId(),$entity);
                    $record->set('entity', $newEntity);
                } else if ($record->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                    $record->set('entity', $entity);
                    $newEntity = $this->service->events->delete('primary',$entity->getId());
                } else {
                    $newEntity = $this->service->events->insert('primary',$entity);
                    $record->set('entity', $newEntity);
                } 
                
            } catch (Exception $e) {
                continue;
            }
        }
        return $records;
    }

    /**
     * Tarsform  Vtiger Records to Google Records
     * @param <array> $vtEvents 
     * @return <array> tranformed vtiger Records
     */
    public function transformToTargetRecord($vtEvents) {
        $records = array();
        foreach ($vtEvents as $vtEvent) {
            $newEvent = new Google_Service_Calendar_Event();

            if ($vtEvent->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                $newEvent->setId($vtEvent->get('_id'));
            } elseif($vtEvent->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE && $vtEvent->get('_id')) {
                if($this->apiConnection->isTokenExpired()) {
                    $this->apiConnection->refreshToken();
                    $this->client->setAccessToken($this->apiConnection->getAccessToken());
                    $this->service = new Google_Service_Calendar($this->client);
                }
                $newEvent = $this->service->events->get('primary', $vtEvent->get('_id'));
            }
            
            $newEvent->setSummary($vtEvent->get('subject'));
            $newEvent->setLocation($vtEvent->get('location'));
            $newEvent->setDescription($vtEvent->get('description'));
            $newEvent->setVisibility(strtolower($vtEvent->get('visibility')));
            
            $startDate = $vtEvent->get('date_start');
            $startTime = $vtEvent->get('time_start');
            $endDate = $vtEvent->get('due_date');
            $endTime = $vtEvent->get('time_end');
            if (empty($endTime)) {
                $endTime = "00:00";
            }
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($this->googleFormat($startDate . ' ' . $startTime));
            $newEvent->setStart($start);
            
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($this->googleFormat($endDate. ' ' .$endTime)); 
            $newEvent->setEnd($end);
            
            $recordModel = Google_Calendar_Model::getInstanceFromValues(array('entity' => $newEvent));
            $recordModel->setType($this->getSynchronizeController()->getSourceType())->setMode($vtEvent->getMode())->setSyncIdentificationKey($vtEvent->get('_syncidentificationkey'));
            $recordModel = $this->performBasicTransformations($vtEvent, $recordModel);
            $recordModel = $this->performBasicTransformationsToTargetRecords($recordModel, $vtEvent);
            $records[] = $recordModel;
        }
        return $records;
    }

    /**
     * returns if more records exits or not
     * @return <boolean> true or false
     */
    public function moreRecordsExits() {
        return ($this->totalRecords - $this->createdRecords > 0) ? true : false;
    }

}
?>

