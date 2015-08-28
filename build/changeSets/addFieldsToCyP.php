<?php
class addFieldsToCyP extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'CobroPago';
			$module = Vtiger_Module::getInstance($modname);
                        $block = Vtiger_Block::getInstance('LBL_COBROPAGO_INFORMATION', $module);
                        $fld_ref = Vtiger_Field::getInstance('reference', $module);
                        $this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='V~O' WHERE fieldid={$fld_ref->id}");
                        $this->ExecuteQuery("UPDATE vtiger_field SET sequence=sequence+1 WHERE block={$block->id} AND sequence>1");
                        $field1 = new Vtiger_Field();
                        $field1->name = 'cyp_no';
                        $field1->label= 'CyP No';
                        $field1->column = 'cyp_no';
                        $field1->columntype = 'VARCHAR(50)';
                        $field1->sequence = 2;
                        $field1->uitype = 4;
                        $field1->typeofdata = 'V~M';
                        $field1->displaytype = 1;
                        $field1->presence = 0;
                        $block->addField($field1);
                        $fld_due = Vtiger_Field::getInstance('duedate', $module);
                        $qry = "SELECT sequence FROM vtiger_field WHERE fieldid={$fld_due->id}";
                        $res = $adb->query($qry);
                        $seq = $adb->query_result($res,0,'sequence');
                        $this->ExecuteQuery("UPDATE vtiger_field SET sequence=sequence+1 WHERE block={$block->id} AND sequence>$seq");
                        $field1 = new Vtiger_Field();
                        $field1->name = 'paymentdate';
                        $field1->label= 'PaymentDate';
                        $field1->column = 'paymentdate';
                        $field1->columntype = 'DATE';
                        $field1->sequence = $seq+1;
                        $field1->uitype = 5;
                        $field1->typeofdata = 'D~O';
                        $field1->displaytype = 1;
                        $field1->presence = 0;
                        $block->addField($field1);
                        
                        $focus = CRMEntity::getInstance($modname);
                        $focus->setModuleSeqNumber('configure',$modname,'PAY-','0000001');
                        $focus->updateMissingSeqNumber($modname);
                        $ins = "UPDATE vtiger_entityname SET fieldname=CONCAT(fieldname,',cyp_no') WHERE tabid={$module->id}";
                        $this->ExecuteQuery($ins);
			$this->sendMsg($ins);

                        $this->ExecuteQuery("UPDATE vtiger_cobropago SET paymentdate=duedate");

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		}
		$this->finishExecution();
	}
}

?>
