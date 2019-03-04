<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Settings/MailScanner/core/MailScannerAction.php';

/**
 * Scanner Rule
 */
class Vtiger_MailScannerRule {
	// id of this instance
	public $ruleid    = false;
	// scanner to which this rule is linked
	public $scannerid = false;
	// from address criteria
	public $fromaddress= false;
	// to address criteria
	public $toaddress  = false;
	// subject criteria operator
	public $subjectop = false;
	// subject criteria
	public $subject   = false;
	// body criteria operator
	public $bodyop    = false;
	// body criteria
	public $body      = false;
	// order of this rule
	public $sequence  = false;
	// is this action valid
	public $isvalid   = false;
	// match criteria ALL or ANY
	public $matchusing = false;
	// Assign (Ticket) to user or groups
	public $assign_to = false;
	public $assign_to_type = false;
	public $assign_to_name = false;

	// associated actions for this rule
	public $actions  = false;
	// TODO we are restricting one action for one rule right now
	public $useaction= false;

	/** DEBUG functionality **/
	public $debug     = false;
	private function log($message) {
		global $log;
		if ($log && $this->debug) {
			$log->debug($message);
		} elseif ($this->debug) {
			echo "$message\n";
		}
	}

	/**
	 * Constructor
	 */
	public function __construct($forruleid) {
		$this->initialize($forruleid);
	}

	/**
	 * String representation of this instance
	 */
	public function __toString() {
		$tostring = '';
		$tostring .= "FROM $this->fromaddress, TO $this->toaddress";
		$tostring .= ",SUBJECT $this->subjectop $this->subject, BODY $this->bodyop $this->body, MATCH USING, $this->matchusing";
		return $tostring;
	}

	/**
	 * Initialize this instance
	 */
	public function initialize($forruleid) {
		global $adb;
		$result = $adb->pquery('SELECT * FROM vtiger_mailscanner_rules WHERE ruleid=? ORDER BY sequence', array($forruleid));

		if ($adb->num_rows($result)) {
			$this->ruleid     = $adb->query_result($result, 0, 'ruleid');
			$this->scannerid  = $adb->query_result($result, 0, 'scannerid');
			$this->fromaddress= $adb->query_result($result, 0, 'fromaddress');
			$this->toaddress  = $adb->query_result($result, 0, 'toaddress');
			$this->subjectop  = $adb->query_result($result, 0, 'subjectop');
			$this->subject    = $adb->query_result($result, 0, 'subject');
			$this->bodyop     = $adb->query_result($result, 0, 'bodyop');
			$this->body       = $adb->query_result($result, 0, 'body');
			$this->sequence   = $adb->query_result($result, 0, 'sequence');
			$this->matchusing = $adb->query_result($result, 0, 'matchusing');
			$this->isvalid    = true;
			//User | Group to assign
			$this->assign_to  = $adb->query_result($result, 0, 'assign_to');
			$result = $adb->pquery('SELECT * from vtiger_users where id = ?', array($this->assign_to));
			if ($adb->num_rows($result) > 0) {
				$this->assign_to_type = 'U';
				$this->assign_to_name = $adb->query_result($result, 0, 'first_name').' '.$adb->query_result($result, 0, 'last_name');
			} else {
				$this->assign_to_type = 'G';
				$result = $adb->pquery('SELECT * from vtiger_groups where groupid = ?', array($this->assign_to));
				$this->assign_to_name = $adb->query_result($result, 0, 'groupname');
			}

			$this->initializeActions();
			// At present we support only one action for a rule
			if (!empty($this->actions)) {
				$this->useaction = $this->actions[0];
			}
		}
	}

	/**
	 * Initialize the actions
	 */
	public function initializeActions() {
		global $adb;
		if ($this->ruleid) {
			$this->actions = array();
			$actionres = $adb->pquery('SELECT actionid FROM vtiger_mailscanner_ruleactions WHERE ruleid=?', array($this->ruleid));
			$actioncount = $adb->num_rows($actionres);
			if ($actioncount) {
				for ($index = 0; $index < $actioncount; ++$index) {
					$actionid = $adb->query_result($actionres, $index, 'actionid');
					$ruleaction = new Vtiger_MailScannerAction($actionid);
					$ruleaction->debug = $this->debug;
					$this->actions[] = $ruleaction;
				}
			}
		}
	}

	/**
	 * Is body rule defined?
	 */
	public function hasBodyRule() {
		return (!empty($this->bodyop));
	}

	/**
	 * Check if the rule criteria is matching
	 */
	public function isMatching($matchfound1, $matchfound2 = null) {
		if ($matchfound2 === null) {
			return $matchfound1;
		}

		if ($this->matchusing== 'AND') {
			return ($matchfound1 && $matchfound2);
		}
		if ($this->matchusing== 'OR') {
			return ($matchfound1 || $matchfound2);
		}
		return false;
	}

	/**
	 * Apply all the criteria.
	 * @param $mailrecord
	 * @param $includingBody
	 * @returns false if not match is found or else all matching result found
	 */
	public function applyAll($mailrecord, $includingBody = true) {
		$matchresults = array();
		$matchfound = null;

		if ($this->hasACondition()) {
			$subrules = array('FROM', 'TO', 'SUBJECT', 'BODY');
			foreach ($subrules as $subrule) {
				// Body rule could be defered later to improve performance
				// in that case skip it.
				if ($subrule == 'BODY' && !$includingBody) {
					continue;
				}

				$checkmatch = $this->apply($subrule, $mailrecord);
				$matchfound = $this->isMatching($checkmatch, $matchfound);
				// Collect matching result array
				if ($matchfound && is_array($checkmatch)) {
					$matchresults[] = $checkmatch;
				}
				if ($matchfound && $this->matchusing == 'OR') {
					break;
				}
			}
		} else {
			$matchfound = false;
			if ($this->matchusing == 'OR') {
				$matchfound = true;
				$matchresults[] = $this->__CreateMatchResult('BLANK', '', '', '');
			}
		}
		return ($matchfound)? $matchresults : false;
	}

	/**
	 * Check if at least one condition is set for this rule.
	 */
	public function hasACondition() {
		$hasFromAddress = $this->fromaddress? true: false;
		$hasToAddress   = $this->toaddress? true : false;
		$hasSubjectOp   = $this->subjectop? true : false;
		$hasBodyOp      = $this->bodyop? true : false;
		return ($hasFromAddress || $hasToAddress || $hasSubjectOp || $hasBodyOp);
	}

	/**
	 * Apply required condition on the mail record.
	 */
	public function apply($subrule, $mailrecord) {
		$matchfound = false;
		if ($this->isvalid) {
			switch (strtoupper($subrule)) {
				case 'FROM':
					if ($this->fromaddress) {
						$matchfound = $this->find($subrule, 'Contains', $mailrecord->_from[0], $this->fromaddress);
					} else {
						$matchfound = $this->__CreateDefaultMatchResult($subrule);
					}
					break;
				case 'TO':
					if ($this->toaddress) {
						foreach ($mailrecord->_to as $toemail) {
							$matchfound = $this->find($subrule, 'Contains', $toemail, $this->toaddress);
							if ($matchfound) {
								break;
							}
						}
					} else {
						$matchfound = $this->__CreateDefaultMatchResult($subrule);
					}
					break;
				case 'SUBJECT':
					if ($this->subjectop) {
						$matchfound = $this->find($subrule, $this->subjectop, $mailrecord->_subject, $this->subject);
					} else {
						$matchfound = $this->__CreateDefaultMatchResult($subrule);
					}
					break;
				case 'BODY':
					if ($this->bodyop) {
						$matchfound = $this->find($subrule, $this->bodyop, $mailrecord->_body, $this->body);
					} else {
						$matchfound = $this->__CreateDefaultMatchResult($subrule);
					}
					break;
			}
		}
		return $matchfound;
	}

	/**
	 * Find if the rule matches based on condition and parameters
	 */
	public function find($subrule, $condition, $input, $searchfor) {
		if (!$input) {
			return false;
		}

		$matchfound = false;
		$matches = false;

		switch ($condition) {
			case 'Contains':
				$matchfound = stripos($input, $searchfor);
				$matchfound = ($matchfound !== false);
				$matches = $searchfor;
				break;
			case 'Not Contains':
				$matchfound = stripos($input, $searchfor);
				$matchfound = ($matchfound === false);
				$matches = $searchfor;
				break;
			case 'Equals':
				$matchfound = strcasecmp($input, $searchfor);
				$matchfound = ($matchfound === 0);
				$matches = $searchfor;
				break;
			case 'Not Equals':
				$matchfound = strcasecmp($input, $searchfor);
				$matchfound = ($matchfound !== 0);
				$matches = $searchfor;
				break;
			case 'Begins With':
				$matchfound = stripos($input, $searchfor);
				$matchfound = ($matchfound === 0);
				$matches = $searchfor;
				break;
			case 'Ends With':
				$matchfound = strripos($input, $searchfor);
				$matchfound = ($matchfound === strlen($input)-strlen($searchfor));
				$matches = $searchfor;
				break;
			case 'Regex':
				$regmatches = array();
				$matchfound = false;
				$searchfor  = str_replace('/', '\/', $searchfor);
				if (preg_match("/$searchfor/i", $input, $regmatches)) {
					// Pick the last matching group
					$matches = $regmatches[count($regmatches)-1];
					$matchfound = true;
				}
				break;
		}
		if ($matchfound) {
			$matchfound = $this->__CreateMatchResult($subrule, $condition, $searchfor, $matches);
		}
		return $matchfound;
	}

	/**
	 * Create matching result for the subrule.
	 */
	private function __CreateMatchResult($subrule, $condition, $searchfor, $matches) {
		return array( 'subrule' => $subrule, 'condition' => $condition, 'searchfor' => $searchfor, 'matches' => $matches);
	}

	/**
	 * Create default success matching result
	 */
	private function __CreateDefaultMatchResult($subrule) {
		if ($this->matchusing == 'OR') {
			return false;
		}
		if ($this->matchusing == 'AND') {
			return $this->__CreateMatchResult($subrule, 'Contains', '', '');
		}
	}

	/**
	 * Detect if the rule match result has Regex condition
	 * @param $matchresult result of apply obtained earlier
	 * @returns matchinfo if Regex match is found, false otherwise
	 */
	public function hasRegexMatch($matchresult) {
		foreach ($matchresult as $matchinfo) {
			$match_condition = $matchinfo['condition'];
			$match_string = $matchinfo['matches'];
			if ($match_condition == 'Regex' && $match_string) {
				return $matchinfo;
			}
		}
		return false;
	}

	/**
	 * Swap (reset) sequence of two rules.
	 */
	public static function resetSequence($ruleid1, $ruleid2) {
		global $adb;
		$ruleresult = $adb->pquery('SELECT ruleid, sequence FROM vtiger_mailscanner_rules WHERE ruleid = ? or ruleid = ?', array($ruleid1, $ruleid2));
		$rule_partinfo = array();
		if ($adb->num_rows($ruleresult) != 2) {
			return false;
		} else {
			$rule_partinfo[$adb->query_result($ruleresult, 0, 'ruleid')] = $adb->query_result($ruleresult, 0, 'sequence');
			$rule_partinfo[$adb->query_result($ruleresult, 1, 'ruleid')] = $adb->query_result($ruleresult, 1, 'sequence');
			$adb->pquery('UPDATE vtiger_mailscanner_rules SET sequence = ? WHERE ruleid = ?', array($rule_partinfo[$ruleid2], $ruleid1));
			$adb->pquery('UPDATE vtiger_mailscanner_rules SET sequence = ? WHERE ruleid = ?', array($rule_partinfo[$ruleid1], $ruleid2));
		}
	}

	/**
	 * Update rule information in database.
	 */
	public function update() {
		global $adb;
		if ($this->ruleid) {
			$adb->pquery(
				'UPDATE vtiger_mailscanner_rules SET scannerid=?,fromaddress=?,toaddress=?,subjectop=?,subject=?,bodyop=?,body=?,matchusing=?,assign_to=? WHERE ruleid=?',
				array($this->scannerid, $this->fromaddress, $this->toaddress, $this->subjectop, $this->subject,
				$this->bodyop, $this->body,$this->matchusing,$this->assign_to,$this->ruleid)
			);
		} else {
			$this->sequence = $this->__nextsequence();
			$adb->pquery(
				'INSERT INTO vtiger_mailscanner_rules(scannerid,fromaddress,toaddress,subjectop,subject,bodyop,body,matchusing,sequence,assign_to)
				VALUES(?,?,?,?,?,?,?,?,?,?)',
				array($this->scannerid,$this->fromaddress,$this->toaddress,$this->subjectop,$this->subject,
				$this->bodyop,
				$this->body,
				$this->matchusing,
				$this->sequence,
				$this->assign_to)
			);
			$this->ruleid = $adb->database->Insert_ID();
		}
	}

	/**
	 * Get next sequence to use
	 */
	private function __nextsequence() {
		global $adb;
		$seqres = $adb->pquery('SELECT max(sequence) AS max_sequence FROM vtiger_mailscanner_rules', array());
		$maxsequence = 0;
		if ($adb->num_rows($seqres)) {
			$maxsequence = $adb->query_result($seqres, 0, 'max_sequence');
		}
		++$maxsequence;
		return $maxsequence;
	}

	/**
	 * Delete the rule and associated information.
	 */
	public function delete() {
		global $adb;
		// Delete dependencies
		if (!empty($this->actions)) {
			foreach ($this->actions as $action) {
				$action->delete();
			}
		}
		if ($this->ruleid) {
			$adb->pquery('DELETE FROM vtiger_mailscanner_ruleactions WHERE ruleid = ?', array($this->ruleid));
			$adb->pquery('DELETE FROM vtiger_mailscanner_rules WHERE ruleid=?', array($this->ruleid));
		}
	}

	/**
	 * Update action linked to the rule.
	 */
	public function updateAction($actionid, $actiontext) {
		$action = $this->useaction;

		if ($actionid != '' && $actiontext == '') {
			if ($action) {
				$action->delete();
			}
		} else {
			if ($actionid == '') {
				$action = new Vtiger_MailScannerAction($actionid);
			}
			$action->scannerid = $this->scannerid;
			$action->update($this->ruleid, $actiontext);
		}
	}

	/**
	 * Take action on mail record
	 */
	public function takeAction($mailscanner, $mailrecord, $matchresult) {
		if (empty($this->actions)) {
			return false;
		}

		$action = $this->useaction; // Action is limited to One right now
		return $action->apply($mailscanner, $mailrecord, $this, $matchresult);
	}
}
?>
