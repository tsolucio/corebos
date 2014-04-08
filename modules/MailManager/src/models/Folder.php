<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_Model_Folder {
	protected $mName;
	protected $mCount;
	protected $mUnreadCount;
	
	protected $mMails;
	
	protected $mPageCurrent;
	protected $mPageStart;
	protected $mPageEnd;
	protected $mPageLimit;
	
	function __construct($name='') {
		$this->setName($name);
	}
	
	function name($prefix='') {
		$endswith = false;
		if (!empty($prefix)) {
			$endswith = (strrpos($prefix, $this->mName) === strlen($prefix)-strlen($this->mName));
		}
		if ($endswith) {
			return $prefix;
		} else {
			return $prefix.$this->mName;
		}
	}
	
	function setName($name) {
		$this->mName = $name;
	}
	
	function mails() {
		return $this->mMails;
	}
	
	function setMails($mails) {
		$this->mMails = $mails;
	}
	
	function setPaging($start, $end, $limit, $total, $current) {
		$this->mPageStart = intval($start);
		$this->mPageEnd = intval($end);
		$this->mPageLimit = intval($limit);
		$this->mCount = intval($total);
		$this->mPageCurrent = intval($current);
	}
	
	function pageStart() {
		return $this->mPageStart;
	}
	
	function pageEnd() {
		return $this->mPageEnd;
	}
	
	function pageInfo() {
		$offset = 0;
		if($this->mPageCurrent != 0) {	// this is needed as set the start correctly
			$offset = 1;
		}
		$s = max(1, $this->mPageCurrent * $this->mPageLimit + $offset);

		$st = ($s==1)? 0 : $s-1;  // this is needed to set end page correctly

		$e = min($st + $this->mPageLimit, $this->mCount);
		$t = $this->mCount;
		return sprintf("%s - %s of %s", $s, $e, $t);
	}
	
	function pageCurrent($offset=0) {
		return $this->mPageCurrent + $offset;
	}
	
	function hasNextPage() {
		return ($this->mPageStart > 1);
	}
	
	function hasPrevPage() {
		return ($this->mPageStart != $this->mPageEnd) && ($this->mPageEnd < $this->mCount);
	}
	
	function count() {
		return $this->mCount;
	}
	
	function setCount($count) {
		$this->mCount = $count;
	}
	
	function unreadCount() {
		return $this->mUnreadCount;
	}
	
	function setUnreadCount($unreadCount) {
		$this->mUnreadCount = $unreadCount;
	}
}

?>