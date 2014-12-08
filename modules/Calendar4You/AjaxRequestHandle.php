<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

global $adb,$current_language, $current_user, $mod_strings;
switch($_REQUEST["handler"])
{
    case "fill_lang":
        $module = addslashes($_REQUEST["langmod"]);

        $mod_lang=return_specified_module_language($current_language,$module);

        $module_lang_labels = array_flip($mod_lang);
        $module_lang_labels = array_flip($module_lang_labels);
        asort($module_lang_labels);

        $keys=implode('||',array_keys($module_lang_labels));
        $values=implode('||',$module_lang_labels);
        echo $keys.'|@|'.$values;
        break;

    case "confirm_portal":
        $module = addslashes($_REQUEST["langmod"]);
        $curr_templatename = $_REQUEST["curr_templatename"];
        $mod_lang = return_specified_module_language($current_language,"XMLExport4You");
        $sql = "SELECT filename
                FROM vtiger_xmlexport4you
                INNER JOIN vtiger_xmlexport4you_settings USING(templateid)
                WHERE is_portal=? AND module=?";
        $params = array("1", $module);
        $result = $adb->pquery($sql, $params);
        $confirm = "";
        if($adb->num_rows($result) > 0){
          $templatename = $adb->query_result($result, 0, "filename");
          $confirm = $mod_lang["LBL_XMLEXPORT4YOU_TEMPLATE"]." '".$templatename."' ".$mod_lang["LBL_REPLACED_PORTAL_TEMPLATE"]." '".$curr_templatename."' ".$mod_lang["LBL_AS_PORTAL_TEMPLATE"];
        } else {
          $confirm = $mod_lang["LBL_VTIGER_TEMPLATE"]." ".$mod_lang["LBL_REPLACED_PORTAL_TEMPLATE"]." '".$curr_templatename."' ".$mod_lang["LBL_AS_PORTAL_TEMPLATE"];
        }
        echo $confirm;
        break;

    case "templates_order":
        $inStr = $_REQUEST["tmpl_order"];
        $inStr = rtrim($inStr, "#");
        $inArr = explode("#", $inStr);
        $tmplArr = array();
        foreach($inArr as $val){
            $valArr = explode("_", $val);
            $tmplArr[$valArr[0]]["order"] = $valArr[1];
            $tmplArr[$valArr[0]]["is_active"] = "1";
            $tmplArr[$valArr[0]]["is_default"] = "0";
        }
        
        $sql = "SELECT templateid, userid, is_active, is_default, sequence
                FROM vtiger_xmlexport4you_userstatus
                WHERE userid = ?";
        $result = $adb->pquery($sql, array($current_user->id));
        while($row = $adb->fetchByAssoc($result)){
            if(!isset($tmplArr[$row["templateid"]]))
                $tmplArr[$row["templateid"]]["order"] = $row["sequence"];

            $tmplArr[$row["templateid"]]["is_active"] = $row["is_active"];
            $tmplArr[$row["templateid"]]["is_default"] = $row["is_default"];
        }
        
        $adb->pquery("DELETE FROM vtiger_xmlexport4you_userstatus WHERE userid=?", array($current_user->id));
        
        $sqlA = "INSERT INTO vtiger_xmlexport4you_userstatus(templateid, userid, is_active, is_default, sequence)
                VALUES ";
        $sqlB = "";
        $params = array();
        foreach($tmplArr as $templateid=>$valArr){
            $sqlB .= "(?,?,?,?,?),";
            $params[] = $templateid;
            $params[] = $current_user->id;
            $params[] = $valArr["is_active"];
            $params[] = $valArr["is_default"];
            $params[] = $valArr["order"];
        }

        $result = "error";
        if($sqlB != ""){
            $sqlB = rtrim($sqlB, ",");
            $sql = $sqlA.$sqlB;
            $adb->pquery($sql, $params);
            $result = "ok";
        }    
                
        echo $result;
        break;
        
    case "custom_labels_edit";
        $sql = "DELETE FROM vtiger_xmlexport4you_label_vals WHERE label_id=? AND lang_id=?";
        $params = array($_REQUEST["label_id"], $_REQUEST["lang_id"]);
        $adb->pquery($sql, $params);

        $sql = "INSERT INTO vtiger_xmlexport4you_label_vals(label_id, lang_id, label_value) VALUES(?,?,?)";
        $params = array($_REQUEST["label_id"], $_REQUEST["lang_id"], $_REQUEST["label_value"]);
        $adb->pquery($sql, $params);
        break;
        
    case "fill_relblocks":
        require_once('modules/XMLExport4You/XMLExport4You.php');
        $module = addslashes($_REQUEST["selmod"]);
        
        $XMLExport4You = new XMLExport4You();
        $Related_Blocks = $XMLExport4You->GetRelatedBlocks($module);
        $keys = implode('||',array_keys($Related_Blocks));
        $values = implode('||',$Related_Blocks);
        echo $keys.'|@|'.$values;
        break;
        
    case "get_relblock":
        $record = addslashes($_REQUEST["relblockid"]);
    	$sql = "SELECT * FROM vtiger_xmlexport4you_relblocks WHERE relblockid = ?";
    	$result = $adb->pquery($sql, array($record));
    	$Blockdata = $adb->fetchByAssoc($result, 0);

    	$body = $Blockdata["block"];
    	$body = str_replace("RELBLOCK_START","RELBLOCK".$record."_START",$body);
    	$body = str_replace("RELBLOCK_END","RELBLOCK".$record."_END",$body);
        echo $body;
        break;
        
    case "delete_relblock":
        $record = addslashes($_REQUEST["relblockid"]);
        $sql = "UPDATE vtiger_xmlexport4you_relblocks SET deleted = 1 WHERE relblockid = ?";
    	$adb->pquery($sql, array($record));
    	break;
}

exit;
?>