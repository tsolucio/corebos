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
 *  Module       : EntittyLog
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/
class HistoryLogHandler extends VTEventHandler {
  
  private $modulesRegistered;

  function setModulesRegistered($map)
  {
  $this->modulesRegistered=$map;
  }
  function handleEvent($eventName, $entityData) {
    global $log, $adb,$current_user,$app_strings;
    $userid=$current_user->id;
    $moduleName = $entityData->getModuleName();    
    $this->setModulesRegistered($this->getModulesFieldMap($moduleName));
    if (!isset($this->modulesRegistered[$moduleName])) {
      return;
    }
    //This block of code needs to be adapted : table_name, tableid, name, and fields you wish to be considered for logging
    $table = $this->modulesRegistered[$moduleName]['tablename'];
    $tableid = $table.'.'.$this->modulesRegistered[$moduleName]['primarykey'];
    $fields = $this->modulesRegistered[$moduleName]['fields'];
    //end of block code to be adapted
    $Id = $entityData->getId();
    $log->debug("Enter Handler for beforesave event...");
    
    if($eventName == 'vtiger.entity.beforesave')
    {
      $log->debug("Enter Handler for beforesave event...");
      if(!empty($Id)) {
        $listquery = getListQuery($moduleName,"and ".$tableid."=".$Id)  ;
     
        $query=$adb->query($listquery);
        if($adb->num_rows($query) > 0) {
          for  ($i=0;$i<count($fields);$i++)
          {
            $entityData->old[$i]=$adb->query_result($query,0,$fields[$i]);
          }
        }
      }
      $log->debug("Exit Handler for beforesave event...");
    }
    if($eventName == 'vtiger.entity.aftersave') {
      
      $log->debug("Enter Handler for aftersave event...");
      
      $tabid =$adb->query_result($adb->pquery("SELECT tabid FROM vtiger_entityname where modulename= ?",array($moduleName)),0);
      $listquery = getListQuery($moduleName,"and ".$tableid."=".$Id)  ;
      $query=$adb->query($listquery);
      
      if($adb->num_rows($query) > 0) {
        for  ($i=0;$i<count($fields);$i++)
        {
          $news[$i]=$adb->query_result($query,0,$fields[$i]);         
        }
      }
      $act = "";
      $act1='';
      for ($i=0;$i<count($fields);$i++)
       {
        if($news[$i]!=$entityData->old[$i]) {         
          $act='fieldname='. $fields[$i]. ';oldvalue='. $entityData->old[$i].';newvalue='. $news[$i].";";
          $dt=date("Y-m-d H:i:s");     
      if(!empty($act)) {         
          require_once('modules/Entitylog/Entitylog.php');
        require_once("data/CRMEntity.php" );
          $focus=new Entitylog();
          $focus->column_fields['entitylogname']=$app_strings['LBL_CHANGES_RECORD'].' '.$Id.' '.$app_strings['LBL_OF_MODULE'].' '.$moduleName.' '.$app_strings['LBL_AT'].' '.$dt;
          $focus->column_fields['user']=$userid;
          $focus->column_fields['relatedto']=$entityData->getId();
          $focus->column_fields['tabid']=$tabid;
          $focus->column_fields['finalstate']=$act;
          $focus->saveentity("Entitylog");
      }
      }
      }
      $log->debug("Exit aftersave event...");
    }
    $log->debug("Exiting Handler for module...".$moduleName);
  }

  function getModulesFieldMap($module='')
  {
   include_once('modules/LoggingConf/LoggingUtils.php');
   require_once('include/utils/UserInfoUtil.php');
   require_once('include/utils/utils.php');

   $tabid=getTabid($module);
   $isModule=isModuleLog($tabid);
   if($module=='')
   {
   $allLoggingModules=array_values(getLoggingModules());
   
   foreach($allLoggingModules as $module)
   {
       $moduleInstance=Vtiger_Module::getClassInstance($module);
       $table=$moduleInstance->table_name;
       $primary_key=$moduleInstance->table_index;
       $tabid=getTabid($module);
       $map=array();
       $fields=array();
       $fields=array_values(getModuleLogFieldList($tabid));

       $map[$module]=array(
           'tablename'=>$table,
           'primarykey'=>$primary_key,
           'fields'=>$fields,
       );  
   }   
   }
   elseif($isModule>0)
   {
       $moduleInstance=Vtiger_Module::getClassInstance($module);
       $table=$moduleInstance->table_name;
       $primary_key=$moduleInstance->table_index;     
       $map=array();
       $fields=array();
       $fields=array_values(getModuleLogFieldListNames($tabid));

       $map[$module]=array(
           'tablename'=>$table,
           'primarykey'=>$primary_key,
           'fields'=>$fields,
       );
   }
   return $map;
  }
}

?>
