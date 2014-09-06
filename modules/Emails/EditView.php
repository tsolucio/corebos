<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Emails/EditView.php,v 1.25 2005/04/18 10:37:49 samk Exp $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once("include/Zend/Json.php");

global $log;
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $current_user;
global $currentModule;
global $default_charset;

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();
$json = new Zend_Json();
$smarty->assign("UPLOADSIZE", $upload_maxsize/1000000); // Convert to MB
if($_REQUEST['upload_error'] == true)
{
        echo '<br><b><font color="red"> The selected file has no data or a invalid file.</font></b><br>';
}

//Email Error handling
if($_REQUEST['mail_error'] != '') 
{
	require_once("modules/Emails/mail.php");
	echo parseEmailErrorString($_REQUEST['mail_error']);
}
//added to select the module in combobox of compose-popup
if(isset($_REQUEST['par_module']) && $_REQUEST['par_module']!=''){
	$smarty->assign('select_module',vtlib_purify($_REQUEST['par_module']));
}
elseif(isset($_REQUEST['pmodule']) && $_REQUEST['pmodule']!='') {
	$smarty->assign('select_module',vtlib_purify($_REQUEST['pmodule']));	
}

if(isset($_REQUEST['record']) && $_REQUEST['record'] !='') 
{
	$focus->id = $_REQUEST['record'];
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($_REQUEST['record'],"Emails");
	$query = 'select idlists,from_email,to_email,cc_email,bcc_email from vtiger_emaildetails where emailid =?';
	$result = $adb->pquery($query, array($focus->id));
	$from_email = $adb->query_result($result,0,'from_email');
	$smarty->assign('FROM_MAIL',$from_email);	
	$to_email = implode(',',$json->decode($adb->query_result($result,0,'to_email')));
	$smarty->assign('TO_MAIL',$to_email);
	$cc_add = implode(',',$json->decode($adb->query_result($result,0,'cc_email')));
	$smarty->assign('CC_MAIL',$cc_add);
	$bcc_add = implode(',',$json->decode($adb->query_result($result,0,'bcc_email')));
	$smarty->assign('BCC_MAIL',$bcc_add);
	$idlist = $adb->query_result($result,0,'idlists');
	$smarty->assign('IDLISTS',$idlist);
	$log->info("Entity info successfully retrieved for EditView.");
	$focus->name=$focus->column_fields['name'];
}
elseif(isset($_REQUEST['sendmail']) && $_REQUEST['sendmail'] !='')
{
	
	$mailids = get_to_emailids($_REQUEST['pmodule']);
	if($mailids['mailds'] != '')
		$to_add = trim($mailids['mailds'],",").",";
	$smarty->assign('TO_MAIL',$to_add);
	$smarty->assign('IDLISTS',$mailids['idlists']);	
	$focus->mode = '';
}
elseif(!empty($_REQUEST['invmodid'])) 
{
	$crmid = vtlib_purify($_REQUEST['invmodid']);
	switch (getSalesEntityType($crmid)) {
		case 'Invoice':
			$emailcrmid=$adb->query_result($adb->pquery('select case accountid when 0 then contactid else accountid end from vtiger_invoice where invoiceid=?',array($crmid)),0,0);
			break;
		case 'Quotes':
			$emailcrmid=$adb->query_result($adb->pquery('select case accountid when 0 then contactid else accountid end from vtiger_quotes where quoteid=?',array($crmid)),0,0);
			break;
		case 'SalesOrder':
			$emailcrmid=$adb->query_result($adb->pquery('select case accountid when 0 then contactid else accountid end from vtiger_salesorder where salesorderid=?',array($crmid)),0,0);
			break;
		case 'PurchaseOrder':
			$emailcrmid=$adb->query_result($adb->pquery('select case vendorid when 0 then contactid else vendorid end from vtiger_purchaseorder where purchaseorderid=?',array($crmid)),0,0);
			break;
	}
	$pmodule = getSalesEntityType($emailcrmid);
	switch ($pmodule) {
		case 'Accounts':
			$_REQUEST["field_lists"]=9;
			break;
		case 'Contacts':
			$_REQUEST["field_lists"]=80;
			break;
		case 'Vendors':
			$_REQUEST["field_lists"]=292;
			break;
	}
	$_REQUEST["idlist"]=$emailcrmid;
	$mailids = get_to_emailids($pmodule);
	if($mailids['mailds'] != '')
		$to_add = trim($mailids['mailds'],",").",";
	$smarty->assign('TO_MAIL',$to_add);
	$smarty->assign('IDLISTS',$mailids['idlists']);
	setObjectValuesFromRequest($focus);
	$focus->mode = '';
}

// INTERNAL MAILER
if($_REQUEST["internal_mailer"] == "true") {
	$smarty->assign('INT_MAILER',"true");
	$rec_type = $_REQUEST["type"];
	$rec_id = $_REQUEST["rec_id"];
	$fieldname = $_REQUEST["fieldname"];
	
	//added for getting list-ids to compose email popup from list view(Accounts,Contacts,Leads)
	if(isset($_REQUEST['field_id']) && strlen($_REQUEST['field_id']) != 0) {
	     if($_REQUEST['par_module'] == "Users")
		$id_list = $_REQUEST['rec_id'].'@'.'-1|';
	     else
                $id_list = $_REQUEST['rec_id'].'@'.$_REQUEST['field_id'].'|';
             $smarty->assign("IDLISTS", $id_list);
        }
	if($rec_type == "record_id") {
		$type = vtlib_purify($_REQUEST['par_module']);
		//check added for email link in user detail view
		$module_focus = Vtiger_Module::getInstance($type);
		$field_focus = Vtiger_Field::getInstance($fieldname,$module_focus);
		$q = "select $fieldname from " . $field_focus->table . " where " . $module_focus->basetableid. "= ?";
		$email1 = $adb->query_result($adb->pquery($q, array($rec_id)),0,$fieldname);
	} elseif ($rec_type == "email_addy") {
		$email1 = vtlib_purify($_REQUEST["email_addy"]);
	}

	$smarty->assign('TO_MAIL',trim($email1,",").",");
}

//handled for replying emails
if($_REQUEST['reply'] == "true")
{
		$fromadd = $_REQUEST['record'];	
		$query = "select from_email,idlists,cc_email,bcc_email from vtiger_emaildetails where emailid =?";
		$result = $adb->pquery($query, array($fromadd));
		$from_mail = $adb->query_result($result,0,'from_email');	
		$smarty->assign('TO_MAIL',trim($from_mail,",").',');
		$cc_add = implode(',',$json->decode($adb->query_result($result,0,'cc_email')));
		$smarty->assign('CC_MAIL',$cc_add);
		$bcc_add = implode(',',$json->decode($adb->query_result($result,0,'bcc_email')));
		$smarty->assign('BCC_MAIL',$bcc_add);
		$smarty->assign('IDLISTS',preg_replace('/###/',',',$adb->query_result($result,0,'idlists')));
}

// Webmails
if(isset($_REQUEST["mailid"]) && $_REQUEST["mailid"] != "") {
	$mailid = $_REQUEST["mailid"];
	$mailbox = $_REQUEST["mailbox"];
	require_once('include/utils/UserInfoUtil.php');
	require_once("modules/Webmails/Webmails.php");
	require_once("modules/Webmails/MailParse.php");
	require_once('modules/Webmails/MailBox.php');

	$mailInfo = getMailServerInfo($current_user);
	$temprow = $adb->fetch_array($mailInfo);

	$MailBox = new MailBox($mailbox);
	$mbox = $MailBox->mbox;

	$webmail = new Webmails($mbox,$mailid);
	$array_tab = Array();
	$webmail->loadMail($array_tab);
	  $hdr = @imap_headerinfo($mbox, $mailid);
	$smarty->assign('WEBMAIL',"true");
	$smarty->assign('mailid',$mailid);
	$smarty->assign('mailbox',$mailbox);
	$temp_id = $MailBox->boxinfo['mail_id'];
	$smarty->assign('from_add',$temp_id);
	$webmail->subject = utf8_decode(utf8_encode(imap_utf8($webmail->subject)));
	if($_REQUEST["reply"] == "all") {
		$smarty->assign('TO_MAIL',$webmail->from.",");	
		//added to remove the emailid of webmail client from cc list....to fix the issue #3818
                $cc_address = '';
                
                $use_to_header = htmlentities($webmail->to_header, ENT_QUOTES, $default_charset);
                $use_cc_address= htmlentities($hdr->ccaddress, ENT_QUOTES, $default_charset);
                
                $cc_array = explode(',',$use_to_header.','.$use_cc_address);
                for($i=0;$i<count($cc_array);$i++) {
                        if(trim($cc_array[$i]) != trim($temp_id)) {
                                $cc_address .= $cc_array[$i];
                                $cc_address = ($i != (count($cc_array)-1))?($cc_address.','):$cc_address;
                        }
		}
		if(trim($cc_address) != '')
			$cc_address = trim($cc_address,",").",";
		$smarty->assign('CC_MAIL',$cc_address);
		// fix #3818 ends
		/*if(is_array($webmail->cc_list))
		{
			$smarty->assign('CC_MAIL',implode(",",$webmail->cc_list).",".implode(",",$webmail->to));
		}
		else
		{
			//Commenting this to fix #3231
		//	$smarty->assign('CC_MAIL',implode(",",$webmail->to));
		}*/
		if(preg_match("/RE:/i", $webmail->subject))
			$smarty->assign('SUBJECT',$webmail->subject);
		else
			$smarty->assign('SUBJECT',"RE: ".$webmail->subject);

	} elseif($_REQUEST["reply"] == "single"){
		$replyToInfo = $webmail->getReplyToInformation(); 
		if(!empty($replyToInfo)){ 
			$smarty->assign('TO_MAIL',$replyToInfo['name']."<".$webmail->reply_to[0].">".","); 
			$smarty->assign('IDLISTS',$replyToInfo['id'].'@'.$replyToInfo['fieldId'].'|'); 
		} 
		else 
			$smarty->assign('TO_MAIL',$replyToInfo['name']."<".$webmail->reply_to[0].">".","); 
			
		//$smarty->assign('BCC_MAIL',$webmail->to[0]);
		if(preg_match("/RE:/i", $webmail->subject))
			$smarty->assign('SUBJECT',$webmail->subject);
		else
			$smarty->assign('SUBJECT',"RE: ".$webmail->subject);

	} elseif($_REQUEST["forward"] == "true" ) {
		//added for attachment handling
		$attachment_links = Array();
		for($i=0;$i<count($webmail->attname);$i++){
			        $attachment_links[$i] = $webmail->anchor_arr[$i].decode_header($webmail->attname[$i])."</a></br>";
		}
		$smarty->assign('webmail_attachments',$attachment_links);
		if(preg_match("/FW:/i", $webmail->subject))
			$smarty->assign('SUBJECT',$webmail->subject);
		else
			$smarty->assign('SUBJECT',"FW: ".$webmail->subject);
	} 
	$smarty->assign('DESCRIPTION',$webmail->replyBody());
	$focus->mode='';
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$disp_view = getView($focus->mode);
$details = getBlocks($currentModule,$disp_view,$mode,$focus->column_fields);
//changed this below line to view description in all language - bharath
$smarty->assign("BLOCKS",$details[$mod_strings['LBL_EMAIL_INFORMATION']]); 
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",$app_strings['Email']);
//id list of attachments while forwarding
$smarty->assign("ATT_ID_LIST",$att_id_list);

//needed when creating a new email with default values passed in
if (isset($_REQUEST['contact_name']) && is_null($focus->contact_name)) 
{
	$focus->contact_name = vtlib_purify($_REQUEST['contact_name']);
}
if (isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) 
{
	$focus->contact_id = vtlib_purify($_REQUEST['contact_id']);
}
if (isset($_REQUEST['parent_name']) && is_null($focus->parent_name)) 
{
	$focus->parent_name = vtlib_purify($_REQUEST['parent_name']);
}
if (isset($_REQUEST['parent_id']) && is_null($focus->parent_id)) 
{
	$focus->parent_id = vtlib_purify($_REQUEST['parent_id']);
}
if (isset($_REQUEST['parent_type'])) 
{
	$focus->parent_type = vtlib_purify($_REQUEST['parent_type']);
}
if (isset($_REQUEST['filename']) && $_REQUEST['isDuplicate'] != 'true') 
{
        $focus->filename = vtlib_purify($_REQUEST['filename']);
}
elseif (is_null($focus->parent_type)) 
{
	$focus->parent_type = $app_list_strings['record_type_default_key'];
}

$log->info("Email detail view");

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
else $smarty->assign("NAME", "");


if($focus->mode == 'edit')
{
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
	if(((!empty($_REQUEST['forward']) || !empty($_REQUEST['reply'])) &&
			$focus->column_fields['email_flag'] != 'SAVED') || (empty($_REQUEST['forward']) &&
			empty($_REQUEST['reply']) && $focus->column_fields['email_flag'] != 'SAVED')) {
		$mode = '';
	} else {
		$mode = $focus->mode;
	}
	$smarty->assign("MODE", $mode);
}

// Unimplemented until jscalendar language files are fixed

$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

if(isset($_REQUEST['return_module'])) $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
else $smarty->assign("RETURN_MODULE",'Emails');
if(isset($_REQUEST['return_action'])) $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
else $smarty->assign("RETURN_ACTION",'index');
if(isset($_REQUEST['return_id'])) $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("ID", $focus->id);
$smarty->assign("ENTITY_ID", vtlib_purify($_REQUEST["record"]));
$smarty->assign("ENTITY_TYPE",vtlib_purify($_REQUEST["email_directing_module"]));
$smarty->assign("OLD_ID", $old_id );
//Display the RTE or not? -- configure $USE_RTE in config.php
$USE_RTE = vt_hasRTE();
$smarty->assign("USE_RTE",$USE_RTE);

if(empty($focus->filename))
{
        $smarty->assign("FILENAME_TEXT", "");
        $smarty->assign("FILENAME", "");
}
else
{
        $smarty->assign("FILENAME_TEXT", "(".$focus->filename.")");
        $smarty->assign("FILENAME", $focus->filename);
}
if($ret_error == 1) {
	require_once('modules/Webmails/MailBox.php');
	$smarty->assign("RET_ERROR",$ret_error);
	if($ret_parentid != ''){
		$smarty->assign("IDLISTS",$ret_parentid);
	}
	if($ret_toadd != '')
                $smarty->assign("TO_MAIL",$ret_toadd);
	$ret_toadd = '';
	if($ret_subject != '')
		$smarty->assign("SUBJECT",$ret_subject);
	if($ret_ccaddress != '')
        	$smarty->assign("CC_MAIL",$ret_ccaddress);
	if($ret_bccaddress != '')
        	$smarty->assign("BCC_MAIL",$ret_bccaddress);
	if($ret_description != '')
        	$smarty->assign("DESCRIPTION", $ret_description);
	$temp_obj = new MailBox($mailbox);
	$temp_id = $temp_obj->boxinfo['mail_id'];
	if($temp_id != '')
		$smarty->assign('from_add',$temp_id);
}
$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$smarty->display("ComposeEmail.tpl");

?>