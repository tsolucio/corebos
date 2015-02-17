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
*************************************************************************************************/

class coreboscp_rest extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			foreach (glob('build/wsChanges/*.php',GLOB_BRACE) as $restOp) {
				include $restOp;
				if (!empty($operationInfo)) {
					ob_start();
					$rdo = registerWSAPI($operationInfo);
					$out = ob_get_contents();
					ob_end_clean();
					$this->sendMsg($out);
					if ($rdo)
						$this->sendMsg("Registered WS Operation: <b>".$operationInfo['name']."</b><br>");
					else
						$this->sendMsg("WS Operation: <b>".$operationInfo['name']."</b> already registered<br>");
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}