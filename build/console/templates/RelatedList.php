<?php
$module1= Vtiger_Module::getInstance('MODULE1');
$module2= Vtiger_Module::getInstance('MODULE2');
$relationLabel = 'LABEL';
$module1->setRelatedList($module2, $relationLabel, array(TYPE), 'FUNCTION');
