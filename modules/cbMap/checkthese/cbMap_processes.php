<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once('modules/cbMap/crXml.php');
include_once('include/utils/VTCacheUtils.php');
class cbMap extends CRMEntity {
       function getMapTargetModule(){
           $map=htmlspecialchars_decode($this->column_fields['content']);
           $x = new crXml();
           $x->loadXML($map);
           return (string)$x->map->targetmodule[0]->targetname;
        }
       function getMapOriginModule(){
           $map=htmlspecialchars_decode($this->column_fields['content']);
           $x = new crXml();
           $x->loadXML($map);
           return (string)$x->map->originmodule[0]->originname;
        }
         function getMapOriginTable(){
           $map=htmlspecialchars_decode($this->column_fields['content']);
           $x = new crXml();
           $x->loadXML($map);
           return (string)$x->map->table[0]->tablename;
        }
        function getMapPointingFieldUpdate(){
           $map=htmlspecialchars_decode($this->column_fields['content']);
           $x = new crXml();
           $x->loadXML($map);
           return (string)$x->map->pointingfield[0]->originname;
        }
       function getMapTargetFields(){
            $map=htmlspecialchars_decode($this->column_fields['content']);
            $x = new crXml();
            $x->loadXML($map);
            $target_fields=array();
            $index=0;
            foreach($x->map->fields[0] as $k=>$v) {
            $fieldname=  (string)$v->fieldname;
            $allmergeFields=array();
			if(!empty($v->value)){
            	$target_fields[$fieldname]["value"] = (string) $v->value;
            }
            foreach($v->Orgfields[0]->Orgfield as $key=>$value) {
              // echo $fk;
               if($key=='OrgfieldName')
                  $allmergeFields[]=(string)$value;
               if($key=='delimiter')
                   $target_fields[$fieldname]['delimiter']=(string)$value;
            }
            $target_fields[$fieldname]['merge']=$allmergeFields;
           }
           return $target_fields;
        }
         function readMappingType() {
        $map = htmlspecialchars_decode($this->column_fields['content']);
        $x = new crXml();
        $x->loadXML($map);
        $target_fields = array();
        foreach ($x->map->fields[0] as $k => $v) {
            $fieldname = (string) $v->fieldname;
            $allmergeFields = array();
            if(!empty($v->value)){
            $target_fields[$fieldname] = array("value" => $v->value);
            }
            elseif(!empty($v->Orgfields[0]->Orgfield) && isset($v->Orgfields[0]->Orgfield) ){
            foreach ($v->Orgfields[0]->Orgfield as $key => $value) {
                if ($key == 'OrgfieldName') {
                    $allmergeFields[] = (string) $value;
                }
                if ($key == 'delimiter') {
                    $delimiter = (string) $value;
                }
                if (empty($delimiter))
                    $delimiter = "";
            }
            $target_fields[$fieldname] = array("delimiter" => $delimiter, "listFields" => $allmergeFields);
            }
            elseif(!empty($v->Orgfields[0]->Relfield)&& isset($v->Orgfields[0]->Relfield) ){
                $allRelValues=array();
            foreach ($v->Orgfields[0]->Relfield as $key => $value) {
                if ($key == 'RelfieldName') {
                    $allRelValues['fieldname']=(string) $value;
                }
                if ($key == 'RelModule') {
                    $allRelValues['relmodule']=(string) $value;
                }
                if ($key == 'linkfield') {
                    $allRelValues['linkfield']=(string) $value;
                }
                if ($key == 'delimiter') {
                    $delimiter = (string) $value;
                    if (empty($delimiter))
                        $delimiter = "";
                }
                
            }
            $allmergeFields[]=$allRelValues;
            $target_fields[$fieldname] = array("delimiter" => $delimiter, "relatedFields" => $allmergeFields);
            }
        }
        return $target_fields;
    }
        function readTableMappingType() {
        $map = htmlspecialchars_decode($this->column_fields['content']);
        $x = new crXml();
        $x->loadXML($map);
        $target_fields = array();
        $match_fields = array();
        $update_rules = array();
        foreach ($x->map->fields[0] as $k => $v) {
            $fieldname = (string) $v->fieldname;
            //$allmergeFields = array();
            $value=(string) $v->value;
            $target_fields[$fieldname] = array('value'=>$value);
            //}
        }
        return array('target' => $target_fields);
    }
    function readImportType() {
    $map = htmlspecialchars_decode($this->column_fields['content']);
        $x = new crXml();
        $x->loadXML($map);
        $target_fields = array();
        $match_fields = array();
        $update_rules = array();
        foreach ($x->map->fields[0] as $k => $v) {
            $fieldname = (string) $v->fieldname;
            $conditionsValues = array();
            //$allmergeFields = array();
            if (!empty($v->value)) {
                $value = (string) $v->value;
            }
            if (!empty($v->conditions[0])) {
                foreach ($v->conditions[0] as $condkey => $condval) {
                    $condition = (string) $condval->cond;
                    $condValue = (string) $condval->value;
                    $conditionsValues[] = array('cond' => $condition, 'value' => $condValue);
                }
            }
            if(!empty($v->predefined)){
            $predefined=(string) $v->predefined;
            }
            $target_fields[$fieldname] = array('value'=>$value,'predefined'=>$predefined,'conditions'=>$conditionsValues);
            //}
        }
        foreach ($x->map->matches[0] as $key => $value) {
            //if ($key == 'fieldname') {
            $fldname = (string) $value->fieldname;
            //}
            //if ($key == 'value') {
            $fldval = (string) $value->value;
            // }
            $match_fields[$fldname] = $fldval;
        }
         foreach ($x->map->options[0] as $key => $value) {
            //if ($key == 'update') {
           //$update = (string) $value;
            //}
            //if ($key == 'value') {
            //$fldval = (string) $value->value;
            // }
            $update_rules[$key] = (string) $value;
        }

        return array('target' => $target_fields, 'match' => $match_fields,'options'=>$update_rules);
    }
       function getMapOriginEmail_table(){
           $map=htmlspecialchars_decode($this->column_fields['content']);
           $x = new crXml();
           $x->loadXML($map);
           //var_dump($return_module);
           return (string)$x->map->targetmodule[0]->targetid;
        }

       function getMapSendEmails(){
            $map=htmlspecialchars_decode($this->column_fields['content']);
            $x = new crXml();
            $x->loadXML($map);
            $target_data=array();
            $index=0;
            foreach($x->map->fields->field->Orgfields[0] as $k=>$v) {
                if($k=='Orgfield'){
                $target_tab[]=  (string)$v->Orgfieldid;
                $target_table[]=  (string)$v->OrgfieldName;
                }
              }
           $target_fields['tab']=$target_tab;
           $target_fields['field']=$target_table;
           return $target_fields;
        }

function getMapFieldDependency(){
            $map=htmlspecialchars_decode($this->column_fields['content']);
            $x = new crXml();
            $x->loadXML($map);
            $target_data=array();
            $index=0;
            foreach($x->map->fields->field->Orgfields[0] as $k=>$v) {
                if($k=='Orgfield'){
                $targetfield[]=  (string)$v->fieldname;
                $action[]=  (string)$v->fieldaction;
                $targetvalue[]=  (string)$v->fieldvalue;
                }
                if($k=='Responsiblefield'){
                $respfield[]=  (string)$v->fieldname;
                $respvalue[]=  (string)$v->fieldaction;
                }
              }
           $target_fields['targetfield']=  $targetfield;
           $target_fields['action']=  $action;
           $target_fields['targetvalue']=  $targetvalue;
           
           $target_fields['respfield']=  $respfield;
           $target_fields['respvalue']=  $respvalue;
                
           return $target_fields;
        }

         function getMapMessageMailer(){
            $map=htmlspecialchars_decode($this->column_fields['content']);
            $x = new crXml();
            $x->loadXML($map);
            $target_data=array();
            $index=0;
            foreach($x->map->fields->field->Orgfields[0] as $k=>$v) {
                if($k=='Orgfield'){
                $targetfield[]=  (string)$v->OrgfieldName;
                $columnfield[]=  (string)$v->OrgfieldCorrespond;
                }
                
                if($k=='RelatedField'){
                $relatedtargetfield[]=  (string)$v->OrgfieldName;
                $relatedcolumnfield[]=  (string)$v->OrgfieldCorrespond;
                $relatedentityfield[]=  (string)$v->OrgfieldRelatedField;
                }
                if($k=='MatchingField'){
                $matching_field[]=  (string)$v->OrgfieldName;
                $matching_field2[]=  (string)$v->OrgfieldCorrespond;
                }
                
              }
           $target_fields['targetfield']=  $targetfield;
           $target_fields['columnfield']=  $columnfield;
           
           $target_fields['relatedtargetconstant']=  $relatedtargetfield;
           $target_fields['relatedcolumnfield']=  $relatedcolumnfield;
           $target_fields['relatedui10field']=  $relatedentityfield;
           
           $target_fields['match_field']=  $matching_field;
           $target_fields['match_field2']=  $matching_field2;
           
           return $target_fields;
       }

       function getMapCustomerTypes(){
            $map=htmlspecialchars_decode($this->column_fields['content']);
            $x = new crXml();
            $x->loadXML($map);
            $target_data=array();
            $index=0;
            foreach($x->map as $k=>$v) {
                
                if($k=='respmodule'){
                    $respmodule=  (string)$v;
                }
                
                if($k=='relatedmodule'){
                    $modulename=  (string)$v->name;
                    $field=  (string)$v->field;
                    $throughmodulename=array();
                    // indirect related modules
                    $relmod=  (string)$v->step->throughmodule;
                    $throughfield=  (string)$v->step->throughfield;
                    if($relmod!==''){
                        $throughmodulename[$relmod]=  $throughfield;
                    }
                    $rel_mod[$modulename]=array('field'=>$field,
                        'throughmodule'=>$throughmodulename);
                }
              }
           $target_fields['respmodule']=  $respmodule;
           $target_fields['relmodule']=  $rel_mod;
           return $target_fields;
       }

       function getMapMenuStructure(){
            $default_language = 'it_it';
            global $current_language,$adb; 
            $current_language = $default_language; 
            $current_language = vtws_preserveGlobal('current_language',$current_language); 

            $appStrings = return_application_language($current_language);
            $appListString = return_app_list_strings_language($current_language);
            vtws_preserveGlobal('app_strings',$appStrings);
            vtws_preserveGlobal('app_list_strings',$appListString);
            
            $map=htmlspecialchars_decode($this->column_fields['content']);
            $x = new crXml();
            $x->loadXML($map);
            $rows=array();
            $columns=array();
            $name='';
            foreach($x->map->menus[0] as $k0=>$v0) {
            if($k0=='profile'){
                $profile=(string)$v0;
            }
            else{
                foreach($v0 as $k1=>$v1) {
                    if($k1=='label')
                        $label=(string)$v1;
                    if($k1=='name'){
                        $res_entity=$adb->pquery("Select isentitytype"
                                . " from vtiger_tab"
                                . " where name=?",array((string)$v1));
                        $isentitytype=$adb->query_result($res_entity,0,'isentitytype');
                        $columns[$label][]=  array('item'=>(string)$v1,
                            'label'=>getTranslatedString((string)$v1,(string)$v1),
                            'entitytype'=>$isentitytype);
                    }
                  }
            }
              }
            return array('modules'=>$columns,'profile'=>$profile);
        }
        
       function getMapPortalDvBlocks(){
            $map=htmlspecialchars_decode($this->column_fields['content']);
            $x = new crXml();
            $x->loadXML($map);
            $rows=array();$rows1=array();
            $columns=array();
            $name='';
            foreach($x->map->blocks[0] as $k0=>$v0) {
                    foreach($v0 as $k2=>$v2) {
                        foreach($v0 as $k=>$v) {
                        if($k=='name'){
                            $name=$v;
                        }
                        if($k=='row'){
                        $columns=array();
                        foreach($v as $k1=>$v1) {
                                    if($k1=='column'){
                                    $columns[]=  (string)$v1;
                                }
                          }
                          $rows["$name"][]=  $columns; 
                        }
//                        $rows1[$name]=  $rows; 
                    }
                    }
              }
           
               $target_fields['rows']=  $rows;
          return $target_fields;
        }
           
       function getMapSQL(){
           $map= htmlspecialchars_decode($this->column_fields['content']);
           $x = new crXml();
           $x->loadXML($map);
           $sqlString=(string)$x->map->sql[0];
		   var_dump((string)$x->map);
           return $sqlString;
        }

     function initListOfModules(){
            global $adb;
            $restricted_modules = array('Emails','Events');
            $restricted_blocks = array('LBL_IMAGE_INFORMATION','LBL_COMMENTS','LBL_COMMENT_INFORMATION');
            //tabid and name of modules
            $this->module_id = array();
            //name and blocks of modules
            $this->module_list = array();

            $modulerows = vtlib_prefetchModuleActiveInfo(false);
            $cachedInfo = VTCacheUtils::lookupMap_ListofModuleInfos();

            if($cachedInfo !== false) {
                                $this->module_list = $cachedInfo['module_list'];
                                $this->related_modules = $cachedInfo['related_modules'];
                                $this->rel_fields = $cachedInfo['rel_fields'];

            } else {
            if($modulerows) {
                foreach($modulerows as $resultrow) {
                        if($resultrow['presence'] == '1') continue;      // skip disabled modules
                        if($resultrow['isentitytype'] != '1') continue;  // skip extension modules
                        if(in_array($resultrow['name'], $restricted_modules)) { // skip restricted modules
                                continue;
                        }
                        if($resultrow['name']!='Calendar'){
                                $this->module_id[$resultrow['tabid']] = $resultrow['name'];
                        } else {
                                $this->module_id[9] = $resultrow['name'];
                                $this->module_id[16] = $resultrow['name'];

                        }
                        $this->module_list[$resultrow['name']] = array();
                }
                //get tabId of all modules
                $moduleids = array_keys($this->module_id);
                $moduleblocks = $adb->pquery("SELECT blockid, blocklabel, tabid FROM vtiger_blocks WHERE tabid IN (" .generateQuestionMarks($moduleids) .")",
                                                        array($moduleids));
                        $prev_block_label = '';
                        if($adb->num_rows($moduleblocks)) {
                                while($resultrow = $adb->fetch_array($moduleblocks)) {
                                        $blockid = $resultrow['blockid'];
                                        $blocklabel = $resultrow['blocklabel'];
                                        $module = $this->module_id[$resultrow['tabid']];

                                        if(in_array($blocklabel, $restricted_blocks) ||
                                                in_array($blockid, $this->module_list[$module]) ||
                                                isset($this->module_list[$module][getTranslatedString($blocklabel,$module)])) {
                                                    continue;
                                                }

                                        if(!empty($blocklabel)){
                                                if($module == 'Calendar' && $blocklabel == 'LBL_CUSTOM_INFORMATION')
                                                        $this->module_list[$module][$blockid] = getTranslatedString($blocklabel,$module);
                                                else
                                                        $this->module_list[$module][$blockid] = getTranslatedString($blocklabel,$module);
                                                $prev_block_label = $blocklabel;
                                        } else {
                                                $this->module_list[$module][$blockid] = getTranslatedString($prev_block_label,$module);
                                        }
                                }
                        }

        //                $relatedmodules = $adb->pquery(
        //                        "SELECT vtiger_tab.name, vtiger_relatedlists.tabid FROM $dbname.vtiger_tab
        //                        INNER JOIN $dbname.vtiger_relatedlists on vtiger_tab.tabid=vtiger_relatedlists.related_tabid
        //                        WHERE vtiger_tab.isentitytype=1
        //                        AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
        //                        AND vtiger_tab.presence = 0 AND vtiger_relatedlists.label!='Activity History'
        //                        UNION
        //                        SELECT module, vtiger_tab.tabid FROM $dbname.vtiger_fieldmodulerel
        //                        INNER JOIN $dbname.vtiger_tab on vtiger_tab.name = vtiger_fieldmodulerel.relmodule
        //                        WHERE vtiger_tab.isentitytype = 1
        //                        AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
        //                        AND vtiger_tab.presence = 0",
        //                        array($restricted_modules,$restricted_modules)
        //                );
                   $relatedmodules = $adb->pquery(
                                "SELECT module as name, vtiger_tab.tabid,fieldid FROM vtiger_fieldmodulerel
                                INNER JOIN vtiger_tab on vtiger_tab.name = vtiger_fieldmodulerel.relmodule
                                WHERE vtiger_tab.isentitytype = 1
                                AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
                                AND vtiger_tab.presence = 0
				UNION
			        SELECT relmodule as name, vtiger_tab.tabid,fieldid FROM vtiger_fieldmodulerel
                                INNER JOIN vtiger_tab on vtiger_tab.name = vtiger_fieldmodulerel.module
                                WHERE vtiger_tab.isentitytype = 1
                                AND vtiger_tab.name NOT IN(".generateQuestionMarks($restricted_modules).")
                                AND vtiger_tab.presence = 0",
                                array($restricted_modules,$restricted_modules)
                        );
                        if($adb->num_rows($relatedmodules)) {
                                while($resultrow = $adb->fetch_array($relatedmodules)) {
                                        $module = $this->module_id[$resultrow['tabid']];
                                        if(!isset($this->related_modules[$module])) {
                                                $this->related_modules[$module] = array();
                                        }
                                         if(!isset($this->related_modules[$module])) {
                                                $this->rel_fields[$module] = array();
                                        }

                                        if($module != $resultrow['name']) {
                                                $this->related_modules[$module][] = $resultrow['name'];

                                                $this->rel_fields[$module][$resultrow['name']] = $this->getFieldName($resultrow['fieldid']);
                                        }
                                }
                        }
                    // Put the information in cache for re-use
                    VTCacheUtils::updateMap_ListofModuleInfos($this->module_list, $this->related_modules,$this->rel_fields);
                }
            }
        }

     function getFieldName($fieldid){
        global $adb;
        $result = $adb->pquery("Select fieldname from vtiger_field where fieldid = ?",array($fieldid));
        return $adb->query_result($result,0,'fieldname');
    }

    function getPriModuleFieldsList($module,$modtype,$mode='')
    {
        global $log;
        $log->debug("Entering getPriModuleFieldsList method moduleID=".$module);
            $cachedInfo = VTCacheUtils::lookupMap_ListofModuleInfos();
            if($cachedInfo !== false) {
                $this->module_list = $cachedInfo['module_list'];
                $this->rel_fields = $cachedInfo['rel_fields'];
            }
            $modName = getTabModuleName($module);
            $this->primodule = $module;
//            if($mode == "edit")
//            foreach($this->module_list[$modName] as $key=>$value)
//            {
//                $ret_module_list[$modName][$value] = $this->getFieldListbyBlock($modName,$key,'direct');
//            }
//            else
   $temp = array();
            foreach($this->module_list->$modName as $key=>$value)
            {
               $temp = $this->getFieldListbyBlock($modName,$key,'direct');
     		if($temp !== NULL)  $ret_module_list[$modName][$value] = $temp;
            }
//            var_dump($ret_module_list);
            if($modtype == "target"){
                $this->related_modules = $cachedInfo['related_modules'];
                $this->rel_fields = $cachedInfo['rel_fields'];
                if($mode == "edit") $arr = $this->related_modules[$modName];
                else $arr = $this->related_modules->$modName;
                for($i=0;$i <count($arr);$i++){
                    $modName = $arr[$i];
                    if($mode == "edit")
                    foreach($this->module_list[$modName] as $key=>$value)
                    {
                            $ret_module_list[$modName][$value] = $this->getFieldListbyBlock($modName,$key,'related');
                    }
                    else
                    foreach($this->module_list->$modName as $key=>$value)
                    {
                            $ret_module_list[$modName][$value] = $this->getFieldListbyBlock($modName,$key,'related');
                    }
                }
            }
    $this->pri_module_columnslist = $ret_module_list;
    $log->debug("Exiting getPriModuleFieldsList method");
    return true;
}

function getPrimaryFieldHTML($module,$modtype)
{
    global $app_strings;
    global $current_language;
    $id_added=false;
    $mod_strings = return_module_language($current_language,$module);
    $block_listed = array();
    $modName = getTabModuleName($module,$this->dbname);
    foreach($this->module_list->$modName as $key=>$value)
    {
            if(isset($this->pri_module_columnslist[$modName][$value]) && !$block_listed[$value])
            {
                    $block_listed[$value] = true;
                    $shtml .= "<optgroup label=\"".getTranslatedString($modName, $module)." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
                    if($id_added==false){
                            $shtml .= "<option value=\"vtiger_crmentity:crmid:".$modName."_ID:crmid:I\">".getTranslatedString($modName.' ID', $modName)."</option>";
                            $id_added=true;
                    }
                    foreach($this->pri_module_columnslist[$modName][$value] as $field=>$fieldlabel)
                    {
                            if(isset($mod_strings[$fieldlabel]))
                            {
                                    $shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
                            }else
                            {
                                    $shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
                            }
                    }
            }
    }
    if($modtype == "target"){
    $arr = $this->related_modules->$modName;
    for($i=0;$i <count($arr);$i++){
    $modName = $arr[$i];
    foreach($this->module_list->$modName as $key=>$value)
    {
            if(isset($this->pri_module_columnslist[$modName][$value]) && !$block_listed[$value])
            {
                    $block_listed[$value] = true;
                    $shtml .= "<optgroup label=\"".getTranslatedString($modName, $module)." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
                    if($id_added==false){
                            $shtml .= "<option value=\"vtiger_crmentity:crmid:".$modName."_ID:crmid:I\">".getTranslatedString($modName.' ID', $modName)."</option>";
                            $id_added=true;
                    }
                    foreach($this->pri_module_columnslist[$modName][$value] as $field=>$fieldlabel)
                    {
                            if(isset($mod_strings[$fieldlabel]))
                            {
                                    $shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
                            }else
                            {
                                    $shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
                            }
                    }
            }
    }
  }
 }
    return $shtml;
}
function getFieldListbyBlock($module,$block,$type)
{
        global $adb;
        global $log;
        global $current_user;

        if(is_string($block)) $block = explode(",", $block);
        $tabid = getTabid($module,$this->dbname);
        if ($module == 'Calendar') {
                $tabid = array('9','16');
        }
        $params = array($tabid, $block);
        $sql = "select * from $this->dbname.vtiger_field where vtiger_field.tabid in (". generateQuestionMarks($tabid) .") and vtiger_field.block in (". generateQuestionMarks($block) .") and vtiger_field.displaytype in (1,2,3) and vtiger_field.presence in (0,2) ";
        $result = $adb->pquery($sql, $params);
        $pmod = getTabModuleName($this->primodule,$this->dbname);

        $noofrows = $adb->num_rows($result);
        for($i=0; $i<$noofrows; $i++)
        {
                $fieldtablename = $adb->query_result($result,$i,"tablename");
                $fieldcolname = $adb->query_result($result,$i,"columnname");
                $fieldname = $adb->query_result($result,$i,"fieldname");
                $fieldtype = $adb->query_result($result,$i,"typeofdata");
                $uitype = $adb->query_result($result,$i,"uitype");
                $fieldid = $adb->query_result($result,$i,"fieldid");
                $fieldtype = explode("~",$fieldtype);
                $fieldtypeofdata = $fieldtype[0];

                if($uitype == 68 || $uitype == 59)
                {
                        $fieldtypeofdata = 'V';
                }
                if($fieldtablename == "vtiger_crmentity")
                {
                        $fieldtablename = $fieldtablename.$module;
                }
                if($fieldname == "assigned_user_id")
                {
                        $fieldtablename = "vtiger_users".$module;
                        $fieldcolname = "user_name";
                }
                if($fieldname == "assigned_user_id1")
                {
                        $fieldtablename = "vtiger_usersRel1";
                        $fieldcolname = "user_name";
                }

                $fieldlabel = $adb->query_result($result,$i,"fieldlabel");
                $fieldlabel1 = str_replace(" ","_",$fieldlabel);
                if($type == "related")
                $optionvalue = $fieldtablename.":".$fieldcolname.":".$module.":".$fieldname.":".$fieldid.":".$type.":".$this->rel_fields->$pmod->$module;
                else
                $optionvalue = $fieldtablename.":".$fieldcolname.":".$module.":".$fieldname.":".$fieldid.":".$type;
                if($module != 'HelpDesk' || $fieldname !='filename')  $module_columnlist[$optionvalue] = $fieldlabel;
        }
        $blockname = getBlockName($block,$this->dbname);
        if($blockname == 'LBL_RELATED_PRODUCTS' && ($module=='PurchaseOrder' || $module=='SalesOrder' || $module=='Quotes' || $module=='Invoice')){
                $fieldtablename = 'vtiger_inventoryproductrel';
                $fields = array('productid'=>getTranslatedString('Product Name',$module),
                                                'serviceid'=>getTranslatedString('Service Name',$module),
                                                'listprice'=>getTranslatedString('List Price',$module),
                                                'discount'=>getTranslatedString('Discount',$module),
                                                'quantity'=>getTranslatedString('Quantity',$module),
                                                'comment'=>getTranslatedString('Comments',$module),
                );
                $fields_datatype = array('productid'=>'V',
                                                'serviceid'=>'V',
                                                'listprice'=>'I',
                                                'discount'=>'I',
                                                'quantity'=>'I',
                                                'comment'=>'V',
                );
                foreach($fields as $fieldcolname=>$label){
                        $fieldtypeofdata = $fields_datatype[$fieldcolname];
                        if($type == "related")
                        $optionvalue =  $fieldtablename.":".$fieldcolname.":".$module.":".$fieldcolname.":".$fieldid.":".$type.":".$this->rel_fields->$pmod->$module;
                        else
                        $optionvalue =  $fieldtablename.":".$fieldcolname.":".$module.":".$fieldcolname.":".$fieldid.":".$type;
                        $module_columnlist[$optionvalue] = $label;
                }
        }
        $log->info("Map :: FieldColumns->Successfully returned FieldlistbyBlock".$module.$block);
        return $module_columnlist;
}
function getBlockInfo($modId)
{
    global $adb , $log;
    $moduleName = getTabModuleName($modId);
    $blockinfo=array();
    $blocks_query=$adb->pquery("select blockid,tabid,blocklabel from vtiger_blocks where tabid=? order by sequence ASC",array($modId));
    for($i=0;$i<$adb->num_rows($blocks_query);$i++)
    {
        $blockinfo[]=array(
            'blockid' => $adb->query_result($blocks_query,$i,'blockid'),
            'tabid' => $adb->query_result($blocks_query,$i,'tabid'),
            'blocklabel'=>$adb->query_result($blocks_query,$i,'blocklabel'),
            );
    }
    if($moduleName=='Project')
    {
        $size=sizeof($blockinfo);
        $blockinfo[$size]=array(
            'blockid' => '1000',
            'tabid' => $modId,
            'blocklabel'=>'Execute',
        );
    }
    return $blockinfo;
}
function getBlockHTML($blocks,$module)
{
    global $log;
    global $app_strings;
    global $current_language;
    $id_added=false;
    $mod_strings = return_module_language($current_language,$module);
    $block_listed = array();
    $modName = getTabModuleName($module,$this->dbname);$shtml='';
    for($i=0;$i<sizeof($blocks);$i++)
    {
       foreach($blocks[$i] as $key=>$value)
           {  if($key=='blocklabel')
                 if($value=='Execute')
                 $shtml .= "<option value=\"".$blocks[$i]['blockid']."\" class=\"select\" style=\"border:none\">".getTranslatedString($value, $modName)."</option>";
                   else
                 $shtml .= "<option value=\"".$blocks[$i]['blockid']."\" class=\"select\" style=\"border:none\">".getTranslatedString($value, $modName)."</option>";
            }
    }
  return $shtml;
}
function getBlockAccessBlockInfo() {
	global $log,$adb;
	$map = htmlspecialchars_decode($this->column_fields['content']);
	$x = new crXml();
	$x->loadXML($map);
	$blockinfo = array();
	foreach($x->map->blocks[0] as $block){
		$blockinfo[]=array(
			'blockid'    => (string) $block->blockID,
			'blockname'  => (string) $block->blockname,
			'blocklabel' => (string) $block->blocklabel,
		);
	}
	return $blockinfo;
}
function readInputFields() {
        $map = htmlspecialchars_decode($this->column_fields['content']);
        $x = new crXml();
        $x->loadXML($map);
        $input_fields = array();
        foreach ($x->map->input->fields[0] as $k => $v) {
            $fieldname = (string) $v->fieldname;
            $input_fields[] = $fieldname;
        }
        return $input_fields;
    }

    function readOutputFields() {
        $map = htmlspecialchars_decode($this->column_fields['content']);
        $x = new crXml();
        $x->loadXML($map);
        $output_fields = array();
        foreach ($x->map->output->fields[0] as $k => $v) {
            $fieldname = (string) $v->fieldname;
            $output_fields[] = $fieldname;
        }
        return $output_fields;
    }
        function getEntityFieldNamesByTablename($tablename) {
	$adb = PearDatabase::getInstance();
	$data = array();
	if (!empty($tablename)) {
		$query = "select fieldname,modulename,tablename,entityidfield from vtiger_entityname where tablename = ?";
		$result = $adb->pquery($query, array($tablename));
		$fieldsName = $adb->query_result($result, 0, 'fieldname');
		$tableName = $adb->query_result($result, 0, 'tablename');
		$entityIdField = $adb->query_result($result, 0, 'entityidfield');
		$moduleName = $adb->query_result($result, 0, 'modulename');
		if (!(strpos($fieldsName, ',') === false)) {
			$fieldsName = explode(',', $fieldsName);
		}
	}
	$data = array("tablename" => $tableName, "modulename" => $moduleName, "fieldname" => $fieldsName, "entityidfield" => $entityIdField);
	return $data;
}
    function create_query(){
        global $log,$adb;
        $content=html_entity_decode($this->column_fields['content']);
        $isxml=$this->isXML($content);
           if($isxml=='true'){
             $xml=simplexml_load_string($content);
             $xml_module=$xml->modules->module;  
             foreach($xml_module as $key=>$value){
                 $modules[]=array('modulename'=>(string)$value->modulename,
                                   'tablename'=>(string)$value->tablename);
             }
             
             $xml_fields=$xml->modules->fields->field;
             foreach($xml_fields as $field_key=>$field_value){
                 $fields[]=array('fieldname'=>(string)$field_value->fieldname,
                               'operator'=>(string)$field_value->operator,
                               'expectedvalue'=>(string)$field_value->expectedvalue,
                               'uniquesearch'=>(string)$field_value->uniquesearch
                         );
             }
        }
		   var_dump($modules,$fields);
        $sql='';
        $select=' ';
        $join=' ';
        $crmentity_check=' ';
        $deleted=' ';
        $where=" ";
        $entityid=array();
        if(sizeof($modules)>0 && sizeof($fields)>0 ){
            if(sizeof($modules)>1){
                for($i=0;$i<sizeof($modules);$i++){
                    for($j=$i+1;$j<sizeof($modules);$j++){
                        $modulename_i=$modules[$i]['modulename'];
                        $modulename_j=$modules[$j]['modulename'];
                       // $tablename_i=$modules[$i]['tablename'];
                        //$tablename_j=$modules[$j]['tablename'];
                   
                        $related_modules_query=$adb->pquery('SELECT * FROM  vtiger_fieldmodulerel 
                            WHERE  (module=? and relmodule=?) or (module=? and relmodule=?)',array($modulename_i,$modulename_j,$modulename_j,$modulename_i));
                        if($adb->num_rows($related_modules_query)==1){
                              if($j!=1)   {
                                  $join.=' join ';
                                  $deleted.=' and ';
                                 }
                            $module=$adb->query_result($related_modules_query,0,'module');
                            $relmodule=$adb->query_result($related_modules_query,0,'relmodule');
                            $fieldid=$adb->query_result($related_modules_query,0,'fieldid');
                            require_once('include/utils/CommonUtils.php');
                            $relmodule_info=getEntityFieldNames($relmodule);//moduli uitype 10 ->2
                            $module_info=getEntityFieldNames($module);// mod 1
                            $fieldid_name_query=$adb->pquery("select fieldname from vtiger_field where fieldid=?",array($fieldid));
                            $fieldid_name=$adb->query_result($fieldid_name_query,0,'fieldname');
                              
                          if(stristr($join,$module_info['tablename'])!='' && stristr($join,$relmodule_info['tablename'])!=''){
                             $join.=" ". $relmodule_info['tablename'] . " as tab".$i.$j." on ".$module_info['tablename'].".".$fieldid_name."=tab".$i.$j.".".$relmodule_info['entityidfield'];
                             $crmentity_check.=" join vtiger_crmentity as c".$i.$j." on c".$i.$j.".crmid=tab".$i.$j.".".$relmodule_info['entityidfield'];
                             $deleted.=" c$i$j.deleted=0 ";
                             $entityid[]=$relmodule_info['tablename'].".".$fieldid_name;
                                        
                          }else
                          if(stristr($join,$module_info['tablename'])!=''){                   
                             $join.=" ". $relmodule_info['tablename'] . " on ".$module_info['tablename'].".".$fieldid_name."=".$relmodule_info['tablename'].".".$relmodule_info['entityidfield'];
                             $crmentity_check.=" join vtiger_crmentity as c".$i.$j." on c".$i.$j.".crmid=".$relmodule_info['tablename'].".".$fieldid_name;
                             $deleted.=" c$i$j.deleted=0 ";
                             $entityid[]=$relmodule_info['tablename'].".".$fieldid_name;
                                          } 
                           else if(stristr($join,$relmodule_info['tablename'])!='') {
                            $join.=" ".$module_info['tablename']."  on ".$module_info['tablename'].".".$fieldid_name."=".$relmodule_info['tablename'].".".$relmodule_info['entityidfield'];
                            $crmentity_check.=" join vtiger_crmentity as c".$i.$j." on c".$i.$j.".crmid=".$module_info['tablename'].".".$module_info['entityidfield'];
                            $deleted.=" c$i$j.deleted=0 ";
                            $entityid[]=$module_info['tablename'].".".$module_info['entityidfield'];
                           }    
                            else{
                            $join.=$module_info['tablename']." join  ".$relmodule_info['tablename'] . " on ".$module_info['tablename'].".".$fieldid_name."=".$relmodule_info['tablename'].".".$relmodule_info['entityidfield'];
                            $crmentity_check.=" join vtiger_crmentity as c".$i.$j." on c".$i.$j.".crmid=".$module_info['tablename'].".".$module_info['entityidfield'];
                            $crmentity_check.=" join vtiger_crmentity as c".$i.$j.$j." on c".$i.$j.$j.".crmid=".$relmodule_info['tablename'].".".$relmodule_info['entityidfield'];
                            $deleted.=" c$i$j.deleted=0 and c$i$j$j.deleted=0 " ;
                            $entityid[]=$module_info['tablename'].".".$module_info['entityidfield'];
                            $entityid[]=$relmodule_info['tablename'].".".$relmodule_info['entityidfield'];
                            
                            }
                         }
                    }
                }
              
                $where.=' and ';
                $join.= $crmentity_check. " where  ". $deleted;
            } else if(sizeof($modules)==1){
                 $modulename=$modules[0]['modulename'];
                 //$tablename=$modules[0]['tablename'];
                  $module_info=getEntityFieldNames($modulename);
                  $join=" ". $module_info['tablename']." join vtiger_crmentity on crmid=".$module_info['entityidfield']." where deleted=0 and " ;
                              
            }
            $vals=' ';
            for($f=0;$f<sizeof($fields);$f++){
                if($f!=0)
                    $vals.=' and ';

                $fieldname=$fields[$f]['fieldname'];
                $fieldnames[]=$fieldname;
                
                $operator=trim($fields[$f]['operator']);
                $expectedvalue=trim($fields[$f]['expectedvalue']);
                $uniquesearch=trim($fields[$f]['uniquesearch']);
                if($operator=='in' && stristr($expectedvalue,'vtiger_')==''){
                     $vals.=" " .$fieldname." ".$operator. ' ("'.str_replace(';','","',$expectedvalue).' ")';
                    
                }else
                if(stristr($expectedvalue,';')!=''){
                    $array_values=explode(';',$expectedvalue);
                    $c=0;
                    foreach($array_values as $value){
                       $c++;
                        if($c>1)
                            $vals.=' and ';
                        if($value=="''")
                           $vals.=" " .$fieldname." ".$operator. " '' ";
                        else
                        if(is_string($value)){
                            
                         if(stristr($value,'.')!='' && stristr($value,'vtiger_')!=''){
                          $expectedvalue_array=  explode('.', $value);
                          $expected_table_name=$expectedvalue_array[0];

                          if(stristr($join,$expected_table_name)==''   || ($uniquesearch!='' && stristr($join,$expected_table_name)!='')){
                                $searched_field_array=explode('.',$fieldname);// search is taken from fieldname
                                $searched_field_table_name=$searched_field_array[0];
                                $value=$this->generate_subquery($fieldname,$value,$uniquesearch,$expected_table_name,$searched_field_table_name,$i,$j);
                           }
                 }
                            
                            
                            
                           if(stristr($value,'vtiger_')!='' )
                                  $vals.=" " .$fieldname." ".$operator. " $value";
                          else  if(stristr($operator,'like')!='')
                          $vals.=" " .$fieldname." ".$operator. " '%$value%'";
                            else
                          $vals.=" " .$fieldname." ".$operator. " '$value'";  
                        }else
                         $vals.=" " .$fieldname." ".$operator. " $value";
                    }
                    
                }else  if($expectedvalue=="''")
                           $vals.=" " .$fieldname." ".$operator. " '' ";
                 else  if(is_string($expectedvalue)){
                                      // vetem ne rastin kur expected element eshte nje vtiger_module.field.
                      if(stristr($expectedvalue,'.')!='' && stristr($expectedvalue,'vtiger_')!='' ){
                          $expectedvalue_array=  explode('.', $expectedvalue);
                          $expected_table_name=$expectedvalue_array[0];

                          if(stristr($join,$expected_table_name)=='' || ($uniquesearch!='' && stristr($join,$expected_table_name)!='')){
                                $searched_field_array=explode('.',$fieldname);// search is taken from fieldname
                                $searched_field_table_name=$searched_field_array[0];
                                             
                                $expectedvalue=$this->generate_subquery($fieldname,$expectedvalue,$uniquesearch,$expected_table_name,$searched_field_table_name,$i,$j);
                    }
                 }
           
                 $log->Debug($expectedvalue);
                 if(stristr($expectedvalue,'select')!='' || stristr($expectedvalue,'vtiger_')!='' )
                          $vals.=" " .$fieldname." ".$operator. " $expectedvalue";
                 else if(stristr($operator,'like')!='')
                                 $vals.=" " .$fieldname." ".$operator. " '%$expectedvalue%'";
                                  else
                                $vals.=" " .$fieldname." ".$operator. " '$expectedvalue'";  
                       }
               else  
                        $vals.=" " .$fieldname." ".$operator. " $expectedvalue";
              }
            
            $where.="  $vals ";
            array_merge($fieldnames,$entityid);
            $select=" select ".implode(',',$fieldnames)." from ";
            $sql=$select ." ". $join ." ".$where;
            var_dump($sql);
            return $sql;
        }else
            return false;
     }
     function generate_subquery($fieldname,$expectedvalue,$uniquesearch,$expected_table_name,$searched_field_table_name,$i,$j){
       global $adb,$currentModule,$log;
            
       $expected_module=$this->getEntityFieldNamesByTablename($expected_table_name);
       $searched_module=$this->getEntityFieldNamesByTablename($searched_field_table_name);
       $where=' where ';
       $related_tab_query=$adb->pquery('SELECT * FROM  vtiger_fieldmodulerel 
           WHERE  (module=? and relmodule=?) or (module=? and relmodule=?)',array($expected_module['modulename'],$searched_module['modulename'],$searched_module['modulename'],$expected_module['modulename']));
 
       if($adb->num_rows($related_tab_query)>0){
           $first_module_name=$adb->query_result($related_tab_query,0,'module');
           $related_module_name=$adb->query_result($related_tab_query,0,'relmodule');
           $first_module_fieldid=$adb->query_result($related_tab_query,0,'fieldid');// field in first module
           
           $first_module=$this->getEntityFieldNamesByTablename($first_module_name);
           $related_module=$this->getEntityFieldNamesByTablename($related_module_name);
                                        
           $related_fieldid_name_query=$adb->pquery("select fieldname from vtiger_field where fieldid=?",array($first_module_fieldid));
           $related_fieldid_name=$adb->query_result($related_fieldid_name_query,0,'fieldname');
                                        
           $expectedvalue_base="(Select $fieldname from ". $first_module['tablename'] . " join vtiger_crmentity as c".$i.$j." on c".$i.$j.".crmid=".$first_module['tablename'].".".$first_module['entityidfield']."
                             join  ".$related_module['tablename']." on  ".$related_module['tablename'].".".$related_module['entityidfield']."=".$first_module['tablename'].".".$related_fieldid_name."
                            join vtiger_crmentity as c".$i.$j.$j." on c".$i.$j.$j.".crmid=".$related_module['tablename'].".".$related_module['entityidfield'];
           $where.="  c".$i.$j.".deleted=0 and c".$i.$j.$j.".deleted=0 ";
           }else // no relation between modules
              { 
               $first_module=$this->getEntityFieldNamesByTablename($searched_field_table_name);
               $related_module=$this->getEntityFieldNamesByTablename($expected_table_name);
                                                      
               $expectedvalue_base="(Select $expectedvalue from ". $first_module['tablename'] . " join vtiger_crmentity as c".$i.$j." on c".$i.$j.".crmid=".$first_module['tablename'].".".$first_module['entityidfield']."
                                join  ".$related_module['tablename']." on  $fieldname=$expectedvalue 
                                 join vtiger_crmentity as c".$i.$j.$j." on c".$i.$j.$j.".crmid=".$related_module['tablename'].".".$related_module['entityidfield'];
                $where.="  c".$i.$j.".deleted=0 and c".$i.$j.$j.".deleted=0 ";
               
              }
             
              if($uniquesearch!=''){
                   if(stristr($uniquesearch,';')==''){
                       $expectedvalue_array=$this->generate_sub_subquery($expectedvalue_base,$uniquesearch,$searched_module,$expected_module,$i,$j);
                  
              }else{
                  $uniquesearch_array=explode(';',$uniquesearch);
                  $expectedvalue_arrays='';
                  foreach($uniquesearch_array as $uniquesearch){
                      if(!empty($expectedvalue_arrays)){
                          $expectedvalue_base=$expectedvalue_arrays['sql'];
                          $expectedvalue_arrays=$this->generate_sub_subquery($expectedvalue_base,$uniquesearch,$searched_module,$expected_module,$i,$j);
                           }else
                          $expectedvalue_arrays=$this->generate_sub_subquery($expectedvalue_base,$uniquesearch,$searched_module,$expected_module,$i,$j);
                          
                      }
                  $expectedvalue_array=$expectedvalue_arrays;
                  }
             
              }
             
if(!empty($expectedvalue_array)){
    $expectedvalue=$expectedvalue_array['sql'];
    $first_unique_module=$expectedvalue_array['first_unique_module'];
    $related_unique_module=$expectedvalue_array['related_unique_module'];
}  
                if($_REQUEST['proj_id']!='')
                    $_REQUEST['record']=$_REQUEST['proj_id'];
                
                if($currentModule==$first_module['modulename'] && ($_REQUEST['record']!=''))
                    $expectedvalue.=" and ". $first_module['tablename'].".".$first_module['entityidfield']."=".$_REQUEST['record']. " )";
                
                else if($_REQUEST['record']!='' && $currentModule==$related_module['modulename'])
                    $expectedvalue.=" and ". $related_module['tablename'].".".$related_module['entityidfield']."=".$_REQUEST['record']. " )";
                
                else if($first_unique_module['modulename']==$currentModule && $_REQUEST['record']!='' )
                      $expectedvalue.=" and ". $first_unique_module['tablename'].".".$related_module['entityidfield']."=".$_REQUEST['record']. " )";
                
                else if($related_unique_module['modulename']==$currentModule && $_REQUEST['record']!='' )
                      $expectedvalue.=" and ". $related_unique_module['tablename'].".".$related_unique_module['entityidfield']."=".$_REQUEST['record']. " )";
                
                else if(($_REQUEST['record']!='') && $currentModule!=''){
                    
                    $currentmodule_info=getEntityFieldNames($currentModule);
                    $related_tables_query=$adb->pquery('SELECT * FROM  vtiger_fieldmodulerel 
                                WHERE  (module=? and relmodule=?) or (module=? and relmodule=?)',array($currentmodule_info['modulename'],$first_module['modulename'],$first_module['modulename'],$currentmodule_info['modulename']));
                    
                      if($adb->num_rows($related_tables_query)==0)
                          $related_tables_query=$adb->pquery('SELECT * FROM  vtiger_fieldmodulerel 
                                         WHERE  (module=? and relmodule=?) or (module=? and relmodule=?)',array($currentmodule_info['modulename'],$related_module['modulename'],$related_module['modulename'],$currentmodule_info['modulename']));
                                       
                      if($adb->num_rows($related_tables_query)>0){
                                             $field_relation=$adb->query_result($related_tables_query,0,'fieldid');
                                             $relation_fieldid_name_query=$adb->pquery("select fieldname ,tablename from vtiger_field where fieldid=?",array($field_relation));
                                             $relation_fieldid_name=$adb->query_result($relation_fieldid_name_query,0,'fieldname');
                                             $table=$adb->query_result($relation_fieldid_name_query,0,'tablename');
                                             $expectedvalue.=" and ".$table.".".$relation_fieldid_name."=".$_REQUEST['record'].' )' ;
                                        }
                                        else
                                             $expectedvalue.=" ) ";
                                        }
                                        else
                                      $expectedvalue.=" ) ";
     
      return $expectedvalue;
     }
     
     function generate_sub_subquery($expectedvalue_base,$uniquesearch,$searched_module,$expected_module,$i,$j){
         global $adb;
         if(stristr($uniquesearch,'=')!=''){
                      $uniquesearch_array=explode('=',$uniquesearch);
                      $uniquesearch_field=$uniquesearch_array[0];
                      $uniquesearch_value=$uniquesearch_array[1];
                      
                      $uniquesearch_table_name=explode('.',$uniquesearch_field);
                      //tab exist in query and value is not field name
                      if(stristr($expectedvalue_base,$uniquesearch_table_name)!='' && stristr($uniquesearch_value,'vtiger_')==''){
                          if(is_string($uniquesearch_value))
                              $where.=" and  $uniquesearch_field='".$uniquesearch_value."' ";
                          else 
                              $where.=" and  $uniquesearch ";
                        }    //tab does not exist in query and value is not field name
                    else if(stristr($expectedvalue_base,$uniquesearch_table_name[0])!='' && stristr($uniquesearch_value,'vtiger_')!=''){
                              $uniquesearch_table_name=explode('.',$uniquesearch_value);
                             $uniquesearch_module=$this->getEntityFieldNamesByTablename($uniquesearch_table_name[0]);

                         $related_uniquesearch_query=$adb->pquery('SELECT * FROM  vtiger_fieldmodulerel 
                           WHERE  (module=? and relmodule=?) or (module=? and relmodule=?)',
                                 array($searched_module['modulename'],$uniquesearch_module['modulename'],$uniquesearch_module['modulename'],$searched_module['modulename']));
                        
                         if($adb->num_rows($related_uniquesearch_query)==0)
                             $related_uniquesearch_query=$adb->pquery('SELECT * FROM  vtiger_fieldmodulerel 
                           WHERE  (module=? and relmodule=?) or (module=? and relmodule=?)',
                                 array($expected_module['modulename'],$uniquesearch_module['modulename'],$uniquesearch_module['modulename'],$expected_module['modulename']));
                       
                         if($adb->num_rows($related_uniquesearch_query)>0) {
                            $first_unique_module_name=$adb->query_result($related_uniquesearch_query,0,'module');
                            $related_unique_module_name=$adb->query_result($related_uniquesearch_query,0,'relmodule');
                            $first_unique_module_fieldid=$adb->query_result($related_uniquesearch_query,0,'fieldid');// field in first module
                          
                            $first_unique_module=getEntityFieldNames($first_unique_module_name);
                            $related_unique_module=getEntityFieldNames($related_unique_module_name);
                           
                            $related_unique_fieldid_name_query=$adb->pquery("select fieldname from vtiger_field where fieldid=?",array($first_unique_module_fieldid));
                            $related_unique_fieldid_name=$adb->query_result($related_unique_fieldid_name_query,0,'fieldname');
                          
                            $expectedvalue_base.=" join ". $uniquesearch_table_name[0] ." on ". $first_unique_module['tablename'].".$related_unique_fieldid_name=".$related_unique_module['tablename'].".".$related_unique_module['entityidfield']." ";
                            $where.=" and  c".$i.$j.".deleted=0 " ;
                            if(is_string($uniquesearch_value) && stristr($uniquesearch_value,'vtiger_')!='')
                              $where.=" and $uniquesearch_field=".$uniquesearch_value." ";
                            else if(is_string($uniquesearch_value))
                              $where.=" and $uniquesearch_field='".$uniquesearch_value."' ";
                            else 
                              $where.=" and $uniquesearch ";
                            
                         }
                    }      
                     else if(stristr($expectedvalue_base,$uniquesearch_table_name[0])=='' && stristr($uniquesearch_value,'vtiger_')==''){
                         $uniquesearch_module=$this->getEntityFieldNamesByTablename($uniquesearch_table_name[0]);

                         $related_uniquesearch_query=$adb->pquery('SELECT * FROM  vtiger_fieldmodulerel 
                           WHERE  (module=? and relmodule=?) or (module=? and relmodule=?)',
                                 array($searched_module['modulename'],$uniquesearch_module['modulename'],$uniquesearch_module['modulename'],$searched_module['modulename']));
                       
                         if($adb->num_rows($related_uniquesearch_query)>0) {
                            $first_unique_module_name=$adb->query_result($related_uniquesearch_query,0,'module');
                            $related_unique_module_name=$adb->query_result($related_uniquesearch_query,0,'relmodule');
                            $first_unique_module_fieldid=$adb->query_result($related_uniquesearch_query,0,'fieldid');// field in first module
                           
                           
                            $first_unique_module=getEntityFieldNames($first_unique_module_name);
                            $related_unique_module=getEntityFieldNames($related_unique_module_name);
                           
                            $related_unique_fieldid_name_query=$adb->pquery("select fieldname from vtiger_field where fieldid=?",array($first_unique_module_fieldid));
                            $related_unique_fieldid_name=$adb->query_result($related_unique_fieldid_name_query,0,'fieldname');
                          
                            $expectedvalue_base.=" join ". $uniquesearch_table_name[0] ." on ". $first_unique_module['tablename'].".$related_unique_fieldid_name=".$related_unique_module['tablename'].".".$related_unique_module['entityidfield']." ";
                            $where.=" and  c".$i.$j.".deleted=0 " ;
                            if(is_string($uniquesearch_value))
                              $where.=" and $uniquesearch_field='".$uniquesearch_value."' ";
                            else 
                              $where.=" and $uniquesearch ";
                            
                         }
                      }
                       else if(stristr($expectedvalue_base,$uniquesearch_table_name[0])=='' && stristr($uniquesearch_value,'vtiger_')!=''){
                           
                       }
                  }
                  
                  $expectedvalue=$expectedvalue_base. ' '. $where;
                  $result=array('sql'=>$expectedvalue,
                      'first_unique_module'=>$first_unique_module,
                      'related_unique_module'=>$related_unique_module
                      );

                  return $result;
     }
     function search_update_fields(){
         global $adb,$log;
         
        $content=html_entity_decode($this->column_fields['content']);
        $isxml=$this->isXML($content);

        if($isxml=='true'){
             $xml=simplexml_load_string($content);
             $search_xml_module=$xml->search->module;  
             foreach($search_xml_module as $search_key=>$search_value){
                 $search_module[]=array('modulename'=>(string)$search_value->modulename,
                                        'tablename'=>(string)$search_value->tablename);
             }
             
             $search_xml_fields=$xml->search->fields->field;
             foreach($search_xml_fields as $search_field_key=>$search_field_value){
                $search_fields[]=array('fieldname'=>(string)$search_field_value->fieldname,
                                       'operator'=>(string)$search_field_value->operator,
                                       'expectedvalue'=>(string)$search_field_value->expectedvalue
                         );
             }
             $search_xml_rules=$xml->search->rules->rule;
             foreach($search_xml_rules as $search_rule_key=>$search_rule_value){
                 $rule=array();
                 for($f=0;$f<sizeof($search_rule_value->searchfield);$f++){
                   if((string)$search_rule_value->searchfield[$f]!='')
                   $rule_field=(string)$search_rule_value->searchfield[$f];
                   if((string)$search_rule_value->operator[$f] !='')
                    $rule_operator=(string)$search_rule_value->operator[$f];
                    $rule[]=array('field'=>$rule_field,
                                  'operator'=>$rule_operator,
                                  'alter_expectedvalue'=>array('alter_operator'=>(string)$search_rule_value->alter_expectedvalue[$f]->operator,
                                                               'alter_value'=>(string)$search_rule_value->alter_expectedvalue[$f]->value));
                 }
                 $search_rules[]=$rule;
             }  

             $update_xml_module=$xml->update->modules->module;
             foreach($update_xml_module as $update_key=>$update_value){
                  $update_module[]=array('modulename'=>(string)$update_value->modulename,
                                         'tablename'=>(string)$update_value->tablename);
              }
           
             $update_xml_fields=$xml->update->fields->field;
             foreach($update_xml_fields as $update_field_key=>$update_field_value){
                  $update_fields[]=array('fieldname'=>(string)$update_field_value->fieldname,
                                         'operator'=>(string)$update_field_value->operator,
                                         'expectedvalue'=>(string)$update_field_value->expectedvalue
                         );
             }
            
        }
        if(!empty($search_module) && !empty($search_fields)){
        $result=array('Search'=>array('module'=>$search_module,
                                      'fields'=>$search_fields,
                                      'rules'=>$search_rules),
                       'Update'=>array('module'=>$update_module,
                                       'fields'=>$update_fields));
        return $result;
        }
        else
            return false;
   }
   
   function search_query($result){
     global $log;
    require_once('include/utils/CommonUtils.php');
    
    $search_module=$result['Search']['module'][0]['modulename'];
    //$search_table=$result['Search'][0]['tablename'];
    
    $search_module_info=getEntityFieldNames($search_module);
    
    $select="SELECT * from ".$search_module_info['tablename']." join vtiger_crmentity on 
        crmid=".$search_module_info['tablename'].".".$search_module_info['entityidfield'] ." where deleted=0 and ";

    $rules=$result['Search']['rules'];
    for($r=0;$r<sizeof($rules);$r++){
        $expected_values='';$where='';

      
        for($rr=0;$rr<sizeof($rules[$r]);$rr++){
            
            $rule_field=trim($rules[$r][$rr]['field']);
            $rule_operator=$rules[$r][$rr]['operator'];
            $rule_alter_rule=$rules[$r][$rr]['alter_expectedvalue'];
            for($f=0;$f<sizeof($result['Search']['fields']);$f++){
                
               $fieldname=trim($result['Search']['fields'][$f]['fieldname']);
               if($rule_field===$fieldname){ 
                if($where!=''){
                $where.=" ".$rule_operator." ";
                $expected_values.=',';
                }
                if($rule_alter_rule['alter_operator']!=''){
                    $expected_values.=$rule_alter_rule['alter_value'];
                }else
                $expected_values.=$result['Search']['fields'][$f]['expectedvalue'];
                $where.=" ".$fieldname.$result['Search']['fields'][$f]['operator']."? ";
                $f=sizeof($result['Search']['fields'])+1;
                }

            }
            
        }
        $sql_array[]=array('select'=>$select,
                           'where'=>$where,
                           'expectedvalues'=>$expected_values,
                           'update'=>$result['Update']);
    }
   return $sql_array;
   }
function getBlocksPortal1($module, $disp_view, $mode, $col_fields = '', $info_type = '',$profile) {
	global $log;
	$log->debug("Entering getBlocks(" . $module . "," . $disp_view . "," . $mode . "," . $col_fields . "," . $info_type . ") method ...");
	global $adb, $current_user;
	global $mod_strings;
	$tabid = getTabid($module);
	$block_detail = Array();
	$getBlockinfo = "";
	$query = "select blockid,blocklabel,show_title,display_status from vtiger_blocks where tabid=? and $disp_view=0 and visible = 0 order by sequence";
	$result = $adb->pquery($query, array($tabid));
	$noofrows = $adb->num_rows($result);
	$prev_header = "";
        $blockid_list = array();
	for ($i = 0; $i < $noofrows; $i++) {
		$blockid = $adb->query_result($result, $i, "blockid");
		$blockid_list[] = $blockid;
		$block_label[$blockid] = $adb->query_result($result, $i, "blocklabel");

		$sLabelVal = getTranslatedString($block_label[$blockid], $module);
		$aBlockStatus[$sLabelVal] = $adb->query_result($result, $i, "display_status");
	}
        
	if ($mode == 'edit') {
		$display_type_check = 'vtiger_field.displaytype = 1';
	} elseif ($mode == 'mass_edit') {
		$display_type_check = 'vtiger_field.displaytype = 1 AND vtiger_field.masseditable NOT IN (0,2)';
	} else {
		$display_type_check = 'vtiger_field.displaytype in (1,4)';
	}

	/* if($non_mass_edit_fields!='' && sizeof($non_mass_edit_fields)!=0){
	  $mass_edit_query = "AND vtiger_field.fieldname NOT IN (". generateQuestionMarks($non_mass_edit_fields) .")";
	  } */

	//retreive the vtiger_profileList from database
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	if ($disp_view == "detail_view") {
		
			$profileList = array($profile);
			$sql = "SELECT vtiger_field.*, vtiger_profile2field.readonly FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND vtiger_field.displaytype IN (1,2,4) and vtiger_field.presence in (0,2) AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
			$params = array($tabid, $blockid_list, $profileList);
			//Postgres 8 fixes
			if ($adb->dbType == "pgsql")
				$sql = fixPostgresQuery($sql, $log, 0);
		$result = $adb->pquery($sql, $params);

		// Added to unset the previous record's related listview session values
		if (isset($_SESSION['rlvs']))
			unset($_SESSION['rlvs']);

		$getBlockInfo = getDetailBlockInformation($module, $result, $col_fields, $tabid, $block_label);
	}
	else {
		if ($info_type != '') {
			$profileList = array($profile);
				$sql = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid  WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND info_type = ? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
				$params = array($tabid, $blockid_list, $info_type, $profileList);
				//Postgres 8 fixes
				if ($adb->dbType == "pgsql")
					$sql = fixPostgresQuery($sql, $log, 0);
		}
		else {
			$profileList = array("$profile");
				$sql = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid  WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
				$params = array($tabid, $blockid_list, $profileList);
				//Postgres 8 fixes
				if ($adb->dbType == "pgsql")
					$sql = fixPostgresQuery($sql, $log, 0);
		}
		$result = $adb->pquery($sql, $params);
		$getBlockInfo = getBlockInformation($module, $result, $col_fields, $tabid, $block_label, $mode);
	}
	$log->debug("Exiting getBlocks method ...");
	if (count($getBlockInfo) > 0) {
		foreach ($getBlockInfo as $label => $contents) {
			if (empty($getBlockInfo[$label])) {
				unset($getBlockInfo[$label]);
			}
		}
	}
	return $getBlockInfo;
}
}
?>
