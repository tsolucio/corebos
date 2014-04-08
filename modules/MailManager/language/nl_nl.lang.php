<?php

/*******************************************************************************
 * The contents of this file are subject to the following licences:
 * - SugarCRM Public License Version 1.1.2 http://www.sugarcrm.com/SPL
 * - vtiger CRM Public License Version 1.0 
 * You may not use this file except in compliance with the License
 * Software distributed under the License is distributed on an  "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by Vicus are Copyright (C) Vicus.
 * All Rights Reserved.
 * Feel free to use / redistribute these languagefiles under the VPL 1.0.
 * This translations is based on earlier work of: 
 * - IT-Online.nl <www.it-online.nl>
 * - Weltevree.org <www.Weltevree.org>
 ********************************************************************************/

/*******************************************************************************
 * Vicus eBusiness Solutions Version Control
 * @package 	NL-Dutch
 * Description	Dutch language pack for vtiger CRM version 5.3.x
 * @author	$Author: luuk $
 * @version 	$Revision: 1.4 $ $Date: 2011/11/14 17:07:26 $
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/MailManager/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/

$mod_strings = Array (
	'MailManager' => 'Mail Manager',
	
	// Translations for JS (please use the prefix JSLBL in key)
	'JSLBL_Loading_Please_Wait'  => 'Laden, een ogenblik geduld a.u.b.',
	'JSLBL_Loading'              => 'Laden',
	'JSLBL_Settings'             => 'Instellingen',
	'JSLBL_Opening'              => 'Openen',
	'JSLBL_Deleting'              => 'Verwijderen',  
	'JSLBL_Updating'             => 'Bijwerken',
	'JSLBL_Associating'          => 'Koppelen',
	'JSLBL_Saving_And_Verifying' => 'Opslaan & Controleren',
	'JSLBL_Failed_To_Open_Mail'  => 'Niet gelukt om de e-mail te openen',
	'JSLBL_Finding_Relation'     => 'Zoeken naar relatie',
	'JSLBL_Find_Relation_Now'    => 'Zoek relatie nu',
	'JSLBL_Searching'            => 'Zoeken',
	'JSLBL_Searching_Please_Wait'=> 'Zoeken, een ogenblik geduld a.u.b.',
	'JSLBL_Sending'              => 'Verzenden',
	'JSLBL_Replied'              => 'Beantwoord',
	'JSLBL_Failed_To_Send_Mail'  => 'Niet gelukt om de e-mail te versturen',
	'JSLBL_Recepient_Cannot_Be_Empty' => 'Ontvanger mag niet leeg zijn',
	'JSLBL_SendWith_EmptySubject'     => 'Versturen met leeg onderwerp?',
	'JSLBL_Removing'                  => 'Verwijderen ',
	'JSLBL_Choose_Server_Type'        => 'Kies server type',
	'JSLBL_Other'                     => 'Ander',
	'JSLBL_Gmail'                     => 'Gmail',
	'JSLBL_Fastmail'                  => 'Fastmail',
	'JSLBL_Search_For_Email'          => 'Zoek naar e-mail',
	'JSLBL_Nothing_Found'             => 'Niets gevonden',
	'JSLBL_Delete_Confirm'            =>'Wilt u de e-mails permanent verwijderen?',
	'JSLBL_Delete_Mails_Confirm'      =>'Wilt u de e-mails verwijderen?',
	'JSLBL_Receipents_Warning_Message'=>'Selecteer ontvangers a.u.b.',
	'JSLBL_NO_MATCH'				  => 'Geen gelijkenis gevonden',
	'JSLBL_Saving'					=> 'Opslaan',
	'JSLBL_Failed_To_Save_Mail'		=>	'Niet gelukt om de e-mail op te slaan',
	'JSLBL_ATTACHMENT_NOT_DELETED'	=>	'Bijlage kon niet worden verwijderd',
	'JSLBL_UPLOAD_CANCEL'	=> 'Annuleer',
	'JSLBL_UPLOAD_DROPFILES'=> 'Drop bestanden hier op te uploaden',
	'JSLBL_UPLOAD_FILE'=>'Upload',
	'JSLBL_UPLOAD_DELETE'=>'[x]',
	'JSLBL_UPLOAD_FAILED'=>'Mislukt',
	'JSLBL_FILEUPLOAD_LIMIT_EXCEEDED'=>'Bestandsomvang overschreden!!',
	'JSLBL_MAIL_SENT'=>'E-mail verzonden',
	'JSLBL_EMAIL_FORMAT_INCORRECT'=>'Geef een valide e-mailadres a.u.b.',
	'JSLBL_Saving'=>'Opslaan',
	'JSLBL_SaveWith_EmptySubject'=>'Opslaan met leeg onderwerp?',
	'JSLBL_Delete'            =>  'Verwijder',
	'JSLBL_Drafts'=>'Concepten',
	'JSLBL_PASSWORD_CANNOT_BE_EMPTY'=>'Wachtwoord mag niet leeg zijn',
	'JSLBL_SERVERNAME_CANNOT_BE_EMPTY'=>'Servernaam mag niet leeg zijn',
	'JSLBL_USERNAME_CANNOT_BE_EMPTY'=>'Gebruikersnaam mag niet leeg zijn',
	'JSLBL_ACCOUNTNAME_CANNOT_EMPTY'=>'Accountnaam mag niet leeg zijn',
	'JSLBL_FROM'=>'Van:',
	'JSLBL_DATE'=>'Datum: ',
	'JSLBL_SUBJECT'=>'Onderwerp: ',
	'JSLBL_TO'=>'Aan: ',
	'JSLBL_CC'=>'Cc: ',
	'JSLBL_FORWARD_MESSAGE_TEXT'=>'---------- Doorgestuurd bericht ----------',
	'JSLBL_PLEASE_SELECT_ATLEAST_ONE_MAIL'=>'Selecteer ten minste één e-mail a.u.b.',
	'JSLBL_PLEASE_SELECT_ATLEAST_ONE_RECORD'=>'Selecteer ten minste één record a.u.b.',
	'JSLBL_MAIL_MOVED'=>'E-mail(s) verplaatst',
	'JSLBL_MOVING'=>'Verplaatsen E-mail(s)',
	'JSLBL_LOADING_FOLDERS' => 'Mappen laden..',
	'JSLBL_ADD_COMMENT'=>'Toevoegen opmerking',
    'JSLBL_Yahoo'=>'Yahoo',
    'JSLBL_CANNOT_ADD_EMPTY_COMMENT' => 'Commentaar mag niet leeg zijn',
    'JSLBL_NO_EMAILS_SELECTED' => 'Geen E-mails geselecteerd.',
    'JSLBL_ENTER_SOME_VALUE' => 'Voer tekst in om te zoeken',
    'JSLBL_DRAFT_MAIL_SAVED'=>'De e-mail is opgeslagen onder concepten',
    
	// General translations
	'LBL_Folders'         => 'Mappen',
	'LBL_Newer'           => 'Nieuwer',
	'LBL_Older'           => 'Ouder',
	'LBL_No_Mails_Found'  => 'Geen e-mails gevonden.',
	'LBL_Go_Back'         => 'Terug',
	'LBL_Reply_All'       => 'Antwoord Allen',
	'LBL_Reply'           => 'Antwoord',
	'LBL_Mark_As_Unread'  => 'Markeer als ongelezen',
	'LBL_Previous'        => 'vorige',
	'LBL_Next'            => 'volgende',
	'LBL_RELATED_RECORDS' => 'Gerelateerde Records',
	'LBL_Mailbox'         => 'Mailbox',
	'LBL_Outbox'          => 'CRM Outbox',
	'LBL_Like'            => 'zoals',
	'LBL_Mail_Server'     => 'Mailserver naam of IP',
	'LBL_Refresh'         => 'Ververs',
	'LBL_Cancel'          => 'Annuleer',
	'LBL_Send'            => 'Verzend',
	'LBL_Compose'         => 'Nieuwe e-mail',
	'LBL_Forward'         => 'Doorsturen',
	'LBL_Remove'          => 'Verwijder',
	'LBL_Associate'       => 'Associeer',
	'LBL_Create_Contact'  => 'Maak contact',
	'LBL_No_Matching_Record_Found' => 'Geen matchende records gevonden.',
	'LBL_ACTIONS'         => 'Acties',
	'LBL_Search'          => 'Zoek',
	'LBL_Delete'            =>  'Verwijder',
	
	'LBL_Username'             => 'Gebruikers',
	'LBL_Your_Mailbox_Account' => 'Uw mailbox account',
	'LBL_Password'             => 'Wachtwoord',
	'LBL_Account_Password'     => 'account wachtwoord',
	'LBL_Protocol'    => 'Protocol',
	'LBL_Imap2'       => 'IMAP2',
	'LBL_Imap4'       => 'IMAP4',
	'LBL_SSL_Options' => 'SSL Opties',
	'LBL_No_TLS'      => 'Geen TLS',
	'LBL_TLS'         => 'TLS',
	'LBL_SSL'         => 'SSL',
	'LBL_Certificate_Validations' => 'Certificaat Validaties',
	'LBL_Validate_Cert'           => 'Valideer certificaat',
	'LBL_Do_Not_Validate_Cert'    => 'Certificaat niet valideren',
	'LBL_SELECT_ACCOUNT_TYPE'     => 'Selecteer Accounttype',
	
	'LBL_FROM'        => 'Van',
	'LBL_TO'          => 'Aan',
	'LBL_CC'          => 'CC',
	'LBL_BCC'         => 'BCC',
	'LBL_Date'        => 'Datum',
	'LBL_Attachments' => 'Bijlagen',
	'LBL_EMAIL_TEMPLATES_LIST'=>'E-mail sjablonen',
	'LBL_SELECT_EMAIL_TEMPLATE'=>'Selecteer e-mail sjabloon',
	'LBL_ATTACHMENTS'  =>'Bijlage:',
	'LBL_SELECT_DOCUMENTS'=>'Selecteer Documenten',
	'LBL_IN' =>'in',
	'LBL_FIND'=>'Zoek',
	'LBL_SAVE_NOW'=>'Nu Opslaan',
	'LBL_Drafts'=>'Concepten',
 	'LBL_NO_EMAILS_SELECTED' => 'Geen E-mails Geselecteerd.',
	'LBL_SUBJECT'	=>	'Onderwerp',
	'LBL_WRITE_ACCESS_FOR'	=>'Schrijftoegang voor',
	'LBL_READ_ACCESS_FOR'	=>'Leestoegang voor',
	'LBL_MODULE_DENIED' => 'Module geweigerd!',
	'LBL_REFRESH_TIME'=>'VerversTijd',
	'LBL_NONE' => 'Geen',
	'LBL_5_MIN' => '5 Minuten',
	'LBL_10_MIN' => '10 Minuten',
	'LBL_MOVE_TO'=>'Verplaats Naar...',
    'LBL_MAILMANAGER_ADD_Contacts' => 'Contact toevoegen',
    'LBL_MAILMANAGER_ADD_Accounts' => 'Account toevoegen',
    'LBL_MAILMANAGER_ADD_Leads' => 'Lead toevoegen',
    'LBL_MAILMANAGER_ADD_Calendar' => 'Actie toevoegen',
    'LBL_MAILMANAGER_ADD_HelpDesk' => 'Ticket toevoegen',
    'LBL_MAILMANAGER_ADD_Emails' => 'E-mail toevoegen',
    'LBL_MAILMANAGER_ADD_ModComments' => 'Commentaar toevoegen',
    'LBL_ADD' => 'Toevoegen'
);

?>
