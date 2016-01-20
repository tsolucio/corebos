<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class HelpDeskStatusOnCalendar extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb,$log;
$its4you_calendar_modulestatus = array(
  array('module' => 'HelpDesk','status' => 'Planned','field' => 'ticketstatus','value' => 'Closed','operator' => 'n','glue' => ''),
  array('module' => 'HelpDesk','status' => 'Held','field' => 'ticketstatus','value' => 'Closed','operator' => 'e','glue' => ''),
  array('module' => 'HelpDesk','status' => 'Not Held','field' => 'ticketstatus','value' => 'Closed','operator' => 'n','glue' => ''),
);
$this->ExecuteQuery('CREATE TABLE IF NOT EXISTS `its4you_calendar_modulestatus` (
  `calmodstatus` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `field` varchar(50) NOT NULL,
  `value` varchar(250) NOT NULL,
  `operator` varchar(10) NOT NULL,
  `glue` varchar(3) NOT NULL,
  PRIMARY KEY (calmodstatus),
  index(`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

$ins = 'insert into its4you_calendar_modulestatus (module,status,field,value,operator,glue) values(?,?,?,?,?,?)';
foreach ($its4you_calendar_modulestatus as $record) {
	$this->ExecuteQuery($ins,$record);
}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}