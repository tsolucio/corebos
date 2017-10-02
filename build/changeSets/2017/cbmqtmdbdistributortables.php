<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class cbmqtmdbdistributortables extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery('CREATE TABLE `cbmqtm_config` (
  `cbmqtm_key` varchar(200) NOT NULL,
  `cbmqtm_value` varchar(500) NOT NULL,
  PRIMARY KEY (`cbmqtm_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;', array());
			$this->ExecuteQuery("INSERT INTO `cbmqtm_config` (`cbmqtm_key`, `cbmqtm_value`) VALUES
('cbmqtm_classfile', 'include/cbmqtm/cbmqtm_dbdistributor.php'),
('cbmqtm_classname', 'cbmqtm_dbdistributor');", array());
			$this->ExecuteQuery('CREATE TABLE `cb_messagequeue` (
 `idx` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 `channel` VARCHAR(200) NOT NULL ,
 `producer` VARCHAR(200) NOT NULL ,
 `consumer` VARCHAR(200) NOT NULL ,
 `type` VARCHAR(20) NOT NULL ,
 `share` VARCHAR(20) NOT NULL ,
 `sequence` INT NOT NULL ,
 `senton` DATETIME NOT NULL ,
 `expires` DATETIME NOT NULL ,
 `version` VARCHAR(20) NOT NULL ,
 `invalid` TINYINT NOT NULL ,
 `invalidreason` VARCHAR(500) NOT NULL ,
 `userid` INT NOT NULL ,
 `information` MEDIUMTEXT NOT NULL ,
 PRIMARY KEY (`idx`),
 INDEX `cbmqchannelseq` (`channel`),
 INDEX `cbmqproducer` (`producer`),
 INDEX `cbmqconsumer` (`consumer`),
 INDEX `cbmqexpires` (`expires`),
 INDEX `cbmquserid` (`userid`),
 INDEX `cbmqchannel` (`channel`, `sequence`)
) ENGINE = InnoDB;', array());
			$this->ExecuteQuery('CREATE TABLE `cb_mqsubscriptions` (
 `md5idx` CHAR(32) NOT NULL,
 `channel` VARCHAR(200) NOT NULL ,
 `producer` VARCHAR(200) NOT NULL ,
 `consumer` VARCHAR(200) NOT NULL ,
 `callback` VARCHAR(500) NOT NULL ,
 PRIMARY KEY (`md5idx`),
 INDEX `cbmqchannelseq` (`channel`),
 INDEX `cbmqproducer` (`producer`),
 INDEX `cbmqconsumer` (`consumer`)
) ENGINE = InnoDB;', array());
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
