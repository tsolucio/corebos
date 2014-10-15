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
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/Emails/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/
 
$mod_strings = Array(
// Mike Crowe Mod --------------------------------------------------------added for general search
'LBL_GENERAL_INFORMATION'=>'Algemene Informatie',

'LBL_MODULE_NAME'=>'E-mails',
'LBL_MODULE_TITLE'=>'E-mails: Home',
'LBL_SEARCH_FORM_TITLE'=>'Zoek e-mail',
'LBL_LIST_FORM_TITLE'=>'E-mail lijst',
'LBL_NEW_FORM_TITLE'=>'Volg e-mail',

'LBL_LIST_SUBJECT'=>'Onderwerp',
'LBL_LIST_CONTACT'=>'Contact',
'LBL_LIST_RELATED_TO'=>'Gerelateerd aan',
'LBL_LIST_DATE'=>'Datum verzonden',
'LBL_LIST_TIME'=>'Verzonden om',

'ERR_DELETE_RECORD'=>"Een veld moet gespecificeerd zijn om een account te verwijderen.",
'LBL_DATE_SENT'=>'Datum verzonden:',
'LBL_SUBJECT'=>'Onderwerp:',
'LBL_BODY'=>'Inhoud:',
'LBL_DATE_AND_TIME'=>'Datum & tijd verzonden:',
'LBL_DATE'=>'Datum verzonden:',
'LBL_TIME'=>'Verzonden om:',
'LBL_SUBJECT'=>'Onderwerp:',
'LBL_BODY'=>'Inhoud:',
'LBL_CONTACT_NAME'=>' Contactnaam: ',
'LBL_EMAIL'=>'E-mail:',
'LBL_DETAILVIEW_EMAIL'=>'E-mail', 

'LBL_COLON'=>':',
'LBL_CHK_MAIL'=>'Bekijk e-mail',
'LBL_COMPOSE'=>'Nieuwe e-mail',
//Single change for 5.0.3
'LBL_SETTINGS'=>'Instellingen',
'LBL_EMAIL_FOLDERS'=>'E-mail mappen',
'LBL_INBOX'=>'Postvak In',
'LBL_SENT_MAILS'=>'Verzonden Items',
'LBL_TRASH'=>'Prullenbak',
'LBL_JUNK_MAILS'=>'SPAM',
'LBL_TO_LEADS'=>'Aan leads',
'LBL_TO_CONTACTS'=>'Aan contacten',
'LBL_TO_ACCOUNTS'=>'Aan accounts',
'LBL_MY_MAILS'=>'Mijn e-mails',
'LBL_QUAL_CONTACT'=>'Gekwalificeerde e-mails (als contacten)',
'LBL_MAILS'=>'E-mails',
'LBL_QUALIFY_BUTTON'=>'Kwalificeer',
'LBL_REPLY_BUTTON'=>'Reageer',
'LBL_FORWARD_BUTTON'=>'Doorsturen',
'LBL_DOWNLOAD_ATTCH_BUTTON'=>'Download bijlagen',
'LBL_FROM'=>'Van :',
'LBL_CC'=>'Cc :',
'LBL_BCC'=>'Bcc :',

'NTC_REMOVE_INVITEE'=>'Wilt u dit adres verwijderen van deze e-mail?',
'LBL_INVITEE'=>'Ontvangers',

// Added Fields
// Contacts-SubPanelViewContactsAndUsers.php
'LBL_BULK_MAILS'=>'Bulk e-mails',
'LBL_ATTACHMENT'=>'Bijlage',
'LBL_UPLOAD'=>'Upload',
'LBL_FILE_NAME'=>'Bestandsnaam',
'LBL_SEND'=>'Verzenden',

'LBL_EMAIL_TEMPLATES'=>'E-mail sjablonen',
'LBL_TEMPLATE_NAME'=>'Naam sjabloon',
'LBL_DESCRIPTION'=>'Omschrijving',
'LBL_EMAIL_TEMPLATES_LIST'=>'E-mail sjablonenlijst',
'LBL_EMAIL_INFORMATION'=>'E-mail informatie',




//for v4 release added
'LBL_NEW_LEAD'=>'Nieuwe Lead',
'LBL_LEAD_TITLE'=>'Leads',

'LBL_NEW_PRODUCT'=>'Nieuw Product',
'LBL_PRODUCT_TITLE'=>'Producten',
'LBL_NEW_CONTACT'=>'Nieuw contact',
'LBL_CONTACT_TITLE'=>'Contacten',
'LBL_NEW_ACCOUNT'=>'Nieuw account',
'LBL_ACCOUNT_TITLE'=>'Accounts',

// Added fields after vtiger4 - Beta
'LBL_USER_TITLE'=>'Gebruiker',
'LBL_NEW_USER'=>'Nieuwe gebruiker',

// Added for 4 GA
'LBL_TOOL_FORM_TITLE'=>'E-mail Gereedschap',
//Added for 4GA
'Date & Time Sent'=>'Datum & tijd verzonden',
'Sales Enity Module'=>'Verkoop module',
'Activtiy Type'=>'Type activiteiten',
'Related To'=>'Gerelateerd aan',
'Assigned To'=>'Toegevoegd aan',
'Subject'=>'Onderwerp',
'Attachment'=>'Bijlage',
'Description'=>'Omschrijving',
'Time Start'=>'Starttijd',
'Created Time'=>'Gemaakt',
'Modified Time'=>'Gewijzigd',

'MESSAGE_CHECK_MAIL_SERVER_NAME'=>'Controleer uw e-mail server naam...',
'MESSAGE_CHECK_MAIL_ID'=>'Controleer het e-mailadres of "Toegevoegd aan" gebruiker...',
'MESSAGE_MAIL_HAS_SENT_TO_USERS'=>'E-mail is verstuurd aan de volgende gebruiker(s) :',
'MESSAGE_MAIL_HAS_SENT_TO_CONTACTS'=>'E-mail is verstuurd aan de volgende contact(en) :',
'MESSAGE_MAIL_ID_IS_INCORRECT'=>'E-mailadres is niet correct....',
'MESSAGE_ADD_USER_OR_CONTACT'=>'Gebruiker(s) of contact(en)... toevoegen a.u.b.',
'MESSAGE_MAIL_SENT_SUCCESSFULLY'=>' e-mail(s) zijn verstuurd!',

// Added for web mail post 4.0.1 release
'LBL_FETCH_WEBMAIL'=>'Webmail Ophalen',
//Added for 4.2 Release -- CustomView
'LBL_ALL'=>'Allemaal',
'MESSAGE_CONTACT_NOT_WANT_MAIL'=>'Deze contactpersoon wil geen e-mail ontvangen.',
'LBL_WEBMAILS_TITLE'=>'Webmails',
'LBL_EMAILS_TITLE'=>'E-mails',
'LBL_MAIL_CONNECT_ERROR_INFO'=>'Kan geen verbinding krijgen met de mail server!<br> Controleer in mijn accounts->Mail serverlijst -> mail accountlijst',
'LBL_ALLMAILS'=>'Alle e-mails',
'LBL_TO_USERS'=>'Aan gebruiker',
'LBL_TO'=>'Aan:',
'LBL_IN_SUBJECT'=>'In onderwerp',
'LBL_IN_SENDER'=>'In van',
'LBL_IN_SUBJECT_OR_SENDER'=>'In onderwerp of van',
'CHOSE_EMAIL'=>'Kies e-mailadres',
'Sender'=>'Van',
'LBL_CONFIGURE_MAIL_SETTINGS'=>'Uw inkomende e-mailserver is niet geconfigureerd',
'LBL_MAILSELECT_INFO1'=>'Dit e-mailadres heeft verschillende adressen.',
'LBL_MAILSELECT_INFO2'=>'Selecteer het e-mailadres waar u dit bericht naar toe stuurt.',
'LBL_MULTIPLE'=>'Verschillende',
'LBL_COMPOSE_EMAIL'=>'Nieuwe e-mail',
'LBL_VTIGER_EMAIL_CLIENT'=>'vTiger e-mail Client',

//Added for 5.0.3
'TITLE_VTIGERCRM_MAIL'=>'vTiger CRM e-mail',
'TITLE_COMPOSE_MAIL'=>'Nieuwe e-mail',

'MESSAGE_MAIL_COULD_NOT_BE_SEND'=>'Kan geen e-mail versturen naar de geselecteerde gebruiker.',
'MESSAGE_PLEASE_CHECK_ASSIGNED_USER_EMAILID'=>'Controleer e-mailadres van geselecteerde gebruiker a.u.b. ',
'MESSAGE_PLEASE_CHECK_THE_FROM_MAILID'=>'Controleer het verzend e-mailadres a.u.b.',
'MESSAGE_MAIL_COULD_NOT_BE_SEND_TO_THIS_EMAILID'=>'Kan geen e-mail versturen naar dit e-mailadres',
'PLEASE_CHECK_THIS_EMAILID'=>'aub Controleer dit e-mailadres',
'LBL_CC_EMAIL_ERROR'=>'Uw cc e-mailadres is niet correct',
'LBL_BCC_EMAIL_ERROR'=>'Uw bcc e-mailadres is niet correct',
'LBL_NO_RCPTS_EMAIL_ERROR'=>'Geen ontvangers weergegeven',
'LBL_CONF_MAILSERVER_ERROR'=>'Configureer uw uitgaande mailserver selecteer settings ---> uitgaande server link a.u.b. ',
'LBL_VTIGER_EMAIL_CLIENT'=>'vTiger e-mail Client',
'LBL_MAILSELECT_INFO3'=>'U heeft geen permissie om de e-mailadressen van de geselecteerde velden te bekijken.',
//Added  for script alerts
'FEATURE_AVAILABLE_INFO' => 'Dit kenmerk is momenteel alleen beschikbaar voor Microsoft Internet Explorer 5.5+!',
'DOWNLOAD_CONFIRAMATION' => 'Wilt u het bestand downloaden?',
'LBL_PLEASE_ATTACH' => 'Een geldig bestand bijvoegen en opnieuw proberen a.u.b.!',
'LBL_KINDLY_UPLOAD' => 'aub configureren <font color="red">upload_tmp_dir</font> variabel in php.ini bestand.',
'LBL_EXCEED_MAX' => 'Sorry, het bestand overschrijft het maximum limiet. Een kleiner bestand proberen a.u.b.',
'LBL_BYTES' => ' bytes',
'LBL_CHECK_USER_MAILID' => 'Controleer het huidige e-mailadres van de gebruiker. Het moet een geldig e-mailadres zijn om e-mails te versturen',

// Added/Updated for vtiger CRM 5.0.4
'Activity Type'=>'Activiteit Type',
'LBL_MAILSELECT_INFO'=>'heeft de volgende e-mail IDs gekoppeld. Selecteer de e-mailadressen die gebruikt moeten worden',
'LBL_NO_RECORDS' => 'Niets gevonden',
'LBL_PRINT_EMAIL'=> 'Afdrukken',



);

?>
