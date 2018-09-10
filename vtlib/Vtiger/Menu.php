<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/Utils.php';

/**
 * Provides API to work with application menu
 * @package vtlib
 */
class Vtiger_Menu {

	public $id = false;
	public $label = false;
	public $sequence = false;
	public $visible = 0;
	public $menuid = false;
	public $menulabel = false;
	public $menusequence = false;
	public $menuvisible = 0;
	public $allmenuinfo = array();

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Initialize this instance
	 * @param array Map
	 */
	public function initialize($valuemap) {
		$this->id       = $valuemap['parenttabid'];
		$this->label    = $valuemap['parenttab_label'];
		$this->sequence = $valuemap['sequence'];
		$this->visible  = $valuemap['visible'];
		$this->menuid       = $valuemap['menuid'];
		$this->menulabel    = $valuemap['menulabel'];
		$this->menusequence = $valuemap['menusequence'];
		$this->menuvisible  = $valuemap['menuvisible'];
		$this->allmenuinfo  = array(
			'id'       => $valuemap['parenttabid'],
			'label'    => $valuemap['parenttab_label'],
			'sequence' => $valuemap['sequence'],
			'visible'  => $valuemap['visible'],
			'menuid'       => $valuemap['menuid'],
			'menulabel'    => $valuemap['menulabel'],
			'menusequence' => $valuemap['menusequence'],
			'menuvisible'  => $valuemap['menuvisible'],
		);
	}

	/**
	 * Get relation sequence to use
	 * @access private
	 */
	private function __getNextRelSequence() {
		global $adb;
		$result = $adb->pquery('SELECT MAX(sequence) AS max_seq FROM vtiger_parenttabrel WHERE parenttabid=?', array($this->id));
		$maxseq = $adb->query_result($result, 0, 'max_seq');
		return ++$maxseq;
	}

	/**
	 * Add module to this menu instance
	 * @param Vtiger_Module Instance of the module
	 */
	public function addModule($moduleInstance) {
		if ($this->id) {
			global $adb;
			$relsequence = $this->__getNextRelSequence();
			$adb->pquery('INSERT INTO vtiger_parenttabrel (parenttabid,tabid,sequence) VALUES(?,?,?)', array($this->id, $moduleInstance->id, $relsequence));
			$pmenuidrs = $adb->pquery('select max(mseq) from vtiger_evvtmenu where mparent=?', array($this->menuid));
			$mseq = $adb->query_result($pmenuidrs, 0, 0) + 1;
			$adb->pquery(
				'insert into vtiger_evvtmenu (mtype,mvalue,mlabel,mparent,mseq,mvisible,mpermission) values (?,?,?,?,?,?,?)',
				array('module',$moduleInstance->name,$moduleInstance->name,$this->menuid,$mseq,1,'')
			);
			self::log("Added {$moduleInstance->name} to menu {$this->label} ... DONE");
		} else {
			self::log("Menu could not be found!");
		}
	}

	/**
	 * Remove module from this menu instance.
	 * @param Vtiger_Module Instance of the module
	 */
	public function removeModule($moduleInstance) {
		if (empty($moduleInstance) || empty($moduleInstance)) {
			self::log("Module instance is not set!");
			return;
		}
		if ($this->id) {
			global $adb;
			$adb->pquery('DELETE FROM vtiger_parenttabrel WHERE parenttabid = ? AND tabid = ?', array($this->id, $moduleInstance->id));
			$adb->pquery("DELETE FROM vtiger_evvtmenu WHERE mparent=? AND mtype='module' AND mvalue=?", array($this->menuid,$moduleInstance->name));
			self::log("Removed {$moduleInstance->name} from menu {$this->label} ... DONE");
		} else {
			self::log("Menu could not be found!");
		}
	}

	/**
	 * Detach module from menu
	 * @param Vtiger_Module Instance of the module
	 */
	public static function detachModule($moduleInstance) {
		global $adb;
		$adb->pquery("DELETE FROM vtiger_parenttabrel WHERE tabid=?", array($moduleInstance->id));
		$adb->pquery("DELETE FROM vtiger_evvtmenu WHERE mtype='module' and mvalue=?", array($moduleInstance->name));
		self::log("Detaching from menu ... DONE");
	}

	/**
	 * Get instance of menu by label
	 * @param String Menu label
	 */
	public static function getInstance($value) {
		global $adb;
		$query = false;
		$instance = false;
		$adb->query("CREATE TABLE IF NOT EXISTS `vtiger_evvtmenu` (`evvtmenuid` int(11) NOT NULL AUTO_INCREMENT,
			`mtype` varchar(25) NOT NULL,
			`mvalue` varchar(200) NOT NULL,
			`mlabel` varchar(200) NOT NULL,
			`mparent` int(11) NOT NULL,
			`mseq` smallint(6) NOT NULL,
			`mvisible` tinyint(4) NOT NULL,
			`mpermission` varchar(250) NOT NULL,
			PRIMARY KEY (`evvtmenuid`),
			KEY `mparent` (`mparent`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
		if (Vtiger_Utils::isNumber($value)) {
			$query = "SELECT * FROM vtiger_parenttab WHERE parenttabid=?";
			$querymenu = "SELECT * FROM vtiger_evvtmenu WHERE evvtmenuid=? and mtype='menu'";
		} else {
			$query = "SELECT * FROM vtiger_parenttab WHERE parenttab_label=?";
			$querymenu = "SELECT * FROM vtiger_evvtmenu WHERE mvalue=? and mtype='menu'";
		}
		$result = $adb->pquery($query, array($value));
		if ($result && $adb->num_rows($result)) {
			$mnuinfo = $adb->fetch_array($result);
			$plabel = $mnuinfo['parenttab_label'];
			$rsmnu = $adb->pquery('select * from vtiger_evvtmenu where mparent=0 and mlabel=?', array($plabel));
			if (!($rsmnu && $adb->num_rows($rsmnu)>0)) {
				$rsmnu = $adb->query("select * from vtiger_evvtmenu where mparent=0 and mtype='menu' limit 1");
			}
			$mnu = $adb->fetch_array($rsmnu);
			$mnuinfo = array_merge($mnuinfo, array(
				'menuid' => $mnu['evvtmenuid'],
				'menulabel' => $plabel,
				'menusequence' => $mnu['mseq'],
				'menuvisible' => $mnu['mvisible'],
			));
			$instance = new self();
			$instance->initialize($mnuinfo);
		} else {
			$rsmnu = $adb->pquery($querymenu, array($value));
			if ($rsmnu && $adb->num_rows($rsmnu)>0) {
				$mnu = $adb->fetch_array($rsmnu);
				$mnuinfo = array(
					'parenttabid' => (int)$mnu['evvtmenuid'],
					'menuid' => (int)$mnu['evvtmenuid'],
					'parenttab_label' => $mnu['mlabel'],
					'menulabel' => $mnu['mlabel'],
					'sequence' => (int)$mnu['mseq'],
					'menusequence' => (int)$mnu['mseq'],
					'visible' => 0,
					'menuvisible' => 1,
				);
				$instance = new self();
				$instance->initialize($mnuinfo);
			}
		}
		return $instance;
	}

	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 */
	public static function log($message, $delim = true) {
		Vtiger_Utils::Log($message, $delim);
	}

	/**
	 * @deprecated
	 */
	public static function syncfile() {
	}
}
?>
