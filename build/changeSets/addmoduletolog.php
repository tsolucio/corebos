<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : LoggingConf
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/

class addmoduletolog extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			
                    
                    include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Event.php';
include_once('modules/LoggingConf/LoggingUtils.php');

global $adb;
 
$tabids=explode('-',  $_REQUEST['tabidvalues']);

foreach($tabids as $tabid)
{
$query=$adb->query("insert into vtiger_loggingconfiguration(tabid) values ($tabid)  ");
$moduleName=getTabname($tabid);
$moduleInstance = Vtiger_Module::getInstance($moduleName);
$moduleInstanceLog=  Vtiger_Module::getInstance('Entitylog');
$field7 = Vtiger_Field::getInstance("relatedto",$moduleInstanceLog);
$field7->setRelatedModules(Array($moduleName));

    Vtiger_Event::register($moduleInstance, 'vtiger.entity.beforesave', 'HistoryLogHandler', 'include/utils/HLogHandler.php');
    Vtiger_Event::register($moduleInstance, 'vtiger.entity.aftersave', 'HistoryLogHandler', 'include/utils/HLogHandler.php');

    $newdtid = $adb->getUniqueID("vtiger_relatedlists");
    if ($adb->pquery('insert into vtiger_relatedlists values (?,?,?,?,?,?,?,?)', array($newdtid, $moduleInstance->id, 0, 'get_log_history', 1, 'History Log', 0, ''))) {
        echo "Setting Related list ...DONE";
    } else {
        echo "Setting Related list ... NOT DONE";
    }

}
                    
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	
	
}