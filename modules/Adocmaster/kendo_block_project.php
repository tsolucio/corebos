<?php  
 /*************************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : Adecuaciones
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/



require_once('include/utils/Module_Tables.php');
                require_once('include/utils/utils.php');

                global $adb,$db_use,$log; 
                $content=array();
                $kaction=$_REQUEST['kaction'];
                $id=$_REQUEST['id']; 
                $kendo_block_id=$_REQUEST['kendo_block_id']; 
                $pointing_module=$_REQUEST['module'];  
                $tabid=  getTabid($pointing_module);
                
                 $a=$adb->pquery("SELECT *
                                  from vtiger_kendo_block where 
                                  id =?",array($kendo_block_id));
                $columns=$adb->query_result($a,0,'columns');
                $cond=$adb->query_result($a,0,'cond');
                $sort=$adb->query_result($a,0,'sort');
                $kendo_module=$adb->query_result($a,0,'module_name');
                $pointing_field_name=$adb->query_result($a,0,'pointing_field_name');
                    
                if($sort!=''){
                $so= explode(",",$sort);    
                $sort_by=$so[0]; 
                $order=$so[1];
                $query_sort= " order by $sort_by  $order";}                
                
                $col= explode(",",$columns);
                $kendo=explode(',',get_related_table($kendo_module));
                $pointing=explode(',',get_related_table($pointing_module));
                $kendo_module_table=$kendo[0];
                $kendo_module_id=$kendo[1];
                $pointing_module_table=$pointing[0];
                $pointing_module_id=$pointing[1];
                $pointing_module_field=$pointing_field_name;

                $query_cond='';
                if(in_array('smownerid', $col))
                {
                    $key_id=  array_search('smownerid',$col);
                    unset($col[$key_id]);
                }
                if(in_array('createdtime', $col))
                {
                    $key_id=  array_search('createdtime',$col);
                    unset($col[$key_id]);
                }
                if(in_array('modifiedtime', $col))
                {
                    $key_id=  array_search('modifiedtime',$col);
                    unset($col[$key_id]);
                }
   
                $col2=implode(",$pointing_module_table.",$col);
                 
                // retrieve record data     
                if($kaction=='retrieve'){
                     
                     if($cond!='')
                     {$query_cond= " and  $cond ";}
                     
                    $entity_field_arr=getEntityFieldNames($pointing_module);
                      $entity_field=$entity_field_arr["fieldname"];//var_dump($entity_field);
                      if (is_array($entity_field)) {
                        $entityname=implode(",$pointing_module_table.",$entity_field);
                      } 
                     else {$entityname=$entity_field;}
                        
                    $log->debug('alb6 '." 
                          SELECT $pointing_module_table.$pointing_module_id,
                          $pointing_module_table.$col2,$pointing_module_table.$entityname
                          ,vtiger_crmentity.smownerid,vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime
                          FROM $kendo_module_table
                          join $pointing_module_table on $kendo_module_table.$kendo_module_id = $pointing_module_table.$pointing_module_field
                          join vtiger_crmentity on crmid = $pointing_module_table.$pointing_module_id
                          where deleted = 0 and $kendo_module_table.$kendo_module_id=$id "
                          . "  $query_cond  $query_sort");
                          
                    $query=$adb->pquery(" 
                          SELECT $pointing_module_table.$pointing_module_id,
                          $pointing_module_table.$col2,$pointing_module_table.$entityname
                          ,vtiger_crmentity.smownerid,vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime
                          FROM $kendo_module_table
                          join $pointing_module_table on $kendo_module_table.$kendo_module_id = $pointing_module_table.$pointing_module_field
                          join vtiger_crmentity on crmid = $pointing_module_table.$pointing_module_id
                          where deleted = 0 and $kendo_module_table.$kendo_module_id=? "
                          . $query_cond ."  $query_sort",array($id));
                          
                      $count=$adb->num_rows($query);
                      
                      if(strpos($columns,'smownerid')!==false)
                       {   array_push($col,'smownerid');}
                      if(strpos($columns,'createdtime')!==false)
                       {   array_push($col,'createdtime');}
                       if(strpos($columns,'modifiedtime')!==false)
                       {   array_push($col,'modifiedtime');}

                       // var_dump($col); 

                      for($i=0;$i<$count;$i++){
                          $content[$i]['id']=$adb->query_result($query,$i,$pointing_module_id);
                          $content[$i]['href']='index.php?module='.$pointing_module.'&action=DetailView&record='.$content[$i]['id'];
                          $entityname_val='';
                          if (is_array($entity_field)) {
                              for($k=0;$k<sizeof($entity_field);$k++){
                                $entityname_val.=' '.$adb->query_result($query,$i,$entity_field[$k]);
                              }
                          } 
                          else{
                              $entityname_val=$adb->query_result($query,$i,$entityname);
                          }
                              
                          $content[$i]['name']=$entityname_val;
                           if(strpos($columns,'smownerid')!==false)
                           {   array_push($col,'smownerid');}
                           if(strpos($columns,'createdtime')!==false)
                           {   array_push($col,'createdtime');}
                           if(strpos($columns,'modifiedtime')!==false)
                           {   array_push($col,'modifiedtime');}
                               
                          for($j=0;$j<sizeof($col);$j++)
                          {
                          if($col[$j]=='')
                               continue;
                            
                          $a=$adb->query("SELECT *
                                  from vtiger_field
                                  WHERE columnname='$col[$j]'"
                                  . " and tabid = '$tabid' ");
                              $uitype=$adb->query_result($a,0,'uitype');
                              $fieldname=$adb->query_result($a,0,'fieldname');
                              $col_fields[$fieldname]=$adb->query_result($query,$i,$col[$j]);
                              
                              $block_info=getDetailViewOutputHtml($uitype,$fieldname,'',$col_fields,'','',$pointing_module);

                                  $ret_val=$block_info[1];
                                  if(strpos($ret_val,'href')!==false) //if contains link remmove it because kendo can't interpret it
                              {
                                  $pos1=strpos($ret_val,'>');
                                  $first_sub=substr($ret_val,$pos1+1);
                                  $pos2=strpos($first_sub,'<');
                                  $log->debug('ret_val'.$first_sub.' '.$pos2);
                                  $sec_sub=substr($first_sub,0,$pos2);
                                  $ret_val=$sec_sub;
                              }
                              
                              if($uitype=='10')
                              {
                                  $content[$i][$col[$j]]=$col_fields[$fieldname]; 
                                  $content[$i][$col[$j].'_display']=$ret_val;
                              }
                              else
                          $content[$i][$col[$j]]=$ret_val;
                      }
                      }
                        echo json_encode($content);

                }
                elseif($kaction=='create'){
                    require_once('modules/'.$pointing_module.'/'.$pointing_module.'.php');
                     $models=$_REQUEST['models'];
                     global $log,$current_user;
                     $log->debug('klm2 '.$val_cond);
                     $model_values=array();
                     $model_values=json_decode($models);
                     $mv=$model_values[0];
                     
                     $focus = CRMEntity::getInstance("$pointing_module");
                    $focus->id='';
                    for($j=0;$j<sizeof($col);$j++)
                      {
                      $focus->column_fields["$col[$j]"]=$mv->$col[$j];  // all chosen columns
                      } 
                      $a=$adb->query("SELECT fieldname from vtiger_field
                             WHERE columnname='$pointing_field_name'");
                      $fieldname=$adb->query_result($a,0,"fieldname");
                      $focus->column_fields["$fieldname"]=$id;   //  the pointing field ui10
                      $log->debug('albana2 '.$entityname);
                      $entity_field_arr=getEntityFieldNames($pointing_module);
                      $entity_field=$entity_field_arr["fieldname"];
                      if (is_array($entity_field)) {
			$entityname=$entity_field[0];
		      }
                     else {$entityname=$entity_field;}
                     $log->debug('albana2 '.$entityname);
                      $focus->column_fields["$entityname"]=$mv->$col[0].' - '.$mv->$col[1];
                      //'Generated from '.getTranslatedString($kendo_module,$kendo_module);   //  the entityname field 
                      $log->debug('klm3 '.$pointing_field_name.' '.$id);
                    $focus->column_fields['assigned_user_id']=$current_user->id;
                    $focus->save("$pointing_module"); 
    
                } 
                elseif($kaction=='edit'){
                     require_once('modules/'.$pointing_module.'/'.$pointing_module.'.php');
                     $models=$_REQUEST['models'];
                     global $log,$current_user;
                     $log->debug('klm2 '.$val_cond);
                     $model_values=array();
                     $model_values=json_decode($models);
                     $nr=count($model_values);
                     $mv=$model_values[$nr-1];
                     
                     $focus = CRMEntity::getInstance("$pointing_module");
                     $focus->retrieve_entity_info($mv->id,$pointing_module);
                     $focus->id=$mv->id;
                     $focus->mode='edit';
                     for($j=0;$j<sizeof($col);$j++)
                     {
                     $focus->column_fields["$col[$j]"]=$mv->$col[$j];  // all chosen columns
                     } 
                     
                     $entity_field_arr=getEntityFieldNames($pointing_module);
                     $entity_field=$entity_field_arr["fieldname"];
                      if (is_array($entity_field)) {
			$entityname=$entity_field[0];
		      }
                      else {$entityname=$entity_field;}
                      $log->debug('albana2 '.$entityname);
                      $focus->column_fields["$entityname"]=$mv->$col[0].' '.$mv->$col[1];   //  the entityname field 
                      $log->debug('klm3 '.$pointing_field_name.' '.$id);
                     $focus->column_fields["assigned_user_id"]=$focus->column_fields["assigned_user_id"];
                     $focus->save("$pointing_module"); 
                }              
                 elseif($kaction=='delete'){
                     require_once('modules/'.$pointing_module.'/'.$pointing_module.'.php');
                     $models=$_REQUEST['models'];
                     global $log,$current_user;
                     
                     $model_values=array();
                     $model_values=json_decode($models);
                     $nr=count($model_values);
                     $mv=$model_values[$nr-1];
                     
                     $focus = CRMEntity::getInstance("$pointing_module");
                     $focus->retrieve_entity_info($mv->id,$pointing_module);
                     $focus->id=$mv->id;
                     $focus->mode='edit';echo $mv->id;echo $mv->name;
                     $log->debug('klm5 '.$focus->id);
                     $a=$adb->query("SELECT fieldname from vtiger_field
                            WHERE columnname='$pointing_field_name'");
                     $fieldname=$adb->query_result($a,0,"fieldname");
                     $focus->column_fields["$fieldname"]='';   //  the pointing field ui10
                     
                     echo $fieldname;
                     $log->debug('klm6 '.$fieldname);
                     $focus->save("$pointing_module"); 
    
                    }?> 
