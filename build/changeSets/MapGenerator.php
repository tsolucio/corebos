<?php

/**
 * @Author: Edmond Kacaj
 * @Date:   2018-08-31 14:23:09
 * @Last Modified by:   Edmond Kacaj
 * @Last Modified time: 2018-08-31 14:23:25
 */
class MapGenerator extends cbupdaterWorker {
    
    function applyChange() {
        if ($this->hasError()) $this->sendError();
        if ($this->isApplied()) {
            $this->sendMsg('Changeset '.get_class($this).' already applied!');
        } else {
            $toinstall = array('MapGenerator');
            foreach ($toinstall as $module) {
                if ($this->isModuleInstalled($module)) {
                    vtlib_toggleModuleAccess($module,true);
                    $this->sendMsg("$module activated!");
                } else {
                    $this->installManifestModule($module);
                }
            }
            $this->sendMsg('Changeset '.get_class($this).' applied!');
            $this->markApplied();
        }
        $this->finishExecution();
    }
    
    function undoChange() {
        if ($this->hasError()) $this->sendError();
        if ($this->isApplied()) {
            vtlib_toggleModuleAccess('MapGenerator',false);
            $this->sendMsg('MapGenerator deactivated!');
            $this->markUndone(false);
            $this->sendMsg('Changeset '.get_class($this).' undone!');
        } else {
            $this->sendMsg('Changeset '.get_class($this).' not applied, it cannot be undone!');
        }
        $this->finishExecution();
    }
    
}