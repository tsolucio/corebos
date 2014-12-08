<?php
/*************************************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
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
*************************************************************************************************/

function vtyiicpng_getWSEntityId($entityName,$user='') {
	global $adb,$log;
	$wsrs=$adb->pquery('select id from vtiger_ws_entity where name=?',array($entityName));
	if ($wsrs and $adb->num_rows($wsrs)==1) {
		$wsid = $adb->query_result($wsrs,0,0);
	} else {
		$wsid = 0;
	}
	return $wsid.'x';
}

function evvt_strip_html_links($text) {
	$text = preg_replace('/<a [^>]*?>/','',$text);
	$text=str_replace('</a>','',$text);
	return $text;
}

function vtws_changePortalUserPassword($email,$newPass)
{   global $adb,$log;
    $log->debug("Entering search function with parameter accountname: ".$accountname);

    $nra=$adb->pquery("Update vtiger_portalinfo set user_password=? where user_name=?",array($newPass,$email));

    if ($nra) return true;
    else return false;
}
function vtws_findByPortalUserName($username) {
	global $adb,$log;
	$log->debug("Entering function vtws_findByPortalUserName");
	$nra=$adb->query_result($adb->pquery("select count(*) from vtiger_portalinfo where isactive=1 and user_name=?",array($username)),0,0);
	if (empty($nra))
		$output=false;
	else
		$output=true;
	$log->debug("Exiting function vtws_findByPortalUserName");
	return $output;
}

function vtws_sendRecoverPassword($username) {
	global $adb,$log,$current_user, $PORTAL_URL, $url_code;
	$log->debug("Entering function vtws_sendRecoverPassword");

	$ctors=$adb->query("select contactid,email,user_password
			from vtiger_contactdetails
			inner join vtiger_portalinfo on id=contactid
			where isactive=1 and user_name='$username'");
	if (!ctors or $adb->num_rows($ctors)==0) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD,"Invalid username: username not found or not active");
	}
	require_once 'modules/Emails/mail.php';
	require_once 'modules/Contacts/Contacts.php';
	$cto=$adb->fetch_array($ctors);
	$password = $cto['user_password'];
	$entityData = VTEntityData::fromEntityId($adb, $cto['contactid']);
	$contents = Contacts::getPortalEmailContents($entityData,$password);
	$subject = getTranslatedString('Customer Portal Login Details','Contacts');
	$mail_status = send_mail('Contacts',$cto['email'],$current_user->user_name,"",$subject,$contents);

	$log->debug("Exiting function vtws_sendRecoverPassword");
	return $mail_status;
}
function vtws_getPortalUserDateFormat($user) {
	global $adb,$log;
	if (isset($user->column_fields['date_format']) and !empty($user->column_fields['date_format']))
		return $user->column_fields['date_format'];
	else
		return 'yyyy-mm-dd';
}
function vtws_getPortalUserInfo($user) {
	global $adb,$log;
	$usrinfo = array();
	$retfields = array('date_format','first_name','last_name','email1');
	foreach ($retfields as $fld) {
		if (isset($user->column_fields[$fld]) and !empty($user->column_fields[$fld]))
			$usrinfo[$fld] =  $user->column_fields[$fld];
	}
	return $usrinfo;
}
function vtws_AuthenticateContact($email,$password)
{   global $adb,$log;
    $log->debug("Entering AuthenticateContact function with parameter email: ".$email." password:".$password);

    $nra=$adb->query_result($adb->pquery("select id
     from vtiger_portalinfo
     inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
     inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id
     where vtiger_crmentity.deleted=0 and user_name=? and user_password=?
       and isactive=1 and vtiger_customerdetails.portal=1",array($email,$password)),0,0);

    if (!empty($nra)) return vtyiicpng_getWSEntityId('Contacts').$nra;
    else return false;
}
function vtws_getPicklistValues($fld_module) {
    global $adb,$log;
    include_once('modules/PickList/PickListUtils.php');
    $log->debug("Entering getPicklistValues function with parameter fieldname: ".$fieldname);
    $res=array();
    $all=array();
    if($fld_module == 'Documents') {
     $result=$adb->query("select folderid,foldername from vtiger_attachmentsfolder");
     $number=$adb->num_rows($result);
     $DocumentFoldersWSID=vtyiicpng_getWSEntityId('DocumentFolders');
     for($i=0;$i<$number;$i++){
         $folderid=$DocumentFoldersWSID.$adb->query_result($result,$i,0);
         $foldername=$adb->query_result($result,$i,1);
         $all[$folderid]=$foldername;
     }
     $res['folderid']=$all;
    } else {
      $allpicklists=getUserFldArray($fld_module,'H1');
      foreach($allpicklists as $picklist){
        $res[$picklist['fieldname']]=$picklist['value'];
      }
    }
    return serialize($res);
}

function vtws_getUItype($module) {
    global $adb,$log;
    $log->debug("Entering getUItype function with parameter modulename: ".$module);
    $tabid=getTabid($module);
    $res=$adb->pquery('select uitype,fieldname from vtiger_field where tabid=? and presence in (0,2) ',array($tabid));
    $nr=$adb->num_rows($res);
    $resp=array();
    for($i=0;$i<$nr;$i++) {
      $fieldname=$adb->query_result($res,$i,'fieldname');
      $resp[$fieldname]=$adb->query_result($res,$i,'uitype');
    }
    return $resp;
}
function vtws_getReferenceValue($strids) {
	global $log,$adb;
	$ids=unserialize($strids);
	$log->debug("Entering vtws_getReferenceValue with id ".implode(',',$ids));
	foreach($ids as $id){
		list($wsid,$realid)=explode('x',$id);
		$modulename=$adb->query_result($adb->pquery('select name from vtiger_ws_entity where id=?',array($wsid)),0,0);
		if($modulename=='DocumentFolders'){
			$result[$id]=array('module'=>$modulename,'reference'=>$adb->query_result($adb->pquery('select foldername from vtiger_attachmentsfolder where folderid = ?',array($realid)),0,0));
		} elseif ($modulename=='Groups') {
			$result[$id]=array('module'=>$modulename,'reference'=>$adb->query_result($adb->pquery('select groupname from vtiger_groups where groupid = ?',array($realid)),0,0));
		} else {
			$entityinfo=getEntityName($modulename,$realid);
			$result[$id]=array('module'=>$modulename,'reference'=>$entityinfo[$realid]);
		}
	}
	$log->debug('Exit vtws_getReferenceValue with'.serialize($result));
	return serialize($result);
}

// $query string contains the search term we are looking for
// $search_onlyin is a comma separated list of modules to search in
// $restrictionids is an array which contains the user we are to search as and the account and contact restrictions
function vtws_getSearchResults($query,$search_onlyin,$restrictionids,$user) {
	global $adb,$log,$current_user;
	$res=array();
	// security restrictions
	if (empty($query) or empty($restrictionids) or !is_array($restrictionids)) return serialize($res);
	if (empty($restrictionids['userId']) or empty($restrictionids['accountId']) or empty($restrictionids['contactId'])) return serialize($res);
	list($void,$accountId) = explode('x',$restrictionids['accountId']);
	list($void,$contactId) = explode('x',$restrictionids['contactId']);
	list($void,$userId) = explode('x',$restrictionids['userId']);
	$current_user->retrieveCurrentUserInfoFromFile($userId);
	// if connected user does not have admin privileges > user must be the connected user
	if ($user->is_admin!='on' and $user->id!=$userId) return serialize($res);
	// connected user must have access to account and contact > this will be restricted by the coreBOS system and the rest of the code
	// start work
	require_once('modules/CustomView/CustomView.php');
	require_once('include/utils/utils.php');
	// Was the search limited by user for specific modules?
	$search_onlyin = (empty($search_onlyin) ? array() : explode(',',$search_onlyin));
	$object_array = getSearchModules($search_onlyin);
	$total_record_count = 0;
	$i = 0;
	$j=0;
	$moduleRecordCount = array();
	foreach($object_array as $module => $object_name){
		$listquery = getListQuery($module);
		$oCustomView = new CustomView($module);
		//Instead of getting current customview id, use cvid of All so that all entities will be found
		$cv_res = $adb->pquery("select cvid from vtiger_customview where viewname='All' and entitytype=?", array($module));
		$viewid = $adb->query_result($cv_res,0,'cvid');

		$listquery = $oCustomView->getModifiedCvListQuery($viewid,$listquery,$module);
		if (!empty($accountId) and !empty($contactId)) {
			switch ($module) {
				case 'Products':
					// FIXME:  add inner join on relations to accounts and contacts
					break;
				case 'Services':
					// FIXME:  add inner join on relations to accounts and contacts
					break;
				case 'Documents':
					$listquery = str_replace(' WHERE ', " inner join vtiger_senotesrel on vtiger_senotesrel.notesid=vtiger_notes.notesid and (vtiger_senotesrel.crmid=$accountId or vtiger_senotesrel.crmid=$contactId) WHERE ",$listquery);
					break;
			}
		}
		$where = getUnifiedWhere($listquery,$module,$query);
		if($where != ''){
			$listquery .= ' and ('.$where.')';
		}
		if (!empty($accountId) and !empty($contactId)) {
			$cond = evvt_PortalModuleRestrictions($module,$accountId,$contactId);
			if ($cond != '')
				$listquery .= ' and ('.$cond.')';
		}
		$count_result = $adb->query($listquery);
		$noofrows = $adb->num_rows($count_result);
		$moduleRecordCount[$module]['count'] = $noofrows;
		$navigation_array = VT_getSimpleNavigationValues(1, 100, $noofrows);
		$list_result = $adb->query($listquery);
		$focus = CRMEntity::getInstance($module);
		$listview_entries = getSearchingListViewEntries($focus,$module,$list_result,$navigation_array,"","","","",$oCustomView,"","","",true);
		$total_record_count = $total_record_count + $noofrows;
		if(!empty($listview_entries)) {
			foreach($listview_entries as $key=>$element) {
				$res[$j]=$element;
				$j++;
			}
		}
		$i++;
	}
	$result=serialize($res);
	return $result;
}

function evvt_PortalModuleRestrictions($module,$accountId,$contactId) {
	$condition = '';
	switch ($module) {
		case 'Contacts':
			$condition = "vtiger_contactdetails.accountid=$accountId";
			break;
		case 'Accounts':
			$condition = "vtiger_account.accountid=$accountId";
			break;
		case 'Quotes':
			$condition = "vtiger_quotes.accountid=$accountId or vtiger_quotes.contactid=$contactId";
			break;
		case 'SalesOrder':
			$condition = "vtiger_salesorder.accountid=$accountId or vtiger_salesorder.contactid=$contactId";
			break;
		case 'ServiceContracts':
			$condition = "vtiger_servicecontracts.sc_related_to=$accountId or vtiger_servicecontracts.sc_related_to=$contactId";
			break;
		case 'Invoice':
			$condition = "vtiger_invoice.accountid=$accountId or vtiger_invoice.contactid=$contactId";
			break;
		case 'HelpDesk':
			$condition = "vtiger_troubletickets.parent_id=$accountId or vtiger_troubletickets.parent_id=$contactId";
			break;
		case 'Assets':
			$condition = "vtiger_assets.account=$accountId";
			break;
		case 'Project':
			$condition = "vtiger_project.linktoaccountscontacts=$accountId or vtiger_project.linktoaccountscontacts=$contactId";
			break;
		case 'Products':
			//$condition = "related.Contacts='".$contactId."'";
			break;
		case 'Services':
			//$condition = "related.Contacts='".$contactId."'";
			break;
		case 'Faq':
			$condition = "faqstatus='Published'";
			break;
		case 'Documents':
			// already added in main search function
			break;
		default:
			$condition = '';
	}
	return $condition;
}

/**
 * To get the modules allowed for global search this function returns all the
 * modules which supports global search as an array in the following structure
 * array($module_name1=>$object_name1,$module_name2=>$object_name2,$module_name3=>$object_name3,$module_name4=>$object_name4,-----);
 */
function getSearchModules($filter = array()) {
	global $adb;
	// vtlib customization: Ignore disabled modules.
	//$sql = 'select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid where vtiger_tab.tabid not in (16,29)';
	$sql = 'select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid where vtiger_tab.tabid not in (16,29) and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2)';
	// END
	$result = $adb->pquery($sql, array());
	while($module_result = $adb->fetch_array($result)){
		$modulename = $module_result['name'];
		// Do we need to filter the module selection?
		if(!empty($filter) && is_array($filter) && !in_array($modulename, $filter)) {
			continue;
		}
		// END
		if($modulename != 'Calendar'){
			$return_arr[$modulename] = $modulename;
		}else{
			$return_arr[$modulename] = 'Activity';
		}
	}
	return $return_arr;
}

function getSearchingListViewEntries($focus, $module,$list_result,$navigation_array,$relatedlist='',$returnset='',$edit_action='EditView',$del_action='Delete',$oCv='',$page='',$selectedfields='',$contRelatedfields='',$skipActions=false,$linksallowed=false)
{
	global $log;
	global $mod_strings;
	$log->debug("Entering getSearchingListViewEntries(".get_class($focus).",". $module.",".$list_result.",".$navigation_array.",".$relatedlist.",".$returnset.",".$edit_action.",".$del_action.",".(is_object($oCv)? get_class($oCv) : $oCv).") method ...");
	$tabname = getParentTab();
	global $adb,$current_user;
	global $app_strings;
	$noofrows = $adb->num_rows($list_result);
	$list_block = Array();
	global $theme;
	$evt_status = '';
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	//getting the vtiger_fieldtable entries from database
	$tabid = getTabid($module);

	//added for vtiger_customview 27/5
	if($oCv)
	{
		if(isset($oCv->list_fields))
		{
			$focus->list_fields = $oCv->list_fields;
		}
	}
	if(is_array($selectedfields) && $selectedfields != '')
	{
		$focus->list_fields = $selectedfields;
	}

	// Remove fields which are made inactive
	$focus->filterInactiveFields($module);

	//Added to reduce the no. of queries logging for non-admin user -- by minnie-start
	$field_list = array();
	$j=0;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	foreach($focus->list_fields as $name=>$tableinfo)
	{
		$fieldname = $focus->list_fields_name[$name];
		if($oCv)
		{
			if(isset($oCv->list_fields_name))
			{
				$fieldname = $oCv->list_fields_name[$name];
			}
		}
		if($fieldname == 'accountname' && $module != 'Accounts')
		{
			$fieldname = 'account_id';
		}
		if($fieldname == 'lastname' &&($module == 'SalesOrder'|| $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes'||$module == 'Calendar'))
			$fieldname = 'contact_id';

		if($fieldname == 'productname' && $module != 'Products')
		{
			$fieldname = 'product_id';
		}

		array_push($field_list, $fieldname);
		$j++;
	}
	$field=Array();
	if($is_admin==false)
	{
		if($module == 'Emails')
		{
			$query  = "SELECT fieldname FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2)";
			$params = array($tabid);
		}
		else
		{
			$profileList = getCurrentUserProfileList();
			$params = array();
			$query  = "SELECT DISTINCT vtiger_field.fieldname
			FROM vtiger_field
			INNER JOIN vtiger_profile2field
			ON vtiger_profile2field.fieldid = vtiger_field.fieldid
			INNER JOIN vtiger_def_org_field
			ON vtiger_def_org_field.fieldid = vtiger_field.fieldid";

			if($module == "Calendar")
				$query .=" WHERE vtiger_field.tabid in (9,16) and vtiger_field.presence in (0,2)";
			else {
				$query .=" WHERE vtiger_field.tabid = ? and vtiger_field.presence in (0,2)";
				array_push($params, $tabid);
			}

			$query .=" AND vtiger_profile2field.visible = 0
			AND vtiger_profile2field.visible = 0
			AND vtiger_def_org_field.visible = 0
			AND vtiger_profile2field.profileid IN (". generateQuestionMarks($profileList) .")
			AND vtiger_field.fieldname IN (". generateQuestionMarks($field_list) .")";

			array_push($params, $profileList, $field_list);
		}

		$result = $adb->pquery($query, $params);
		for($k=0;$k < $adb->num_rows($result);$k++)
		{
			$field[]=$adb->query_result($result,$k,"fieldname");
		}
	}
	//constructing the uitype and columnname array
	$ui_col_array=Array();

	$params = array();
	$query = "SELECT uitype, columnname, fieldname FROM vtiger_field ";

	if($module == "Calendar")
		$query .=" WHERE vtiger_field.tabid in (9,16) and vtiger_field.presence in (0,2)";
	else {
		$query .=" WHERE vtiger_field.tabid = ? and vtiger_field.presence in (0,2)";
		array_push($params, $tabid);
	}
	$query .= " AND fieldname IN (". generateQuestionMarks($field_list).") ";
	array_push($params, $field_list);

	$result = $adb->pquery($query, $params);
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$tempArr=array();
		$uitype=$adb->query_result($result,$i,'uitype');
		$columnname=$adb->query_result($result,$i,'columnname');
		$field_name=$adb->query_result($result,$i,'fieldname');
		$tempArr[$uitype]=$columnname;
		$ui_col_array[$field_name]=$tempArr;
	}
	//end
	if($navigation_array['start'] !=0)
		for ($i=1; $i<=$noofrows; $i++)
		{
			$list_header =Array();
			//Getting the entityid
			if($module != 'Users')
			{
				$entity_id = $adb->query_result($list_result,$i-1,"crmid");
				$owner_id = $adb->query_result($list_result,$i-1,"smownerid");
			}else
			{
				$entity_id = $adb->query_result($list_result,$i-1,"id");
			}
			// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
			// begin: Armando Lüscher 05.07.2005 -> §priority
			// Code contri buted by fredy Desc: Set Priority color
			$priority = $adb->query_result($list_result,$i-1,"priority");

			$font_color_high = "color:#00DD00;";
			$font_color_medium = "color:#DD00DD;";
			$P_FONT_COLOR = "";
			switch ($priority)
			{
				case 'High':
					$P_FONT_COLOR = $font_color_high;
					break;
				case 'Medium':
					$P_FONT_COLOR = $font_color_medium;
					break;
				default:
					$P_FONT_COLOR = "";
			}
			//end: Armando Lüscher 05.07.2005 -> §priority
			foreach($focus->list_fields as $name=>$tableinfo)
			{
				$fieldname = $focus->list_fields_name[$name];

				//added for vtiger_customview 27/5
				if($oCv) {
					if(isset($oCv->list_fields_name)) {
						$fieldname = $oCv->list_fields_name[$name];
						if($fieldname == 'accountname' && $module != 'Accounts') {
							$fieldname = 'account_id';
						}
						if($fieldname == 'lastname' &&($module == 'SalesOrder'|| $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes'||$module == 'Calendar' )) {
							$fieldname = 'contact_id';
						}
						if($fieldname == 'productname' && $module != 'Products') {
							$fieldname = 'product_id';
						}
					} else {
						$fieldname = $focus->list_fields_name[$name];
					}
				} else {
					$fieldname = $focus->list_fields_name[$name];
					if($fieldname == 'accountname' && $module != 'Accounts') {
						$fieldname = 'account_id';
					}
					if($fieldname == 'lastname' && ($module == 'SalesOrder'|| $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes'|| $module == 'Calendar')) {
						$fieldname = 'contact_id';
					}
					if($fieldname == 'productname' && $module != 'Products') {
						$fieldname = 'product_id';
					}
				}
				if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0 || in_array($fieldname,$field) || $fieldname == '' || ($name=='Close' && $module=='Calendar')) {
					if($fieldname == '') {
						$table_name = '';
						$column_name = '';
						foreach($tableinfo as $tablename=>$colname) {
							$table_name=$tablename;
							$column_name = $colname;
						}
						$value = $adb->query_result($list_result,$i-1,$colname);
					}
					else {
						if($module == 'Calendar') {
							$act_id = $adb->query_result($list_result,$i-1,"activityid");

							$cal_sql = "select activitytype from vtiger_activity where activityid=?";
							$cal_res = $adb->pquery($cal_sql,array($act_id));
							if($adb->num_rows($cal_res)>=0)
								$activitytype = $adb->query_result($cal_res,0,"activitytype");
						}
						if(($module == 'Calendar' || $module == 'Emails' || $module == 'HelpDesk' || $module == 'Invoice' || $module == 'Leads' || $module == 'Contacts') && (($fieldname=='parent_id') || ($name=='Contact Name') || ($name=='Close') || ($fieldname == 'firstname'))) {
							if($module == 'Calendar'){
								if($fieldname=='status'){
									if($activitytype == 'Task'){
										$fieldname='taskstatus';
									} else {
										$fieldname='eventstatus';
									}
								}
								if($activitytype == 'Task' ) {
									if(getFieldVisibilityPermission('Calendar',$current_user->id,$fieldname) == '0'){
										$has_permission = 'yes';
									} else {
										$has_permission = 'no';
									}
								} else {
									if(getFieldVisibilityPermission('Events',$current_user->id,$fieldname) == '0'){
										$has_permission = 'yes';
									} else {
										$has_permission = 'no';
									}
								}
							}
							if($module != 'Calendar' || ($module == 'Calendar' && $has_permission == 'yes')) {
								if ($fieldname=='parent_id') {
									$value=getRelatedTo($module,$list_result,$i-1);
								}
								if($name=='Contact Name') {
									$contact_id = $adb->query_result($list_result,$i-1,"contactid");
									$contact_name = getFullNameFromQResult($list_result,$i-1,"Contacts");
									$value="";
									//Added to get the contactname for activities custom view - t=2190
									if($contact_id != '' && !empty($contact_name)) {
										$contact_name = getContactName($contact_id);
									}
									if(($contact_name != "") && ($contact_id !='NULL')) {
										// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
										$value =  $contact_name; // Armando Lüscher 05.07.2005 -> §priority -> Desc: inserted style="$P_FONT_COLOR"
									}
								}
								if($fieldname == "firstname") {
									$first_name = textlength_check($adb->query_result($list_result,$i-1,"firstname"));
									$value =$first_name;
								}

								if ($name == 'Close') {
									$status = $adb->query_result($list_result,$i-1,"status");
									$activityid = $adb->query_result($list_result,$i-1,"activityid");
									if(empty($activityid)){
										$activityid = $adb->query_result($list_result, $i-1, "tmp_activity_id");
									}
									$activitytype = $adb->query_result($list_result,$i-1,"activitytype");
									// TODO - Picking activitytype when it is not present in the Custom View.
									// Going forward, this column should be added to the select list if not already present as a performance improvement.
									if (empty($activitytype)) {
										$activitytypeRes = $adb->pquery('SELECT activitytype FROM vtiger_activity WHERE activityid=?', array($activityid));
										if ($adb->num_rows($activitytypeRes) > 0) {
											$activitytype = $adb->query_result($activitytypeRes, 0, 'activitytype');
										}
									}
									if ($activitytype != 'Task' && $activitytype != 'Emails') {
										$eventstatus = $adb->query_result($list_result,$i-1,"eventstatus");
										if(isset($eventstatus)) {
											$status = $eventstatus;
										}
									}
									if($status =='Deferred' || $status == 'Completed' || $status == 'Held' || $status == '') {
										$value="";
									} else {
										if($activitytype=='Task')
											$evt_status='&status=Completed';
										else
											$evt_status='&eventstatus=Held';
									}
								}

							} else {
								$value = "";
							}
						} elseif($module == "Documents" && ($fieldname == 'filelocationtype' || $fieldname == 'filename' || $fieldname == 'filesize' || $fieldname == 'filestatus' || $fieldname == 'filetype')) {
							$value = $adb->query_result($list_result,$i-1,$fieldname);
							if($fieldname == 'filelocationtype') {
								if($value == 'I')
									$value = getTranslatedString('LBL_INTERNAL',$module);
								elseif($value == 'E')
								$value = getTranslatedString('LBL_EXTERNAL',$module);
								else
									$value = ' --';
							}
							if($fieldname == 'filename') {
								$downloadtype = $adb->query_result($list_result,$i-1,'filelocationtype');
								if($downloadtype == 'I') {
									$fld_value = $value;
									$ext_pos = strrpos($fld_value, ".");
									$ext =substr($fld_value, $ext_pos + 1);
									$ext = strtolower($ext);
									if($value != ''){
										if($ext == 'bin' || $ext == 'exe' || $ext == 'rpm')
											$fileicon="<img src='" . vtiger_imageurl('fExeBin.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
										elseif($ext == 'jpg' || $ext == 'gif' || $ext == 'bmp')
										$fileicon="<img src='" . vtiger_imageurl('fbImageFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
										elseif($ext == 'txt' || $ext == 'doc' || $ext == 'xls')
										$fileicon="<img src='" . vtiger_imageurl('fbTextFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
										elseif($ext == 'zip' || $ext == 'gz' || $ext == 'rar')
										$fileicon="<img src='" . vtiger_imageurl('fbZipFile.gif', $theme) . "' hspace='3' align='absmiddle'	border='0'>";
										else
											$fileicon="<img src='" . vtiger_imageurl('fbUnknownFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
									}
								} elseif($downloadtype == 'E') {
									if(trim($value) != '' ) {
										$fld_value = $value;
										$fileicon = "<img src='" . vtiger_imageurl('fbLink.gif', $theme) . "' alt='".getTranslatedString('LBL_EXTERNAL_LNK',$module)."' title='".getTranslatedString('LBL_EXTERNAL_LNK',$module)."' hspace='3' align='absmiddle' border='0'>";
									}
									else {
										$fld_value = '--';
										$fileicon = '';
									}
								} else {
									$fld_value = ' --';
									$fileicon = '';
								}

								$file_name = $adb->query_result($list_result,$i-1,'filename');
								$notes_id = $adb->query_result($list_result,$i-1,'crmid');
								$folder_id = $adb->query_result($list_result,$i-1,'folderid');
								$download_type = $adb->query_result($list_result,$i-1,'filelocationtype');
								$file_status = $adb->query_result($list_result,$i-1,'filestatus');
								$fileidQuery = "select attachmentsid from vtiger_seattachmentsrel where crmid=?";
								$fileidres = $adb->pquery($fileidQuery,array($notes_id));
								$fileid = $adb->query_result($fileidres,0,'attachmentsid');
								if($file_name != '' && $file_status == 1) {
									if($download_type == 'I' ) {
										$fld_value = "<a href='index.php?module=uploads&action=downloadfile&entityid=$notes_id&fileid=$fileid' title='".getTranslatedString("LBL_DOWNLOAD_FILE",$module)."' onclick='javascript:dldCntIncrease($notes_id);'>".$fld_value."</a>";
									} elseif($download_type == 'E') {
										$fld_value = "<a target='_blank' href='$file_name' onclick='javascript:dldCntIncrease($notes_id);' title='".getTranslatedString("LBL_DOWNLOAD_FILE",$module)."'>".$fld_value."</a>";
									} else {
										$fld_value = ' --';
									}
								}
								$value = $fileicon.$fld_value;
							}
							if($fieldname == 'filesize') {
								$downloadtype = $adb->query_result($list_result,$i-1,'filelocationtype');
								if($downloadtype == 'I') {
									$filesize = $value;
									if($filesize < 1024)
										$value=$filesize.' B';
									elseif($filesize > 1024 && $filesize < 1048576)
									$value=round($filesize/1024,2).' KB';
									else if($filesize > 1048576)
										$value=round($filesize/(1024*1024),2).' MB';
								} else {
									$value = ' --';
								}
							}
							if($fieldname == 'filestatus') {
								$filestatus = $value;
								if($filestatus == 1)
									$value=getTranslatedString('yes',$module);
								elseif($filestatus == 0)
								$value=getTranslatedString('no',$module);
								else
									$value=' --';
							}
							if($fieldname == 'filetype') {
								$downloadtype = $adb->query_result($list_result,$i-1,'filelocationtype');
								$filetype = $adb->query_result($list_result,$i-1,'filetype');
								if($downloadtype == 'E' || $downloadtype != 'I') {
									$value = ' --';
								} else
									$value = $filetype;
							}
							if($fieldname == 'notecontent') {
								$value = decode_html($value);
								$value = textlength_check($value);
							}
						} elseif($module == "Products" && $name == "Related to") {
							$value=getRelatedTo($module,$list_result,$i-1);
						} elseif($name=='Contact Name' && ($module =='SalesOrder' || $module == 'Quotes' || $module == 'PurchaseOrder')) {
							if($name == 'Contact Name') {
								$contact_id = $adb->query_result($list_result,$i-1,"contactid");
								$contact_name = getFullNameFromQResult($list_result, $i-1,"Contacts");
								$value="";
								if(($contact_name != "") && ($contact_id !='NULL'))
									$value =$contact_name;
							}
						} elseif($name == 'Product') {
							$product_id = textlength_check($adb->query_result($list_result,$i-1,"productname"));
							$value =  $product_id;
						} elseif($name=='Account Name') {
							//modified for vtiger_customview 27/5
							if($module == 'Accounts') {
								$account_id = $adb->query_result($list_result,$i-1,"crmid");
								//$account_name = getAccountName($account_id);
								$account_name = textlength_check($adb->query_result($list_result,$i-1,"accountname"));
								// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
								$value = $account_name; // Armando Lüscher 05.07.2005 -> §priority -> Desc: inserted style="$P_FONT_COLOR"
							} elseif($module == 'Potentials' || $module == 'Contacts' || $module == 'Invoice' || $module == 'SalesOrder' || $module == 'Quotes') { //Potential,Contacts,Invoice,SalesOrder & Quotes  records   sort by Account Name
								//$accountname = textlength_check($adb->query_result($list_result,$i-1,"accountname"));
								$accountid = $adb->query_result($list_result,$i-1,"accountid");
								$accountname = textlength_check(getAccountName($accountid));
								$value = $accountname;
							} else {
								$account_id = $adb->query_result($list_result,$i-1,"accountid");
								$account_name = getAccountName($account_id);
								$acc_name = textlength_check($account_name);
								// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
								$value = $acc_name; // Armando Lüscher 05.07.2005 -> §priority -> Desc: inserted style="$P_FONT_COLOR"
							}
						} elseif(( $module == 'HelpDesk' || $module == 'PriceBook' || $module == 'Quotes' || $module == 'PurchaseOrder' || $module == 'Faq') && $name == 'Product Name') {
							if($module == 'HelpDesk' || $module == 'Faq')
								$product_id = $adb->query_result($list_result,$i-1,"product_id");
							else
								$product_id = $adb->query_result($list_result,$i-1,"productid");

							if($product_id != '')
								$product_name = getProductName($product_id);
							else
								$product_name = '';

							$value = textlength_check($product_name);
						} elseif(($module == 'Quotes' && $name == 'Potential Name') || ($module == 'SalesOrder' && $name == 'Potential Name')) {
							$potential_id = $adb->query_result($list_result,$i-1,"potentialid");
							$potential_name = getPotentialName($potential_id);
							$value = textlength_check($potential_name);
						} elseif($module =='Emails' && $relatedlist != '' && ($name=='Subject' || $name=='Date Sent' || $name == 'To')) {
							$list_result_count = $i-1;
							$tmp_value = getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"list","",$returnset,$oCv->setdefaultviewid,false);
							$tmp_value = evvt_strip_html_links($tmp_value);
							$value = textlength_check($tmp_value);
							if($name == 'Date Sent') {
								$sql="select email_flag from vtiger_emaildetails where emailid=?";
								$result=$adb->pquery($sql, array($entity_id));
								$email_flag=$adb->query_result($result,0,"email_flag");
								if($email_flag != 'SAVED') {
									$value = getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"list","",$returnset,$oCv->setdefaultviewid,false);
									$value = evvt_strip_html_links($value);
								} else
									$value = '';
							}
						} elseif($module == 'Calendar' && ($fieldname!='taskstatus' && $fieldname!='eventstatus')) {
							if($activitytype == 'Task' ) {
								if(getFieldVisibilityPermission('Calendar',$current_user->id,$fieldname) == '0'){
									$list_result_count = $i-1;
									$value = getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"list","",$returnset,$oCv->setdefaultviewid,false);
									$value = evvt_strip_html_links($value);
								} else {
									$value = '';
								}
							} else {
								if(getFieldVisibilityPermission('Events',$current_user->id,$fieldname) == '0'){
									$list_result_count = $i-1;
									$value = getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"list","",$returnset,$oCv->setdefaultviewid,false);
									$value = evvt_strip_html_links($value);
								} else {
									$value = '';
								}
							}
						} else {
							$list_result_count = $i-1;
							$value = getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"list","",$returnset,$oCv->setdefaultviewid,false);
							$value = evvt_strip_html_links($value);
						}
					}

					// vtlib customization: For listview javascript triggers
					//$value = "$value <span type='vtlib_metainfo' vtrecordid='{$entity_id}' vtfieldname='{$fieldname}' vtmodule='$module' style='display:none;'></span>";
					// END

					if($module == "Calendar" && $name == $app_strings['Close'])
					{
						if(isPermitted("Calendar","EditView") == 'yes')
						{
							if((getFieldVisibilityPermission('Events',$current_user->id,'eventstatus') == '0') || (getFieldVisibilityPermission('Calendar',$current_user->id,'taskstatus') == '0'))
							{
								array_push($list_header,$value);
							}
						}
					}
					else
						$list_header[] = $value;

				}
			}
			$varreturnset = '';

			$varreturnset = $returnset;
			$webserviceEntityId=vtyiicpng_getWSEntityId($module);
			$list_header[]=$webserviceEntityId.$entity_id;
			$list_header[]=$module;
			$list_block[$entity_id] = $list_header;

		}
		$log->debug("Exiting getSearchingListViewEntries method ...");
		return $list_block;
}

function getReferenceAutocomplete($term, $filter, $searchinmodules, $limit, $user) {
	global $current_user,$log,$adb;

	if (!empty($searchinmodules)) {
		$searchin = explode(',', $searchinmodules);
	} else {
		$searchin = array('HelpDesk','Project','ProjectTask','Potentials','ProjectMilestone',
		'Invoice','PurchaseOrder','Quotes','SalesOrder','ServiceContracts','Accounts','Contacts',);
	}
	if (empty($limit)) $limit = 30;  // hard coded default
	$respuesta=array();

	if (empty($term)) {
		$term='%';
		$op='like';
	} else {
		switch ($filter) {
			case 'eq':
				$op='=';
				break;
			case 'neq':
				$op='!=';
				break;
			case 'startswith':
				$term=$term.'%';
				$op='like';
				break;
			case 'endswith':
				$term='%'.$term;
				$op='like';
				break;
			case 'contains':
				$op='like';
				$term='%'.$term.'%';
				break;
			default: $op='='; break;
		}
	}

	foreach ($searchin as $srchmod) {
		if (!(vtlib_isModuleActive($srchmod) and isPermitted($srchmod,'DetailView'))) continue;
		$eirs = $adb->pquery('select fieldname,tablename,entityidfield from vtiger_entityname where modulename=?',array($srchmod));
		$ei = $adb->fetch_array($eirs);
		$fieldsname = $ei['fieldname'];
		$wherefield = $ei['fieldname']." $op '$term' ";
		if (!(strpos($fieldsname, ',') === false)) {
			$fieldlists = explode(',', $fieldsname);
			$fieldsname = "concat(";
			$fieldsname = $fieldsname . implode(",' ',", $fieldlists);
			$fieldsname = $fieldsname . ")";
			$wherefield = implode(" $op '$term' or ", $fieldlists)." $op '$term' ";
		}
		$qry = "select crmid,$fieldsname as crmname
				from {$ei['tablename']}
				inner join vtiger_crmentity on crmid = {$ei['entityidfield']}
				where deleted = 0 and ($wherefield)";
		$rsemp=$adb->query($qry);
		$trmod = getTranslatedString($srchmod,$srchmod);
		$wsid = vtyiicpng_getWSEntityId($srchmod);
		while ($emp=$adb->fetch_array($rsemp)) {
			$respuesta[]=array(
					'crmid'=>$wsid.$emp['crmid'],
					'crmname'=>$emp['crmname']." :: $trmod",
					'crmmodule'=>$srchmod,
			);
			if (count($respuesta)>=$limit) break;
		}
	}
	return $respuesta;
}

?>
