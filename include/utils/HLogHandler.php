<?php
/*+*******************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
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
    $log->debug('unepotani1 '.$Id);
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
               $log->debug('unepotani4 '.$entityData->old[$i].' '.$fields[$i]);
          }
        }
      }
      $log->debug("Exit Handler for beforesave event...");
    }
    if($eventName == 'vtiger.entity.aftersave') {
      
      $log->debug("Enter Handler for aftersave event...");
      
      $tabid =$adb->query_result($adb->pquery("SELECT tabid FROM vtiger_entityname where modulename= ?",array($moduleName)),0);
	$log->debug('jamune'.$tabid);
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
      $log->debug('unepo '.count($fields));
      for ($i=0;$i<count($fields);$i++)
       {
        if($news[$i]!=$entityData->old[$i]) {         
          $act='fieldname='. $fields[$i]. ';oldvalue='. $entityData->old[$i].';newvalue='. $news[$i].";";
             
        
       
        
      
     // $log->debug('drivalda2 '.$act);
      $dt=date("Y-m-d H:i:s");     
      if(!empty($act)) {         
          require_once('modules/Entitylog/Entitylog.php');
        require_once("data/CRMEntity.php" );
          $focus=new Entitylog();
          $focus->column_fields['entitylogname']=$app_strings['LBL_CHANGES_RECORD'].' '.$Id.' '.$app_strings['LBL_OF_MODULE'].' '.$moduleName.' '.$app_strings['LBL_AT'].' '.$dt;
         // $focus->column_fields['assigned_user_id']=$userid;
          $focus->column_fields['user']=$userid;
          $focus->column_fields['relatedto']=$entityData->getId();
          $log->debug('prapeune'.$tabid);
          $focus->column_fields['tabid']=$tabid;
          $focus->column_fields['finalstate']=$act;
//          if($moduleName=='Stock'){
//             $index= array_search("locationid", $fields);
//           
//             $focus->column_fields['locatorfrom']=$entityData->old[$index];
//             $focus->column_fields['locatorto']=$news[$index];
//             $log->debug('ketu jemi '.$index." ".$entityData->old[$index]." ".$news[$index]);
//          }
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
   global $log;

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
