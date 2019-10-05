<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/

class Mobile {
	/**
	 * Detect if request is from IPhone
	 */
	public static function isSafari() {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$ua = $_SERVER['HTTP_USER_AGENT'];
			if (preg_match("/safari/i", $ua)) {
				return true;
			}
		}
		return false;
	}

	public static function templatePath($filename) {
		return vtlib_getModuleTemplate('Mobile', "generic/$filename");
	}

	public static function config($key, $defvalue = false) {
		// Defined in the configuration file
		global $Module_crmtogo_Configuration;
		if (isset($Module_crmtogo_Configuration) && isset($Module_crmtogo_Configuration[$key])) {
			return $Module_crmtogo_Configuration[$key];
		}
		return $defvalue;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			$db = PearDatabase::getInstance();
			$db->pquery(
				'INSERT INTO `berli_crmtogo_defaults` (`fetch_limit`, `crmtogo_lang`, `defaulttheme`, `crm_version`) VALUES (?, ?, ?, ?)',
				array('99','en_us', 'b', '6.3')
			);
			$db->pquery(
				'INSERT INTO `berli_crmtogo_config` (`crmtogouser`, `navi_limit`, `theme_color`, `compact_cal`) VALUES (?, ?, ?, ?)',
				array('1','25', 'b', '1')
			);
			$seq = 0;
			$supported_module = array(
				'Contacts','Accounts','Leads','Calendar','Potentials','HelpDesk','Vendors','Assets','Faq','Documents',
				'Quotes','SalesOrder','Invoice','Products','Project','ProjectMilestone','ProjectTask','Events'
			);
			foreach ($supported_module as $mdulename) {
				$db->pquery(
					'INSERT INTO `berli_crmtogo_modules` (`crmtogo_user`, `crmtogo_module`, `crmtogo_active`, `order_num`) VALUES (?, ?, ?, ?)',
					array('1',$mdulename, '1', $seq)
				);
				$seq = $seq + 1;
			}
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			$db = PearDatabase::getInstance();
			$db->pquery("CREATE TABLE IF NOT EXISTS `berli_crmtogo_defaults` (
				  `fetch_limit` int(3) NOT NULL,
				  `crmtogo_lang` varchar(5) NOT NULL,
				  `defaulttheme` varchar(1) NOT NULL,
				  `crm_version` varchar(5) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8", array());
			$db->pquery("CREATE TABLE IF NOT EXISTS `berli_crmtogo_config` (
				  `crmtogouser` int(19) NOT NULL,
				  `navi_limit` int(3) NOT NULL,
				  `theme_color` varchar(1) NOT NULL,
				  `compact_cal` int(1) NOT NULL,
				   PRIMARY KEY  (`crmtogouser`),
				   CONSTRAINT `fk_1_berli_crmtogo_config` FOREIGN KEY (`crmtogouser`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8", array());
			$db->pquery("CREATE TABLE IF NOT EXISTS `berli_crmtogo_modules` (
				  `crmtogo_user` int(19) NOT NULL,
				  `crmtogo_module` varchar(50) NOT NULL,
				  `crmtogo_active` int(1) NOT NULL DEFAULT '1',
				  `order_num` int(3) NOT NULL,
				   CONSTRAINT `fk_1_berli_crmtogo_modules` FOREIGN KEY (`crmtogo_user`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8", array());
		} elseif ($event_type == 'module.postupdate') {
			$db = PearDatabase::getInstance();
			$res = $db->pquery("SELECT * FROM berli_crmtogo_config WHERE crmtogouser =?", array('1'));
			if ($db->num_rows($res) ==0) {
				$db->pquery(
					'INSERT INTO `berli_crmtogo_defaults` (`fetch_limit`, `crmtogo_lang`, `defaulttheme`, `crm_version`) VALUES (?, ?, ?, ?)',
					array('99','en_us', 'b', '6.3')
				);
				$db->pquery(
					'INSERT INTO `berli_crmtogo_config` (`crmtogouser`, `navi_limit`, `theme_color`, `compact_cal`) VALUES (?, ?, ?, ?)',
					array('1','25', 'b', '1')
				);
				$seq = 0;
				$supported_module = array(
					'Contacts','Accounts','Leads','cbCalendar','Potentials','HelpDesk','Vendors','Assets','Faq','Documents',
					'Quotes','SalesOrder','Invoice','Products','Project','ProjectMilestone','ProjectTask'
				);
				foreach ($supported_module as $mdulename) {
					$db->pquery(
						'INSERT INTO `berli_crmtogo_modules` (`crmtogo_user`, `crmtogo_module`, `crmtogo_active`, `order_num`) VALUES (?, ?, ?, ?)',
						array('1', $mdulename, '1', $seq)
					);
					$seq = $seq + 1;
				}
			}
		}
	}
}
?>