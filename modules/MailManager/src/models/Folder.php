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

	public function __construct($name = '') {
		$this->setName($name);
	}

	public function name($prefix = '') {
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

	public function setName($name) {
		$this->mName = $name;
	}

	public function mails() {
		return $this->mMails;
	}

	public function setMails($mails) {
		$this->mMails = $mails;
	}

	public function setPaging($start, $end, $limit, $total, $current) {
		$this->mPageStart = (int)$start;
		$this->mPageEnd = (int)$end;
		$this->mPageLimit = (int)$limit;
		$this->mCount = (int)$total;
		$this->mPageCurrent = (int)$current;
	}

	public function pageStart() {
		return $this->mPageStart;
	}

	public function pageEnd() {
		return $this->mPageEnd;
	}

	public function pageInfo() {
		$offset = 0;
		if ($this->mPageCurrent != 0) { // this is needed as set the start correctly
			$offset = 1;
		}
		$s = max(1, $this->mPageCurrent * $this->mPageLimit + $offset);

		$st = ($s==1)? 0 : $s-1;  // this is needed to set end page correctly

		$e = min($st + $this->mPageLimit, $this->mCount);
		$t = $this->mCount;
		return sprintf('%s - %s '.getTranslatedString('LBL_LIST_OF').' %s', $s, $e, $t);
	}

	public function pageCurrent($offset = 0) {
		return $this->mPageCurrent + $offset;
	}

	public function hasNextPage() {
		return ($this->mPageStart > 1);
	}

	public function hasPrevPage() {
		return ($this->mPageStart != $this->mPageEnd) && ($this->mPageEnd < $this->mCount);
	}

	public function count() {
		return $this->mCount;
	}

	public function setCount($count) {
		$this->mCount = $count;
	}

	public function unreadCount() {
		return $this->mUnreadCount;
	}

	public function setUnreadCount($unreadCount) {
		$this->mUnreadCount = $unreadCount;
	}
}
?>