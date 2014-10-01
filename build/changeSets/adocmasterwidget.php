<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class adocmasterwidget extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
               echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>coreBOS Utility loader</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style=\'color:red;float:right;margin-right:30px;\'><h2>Proud member of the <a href=\'http://corebos.org\'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">';   
      $Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Module.php');
$modname = 'Adocmaster';
$module = Vtiger_Module::getInstance($modname);

if($module) {
	$module->addLink('DETAILVIEWWIDGET', 'DetailViewAdocdetailng', "block://Adocdetailng:modules/Adocdetail/Adocdetailng.php");
} else {
	echo "<b>Failed to find $modname module.</b><br>";
}

echo '</body></html>';
 

	}
          $this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		$this->finishExecution();
        }
        function undoChange() {
		if ($this->hasError()) $this->sendError();
		$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		$this->finishExecution();
	}
        }
?>

