<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

include_once 'modules/cbMap/cbMap.php';
include_once 'modules/Users/Users.php';

class createContacts2cbCalendarBusinessMap extends cbupdaterWorker {

	public function applyChange() {

		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			$module_name = 'cbMap';

			if ($this->isModuleInstalled($module_name)) {
				$focusnew = new cbMap();
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = "Contacts2cbCalendar";
				$focusnew->column_fields['maptype'] = "Mapping";
				$focusnew->column_fields['targetname'] = "Contacts";
				$focusnew->column_fields['content'] = "<map>
  <originmodule>
    <originname>Contacts</originname>
  </originmodule>
  <targetmodule>
    <targetname>cbCalendar</targetname>
  </targetmodule>
  <fields>
    <field>
      <fieldname>cto_id</fieldname>
      <Orgfields>
        <Orgfield>
          <OrgfieldName>record_id</OrgfieldName>
        </Orgfield>
      </Orgfields>
    </field>
    <field>
      <fieldname>rel_id</fieldname>
      <Orgfields>
        <Orgfield>
          <OrgfieldName>account_id</OrgfieldName>
        </Orgfield>
      </Orgfields>
    </field>
  </fields>
</map>";
				$focusnew->save($module_name);

				$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
				$this->sendMsg('A new Mapping Business Map has been created.');
				$this->markApplied();
			}
		}
		$this->finishExecution();
	}
}