<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
 
class GoogleSync4You {
 	
  	private $user_id = "";
    private $user_login = "";
    private $user_password = "";
    public $root_directory = "";
    public $mod_strings = array();
    public $status = "";
    public $is_logged = false;
    public $event = "";
    public $selected_calendar = "";
    
      
    function __construct() {
        global $root_directory, $current_language, $mod_strings;	    
        $this->db = PearDatabase::getInstance();
        $this->root_directory = $root_directory;
        
        $this->mod_strings = $mod_strings;
  	}
	
	public function getLogin() {
		return $this->user_login;
	}
	
	public function getStatus() {
		return $this->status;
	}
    
    public function isLogged() {
		return $this->is_logged;
	}
    
	public function setAccessDataForUser($userid, $only_active = false) {

        $sql = "SELECT gad.google_login, gad.google_password FROM vtiger_users 
        INNER JOIN its4you_googlesync4you_access AS gad ON gad.userid = vtiger_users.id
        WHERE vtiger_users.id=? AND gad.google_login != '' AND gad.google_password != ''";
		
        if ($only_active) $sql .= " AND vtiger_users.status = 'Active'";
        
        $result = $this->db->pquery($sql, array($userid));
		$num_rows = $this->db->num_rows($result);
		if ($num_rows == 1) 
        {
            $this->user_id = $userid;
            $this->user_login = $this->db->query_result($result,0,'google_login');
            $this->user_password = $this->db->query_result($result,0,'google_password');
		
            return true;
        }
		
		return false;
	}
    
    public function setAccessData($userid, $login, $password) {

            $this->user_id = $userid;
            $this->user_login = $login;
            $this->user_password = $password;
	}
    
	public function connectToGoogle() {
        
        $this->connectToGoogleViaZend();    
       
    }
    
    private function connectToGoogleViaZend() {
		
        set_include_path($this->root_directory. "modules/Calendar4You/");
        
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Gdata');
        Zend_Loader::loadClass('Zend_Gdata_AuthSub');
        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
        Zend_Loader::loadClass('Zend_Gdata_Calendar');
        
        if ($this->user_login != "" && $this->user_password != "") {
            try {
               $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
               $this->gClient = Zend_Gdata_ClientLogin::getHttpClient($this->user_login, $this->user_password, $service);
               $this->status = $this->mod_strings["LBL_OK"];
               $this->is_logged = true;
                
            } catch (Zend_Gdata_App_CaptchaRequiredException $cre) {
               $this->status = $this->mod_strings["LBL_URL_CAPTCHA_IMAGE"].': ' . $cre->getCaptchaUrl() . ', '.$this->mod_strings["LBL_TOKEN_ID"].': ' . $cre->getCaptchaToken();
            } catch (Zend_Gdata_App_AuthException $ae) {
               $this->status = $this->mod_strings["LBL_AUTH_PROBLEM"].': ' . $ae->exception() . "\n";
            }
        } else {
            $this->status = $this->mod_strings["LBL_MISSING_AUTH_DATA"];
        }
        
        if ($this->is_logged) {
            $this->gService = new Zend_Gdata_Calendar($this->gClient);
            
            try {
                $this->gListFeed = $this->gService->getCalendarListFeed();
            } catch (Zend_Gdata_App_Exception $e) {
                $this->gListFeed = array();
            }
        }
        
        set_include_path($this->root_directory);
       
	}
   
    public function getGoogleCalendars() {
        return $this->gListFeed;
    }
    
    public function setEvent($event,$load_user_calendar = true) {
        $this->event = $event;
        
        if ($load_user_calendar) $this->loadUserCalendar();
    }
    
    //$type: 1 = export, 2 = import
    public function isDisabled($type = 1) {

        $query = "SELECT * FROM `its4you_googlesync4you_dis` WHERE `userid`=? AND `event`=? AND `type` =?";
		$result = $this->db->pquery($query, array($this->user_id, $this->event, $type));
		$num_rows = $this->db->num_rows($result);
		if ($num_rows == 1) {
            return true;
        }
		
		return false;
    }
    
    public function loadUserCalendar() {
        
        $query = "SELECT calendar FROM `its4you_googlesync4you_calendar` WHERE `userid`=? AND `event`=? AND `type`=?";
		$result = $this->db->pquery($query, array($this->user_id, $this->event, "1"));
		$num_rows = $this->db->num_rows($result);
		if ($num_rows == 1) {
            $this->selected_calendar = $this->db->query_result($result,0,'calendar');
        }
    }
    
    public function getSCalendar($type) {
        
        if ($type == "1") {
            return $this->selected_calendar;    
        }
    }
    
    function getEvent($eventURL) {
		
        set_include_path($this->root_directory. "modules/Calendar4You/");
        try {
            $eventEntry = $this->gService->getCalendarEventEntry($eventURL);
        } catch (Zend_Gdata_App_Exception $e) {
            $eventEntry = false;
        }
        set_include_path($this->root_directory);
        
        return $eventEntry;
	}
    
    public function addEvent($recordid, $Data, $tzOffset) {
        set_include_path($this->root_directory. "modules/Calendar4You/");

        $startDate = $Data["date_start"];
        $endDate = $Data["due_date"];
        
        $startTime = $Data["time_start"];
        $endTime = $Data["time_end"];
        
	 	$GCalClass = new Zend_Gdata_Calendar($this->gClient);
		$newEntry = $GCalClass->newEventEntry();
        
		$newEntry->title = $GCalClass->newTitle(trim($Data["subject"]));
		$newEntry->where  = array($GCalClass->newWhere(trim($Data["location"])));

		$newEntry->content = $GCalClass->newContent($Data["description"]);
		$newEntry->content->type = 'text';
        
		$when = $GCalClass->newWhen();
		$when->startTime = $startDate.'T'.$this->removeLastColon($startTime).':00.000'.$tzOffset;
		$when->endTime = $endDate.'T'.$this->removeLastColon($endTime).':00.000'.$tzOffset;
		
		$newEntry->when = array($when);
		
        $SendEventNotifications = new Zend_Gdata_Calendar_Extension_SendEventNotifications(); 
        $SendEventNotifications->setValue(true); 
        $newEntry->SendEventNotifications = $SendEventNotifications;
		
        $whos = $this->getInvitedUsersEmails($GCalClass, $recordid); 
       
		if (count($whos) > 0) {
			$newEntry->setWho($whos);
		}

        $appCallUri = "";
        
        foreach ($this->gListFeed as $calendar) { 
            if ($calendar->id == $this->selected_calendar) $appCallUri = $calendar->content->src;
        }  

        $createdEntry = $GCalClass->insertEvent($newEntry, $appCallUri);

        set_include_path($this->root_directory);
        
        $eventid = urldecode($createdEntry->id->text);
        
        return $eventid;
	}
    
    function updateEvent($recordid, $eventOld, $Data, $tzOffset = '+00:00') {
		set_include_path($this->root_directory. "modules/Calendar4You/");
        
        $startDate = $Data["date_start"];
        $endDate = $Data["due_date"];
        
        $startTime = $Data["time_start"];
        $endTime = $Data["time_end"];
        
        
        $GCalClass = new Zend_Gdata_Calendar($this->gClient);
		$eventOld->title = $GCalClass->newTitle(trim($Data["subject"]));
		$eventOld->where = array($GCalClass->newWhere(trim($Data["location"])));
		$eventOld->content = $GCalClass->newContent($Data["description"]);
		$when = $GCalClass->newWhen();
		$when->startTime = $startDate.'T'.$this->removeLastColon($startTime).':00.000'.$tzOffset;
		$when->endTime = $endDate.'T'.$this->removeLastColon($endTime).':00.000'.$tzOffset;
		$eventOld->when = array($when);
		
        $whos = $this->getInvitedUsersEmails($GCalClass, $recordid); 
       
		if (count($whos) > 0) {
			$eventOld->setWho($whos);
		}
			
		try {
			$eventOld->save();
            $status = true;
		} catch (Zend_Gdata_App_Exception $e) {
		    $status = null;
		}
        
        set_include_path($this->root_directory);
        
        return $status;
	}
    
    public function deleteEvent($recordid, $eventURL) {

        $gevent = $this->getEvent($eventURL);
        
        set_include_path($this->root_directory. "modules/Calendar4You/");
        $this->gService->delete($gevent->getEditLink()->href);
        set_include_path($this->root_directory); 

	}
     
    private function getInvitedUsersEmails($GCalClass,$recordid) {
		
        $whos = array();
        $sql = 'select vtiger_users.email1 from vtiger_invitees left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id where activityid=?';
    	$result = $this->db->pquery($sql, array($recordid));
    	$num_rows=$this->db->num_rows($result);
    	
        if ($num_rows > 0)
        {
        	for($i=0;$i<$num_rows;$i++)
        	{
        		$userid=$this->db->query_result($result,$i,'inviteeid');
        		$who = $GCalClass->newwho();
    			$who->setEmail($googleEmail);
    			$whos[] = $who;
        	}
        }  
        
        return $whos;
	}
    
    private function removeLastColon($text) {
		return substr($text, 0, -3);
	}
    
    public function saveEvent($recordid, $event, $Data) {
		if ($this->is_logged) {
			if($oldEvent = $this->getRecordsGEvent($recordid, $event)) 
            {
                if (!isset($Data["time_end"])) $Data["time_end"] = $Data["time_start"];
                $eventid = $this->updateEvent($recordid, $oldEvent,$Data,date('P'));
			} 
            else 
            {
                $eventid = $this->addEvent($recordid,$Data, date('P'));
                $this->insertIntoEvents($recordid, $eventid, $event);
			}
		}
	}
    
    public function getGEventId($recordid, $event) {
        $geventid = "";
        $sql = 'SELECT geventid FROM its4you_googlesync4you_events WHERE crmid = ? AND userid = ? AND eventtype = ?';
	    $result = $this->db->pquery($sql, array($recordid, $this->user_id, $event));
        $num_rows=$this->db->num_rows($result);
    	
        if ($num_rows > 0)
        {
            $geventid = $this->db->query_result($result,0,'geventid');
        }
        return $geventid;
	}
    
    public function getRecordsGEvent($recordid, $event) {
        
        $geventid = $this->getGEventId($recordid, $event);
        
        return $this->getEvent($geventid);
        
	}
    
    function insertIntoEvents($recordid, $geventid, $event) {
        
        $p = array($recordid, $geventid, $this->user_id, $event);
        
        $sql1 = "SELECT * FROM its4you_googlesync4you_events WHERE crmid = ? AND geventid = ? AND userid = ? AND eventtype = ?";
        $result1 = $this->db->pquery($sql1, $p);
        $num_rows1 = $this->db->num_rows($result1); 
        
        if ($num_rows1 == 0)
        {
            $sql2 = 'INSERT INTO its4you_googlesync4you_events (crmid,geventid,userid,eventtype) VALUES (?,?,?,?)';
            $result = $this->db->pquery($sql2, $p);
        }
	}
    
    function getGoogleCalEvents($calendar_feed) {
        set_include_path($this->root_directory. "modules/Calendar4You/");
        
        $user = str_replace("http://www.google.com/calendar/feeds/default/", '', $calendar_feed);
        
        $start_date = '2010-06-01';
        $end_date = '2015-06-30';
         
        $query = $this->gService->newEventQuery();
        
        $query->setUser($user);
        $query->setVisibility('private');
        $query->setProjection('full');
        $query->setOrderby('starttime');

        $query->setStartMin($start_date);
        $query->setStartMax($end_date);
        
        $event_list = $this->gService->getCalendarEventFeed($query); 
        set_include_path($this->root_directory);
        
        return $event_list;  
    }
    
    function getGoogleCalEvent($event_id) {
        set_include_path($this->root_directory. "modules/Calendar4You/");
       
        try {
            $event = $this->gService->getCalendarEventEntry($event_id);
        } catch (Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
            $event = false;
        }

        set_include_path($this->root_directory);

        return $event;  
    }
}
