<?php
class changeSignatureUitype extends cbupdaterWorker {
	function applyChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$moduleInstance = Vtiger_Module::getInstance('Users');
			$block = Vtiger_Block::getInstance('LBL_SIGNATURE', $moduleInstance);
			if (!$block) {
				$block = new Vtiger_Block();
				$block->label = 'LBL_SIGNATURE';
				$block->sequence = 2;
				$moduleInstance->addBlock($block);
            }
            $block = Vtiger_Block::getInstance('LBL_SIGNATURE', $moduleInstance);
            $this->ExecuteQuery("UPDATE vtiger_field SET uitype = '19', block = $block->id  WHERE fieldname = 'signature' and uitype = '21' and tablename = 'vtiger_users'");
            $this->sendMsg('Changeset '.get_class($this).' applied!');
            $this->markApplied();
        }
		$this->finishExecution();
	}
}