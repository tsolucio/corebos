<?php

class createcontentjsonfield extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$modname = 'cbMap';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_CBMAP_INFORMATION', $module);
                        $field = Vtiger_Field::getInstance('contentjson',$module);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$fieldInstance = new Vtiger_Field();
				$fieldInstance->name = 'contentjson';
				$fieldInstance->label = 'ContentJson';
				$fieldInstance->columntype = 'text';
				$fieldInstance->uitype = 19;
				$fieldInstance->displaytype = 3;
				$fieldInstance->typeofdata = 'V~O';
				$block->addField($fieldInstance);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			// undo your magic here
			$moduleInstance=Vtiger_Module::getInstance('cbMap');
			$field = Vtiger_Field::getInstance('contentjson',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=1 where fieldid=?',array($field->id));
			}
			$this->sendMsg('Changeset '.get_class($this).' undone!');
			$this->markUndone();
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied!');
		}
		$this->finishExecution();
	}

}

?>