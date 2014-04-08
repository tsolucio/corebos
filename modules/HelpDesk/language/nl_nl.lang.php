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
 * @version 	$Revision: 1.5 $ $Date: 2012/02/28 23:40:22 $
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/HelpDesk/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/

$mod_strings = Array(
// Added in release 4.0
'LBL_MODULE_NAME'=>'Helpdesk',
'LBL_GROUP'=>'Groep',
'LBL_ACCOUNT_NAME'=>'Accountnaam',
'LBL_CONTACT_NAME'=>'Contactnaam',
'LBL_SUBJECT'=>'Onderwerp',
'LBL_NEW_FORM_TITLE' => 'Nieuwe ticket',
'LBL_DESCRIPTION'=>'Omschrijving',
'NTC_DELETE_CONFIRMATION'=>'Weet u zeker dat u dit veld wilt verwijderen?',
'LBL_CUSTOM_FIELD_SETTINGS'=>'Custom veld instellingen:',
'LBL_PICKLIST_FIELD_SETTINGS'=>'Picklijst veld instellingen:',
'Leads'=>'Lead',
'Accounts'=>'Account',
'Contacts'=>'Contact',
'Opportunities'=>'Verkoopkans',
'LBL_CUSTOM_INFORMATION'=>'Standaard informatie',
'LBL_DESCRIPTION_INFORMATION'=>'Omschrijving',

'LBL_ACCOUNT'=>'Account',
'LBL_OPPURTUNITY'=>'Verkoopkans',
'LBL_PRODUCT'=>'Product',

'LBL_COLON'=>':',
'LBL_TICKET'=>'Ticket',
'LBL_CONTACT'=>'Contact',
'LBL_STATUS'=>'Status',
'LBL_ASSIGNED_TO'=>'Toegewezen aan',
'LBL_FAQ'=>'FAQ',
'LBL_VIEW_FAQS'=>'Bekijk FAQ',
'LBL_ADD_FAQS'=>'Toevoegen FAQ',
'LBL_FAQ_CATEGORIES'=>'FAQ categorieën',

'LBL_PRIORITY'=>'Prioriteiten',
'LBL_CATEGORY'=>'Categorie',

'LBL_ANSWER'=>'Antwoord',
'LBL_COMMENTS'=>'Opmerkingen',

'LBL_AUTHOR'=>'Auteur',
'LBL_QUESTION'=>'Vragen',

//Added fields for File Attachment and Mail send in Tickets
'LBL_ATTACHMENTS'=>'Bijlagen',
'LBL_NEW_ATTACHMENT'=>'Nieuwe bijlage',
'LBL_SEND_MAIL'=>'Verstuur e-mail',

//Added fields for search option  in TicketsList -- 4Beta
'LBL_CREATED_DATE'=>'Gemaakt op',
'LBL_IS'=>'is',
'LBL_IS_NOT'=>'is niet',
'LBL_IS_BEFORE'=>'is voor',
'LBL_IS_AFTER'=>'is na',
'LBL_STATISTICS'=>'Statistieken',
'LBL_TICKET_ID'=>'Ticket Id',
'LBL_MY_TICKETS'=>'Mijn tickets',
"LBL_MY_FAQ"=>"Mijn FAQ",
'LBL_ESTIMATED_FINISHING_TIME'=>'Verwachte eindtijd',
'LBL_SELECT_TICKET'=>'Selecteer ticket',
'LBL_CHANGE_OWNER'=>'Wijzig eigenaar',
'LBL_CHANGE_STATUS'=>'Wijzig Status',
'LBL_TICKET_TITLE'=>'Titel',
'LBL_TICKET_DESCRIPTION'=>'Omschrijving',
'LBL_TICKET_CATEGORY'=>'Categorie',
'LBL_TICKET_PRIORITY'=>'Prioriteit',

//Added fields after 4 -- Beta
'LBL_NEW_TICKET'=>'Nieuw ticket',
'LBL_TICKET_INFORMATION'=>'Ticket informatie',

'LBL_LIST_FORM_TITLE'=>'Ticketlijst',
'LBL_SEARCH_FORM_TITLE'=>'Ticket zoeken',

//Added fields after RC1 - Release
'LBL_CHOOSE_A_VIEW'=>'Kies een ',
'LBL_ALL'=>'Alle',
'LBL_LOW'=>'Laag',
'LBL_MEDIUM'=>'Medium',
'LBL_HIGH'=>'Hoog',
'LBL_CRITICAL'=>'Kritiek',
//Added fields for 4GA
'Assigned To'=>'Toegewezen aan',
'Contact Name'=>'Contactnaam',
'Priority'=>'Prioriteit',
'Status'=>'Status',
'Category'=>'Categorie',
'Update History'=>'Geschiedenis verversen',
'Created Time'=>'Gemaakt',
'Modified Time'=>'Gewijzigd',
'Title'=>'Titel',
'Description'=>'Omschrijving',

'LBL_TICKET_CUMULATIVE_STATISTICS'=>'Ticket Cumulatieve Statistieken:',
'LBL_CASE_TOPIC'=>'Casus Topic',
'LBL_OPEN'=>'Open',
'LBL_CLOSED'=>'Gesloten',
'LBL_TOTAL'=>'Totaal',
'LBL_TICKET_HISTORY'=>'Ticket geschiedenis',
'LBL_CATEGORIES'=>'Categorieen',
'LBL_PRIORITIES'=>'Prioriteiten',
'LBL_SUPPORTERS'=>'Supporters',
//this label for customerportal.
'LBL_STATUS_CLOSED' =>'Closed',//Do not convert this label. This is used to check the status. If the status 'Closed' is changed in vtigerCRM server side then you have to change in customerportal language file also.
'LBL_STATUS_UPDATE' => 'Ticket status is veranderd naar',
'LBL_COULDNOT_CLOSED' => 'Ticket kan niet',
'LBL_CUSTOMER_COMMENTS' => 'De klant heeft de volgende additionele informatie toegevoegd in uw antwoord:',
'LBL_RESPOND'=> 'Gaarne vroegtijdig bericht op bovengenoemde ticket.',
'LBL_REGARDS' =>'Vriendelijke Groeten',
'LBL_SUPPORT_ADMIN' => 'Ondersteuningsbeheerder',
'LBL_RESPONDTO_TICKETID' =>'Antwoord op ticket ID',
'LBL_CUSTOMER_PORTAL' => 'in klanten Portal - SPOED', 
'LBL_LOGIN_DETAILS' => 'Hierbij uw klant Portal login details gegevens:',
'LBL_MAIL_COULDNOT_SENT' =>'E-mail kon niet verstuurd worden',
'LBL_USERNAME' => 'Gebruikersnaam :',
'LBL_PASSWORD' => 'Wachtwoord :',
'LBL_SUBJECT_PORTAL_LOGIN_DETAILS' => 'Met betrekking tot uw klant Portal login gegevens',
'LBL_GIVE_MAILID' => 'Gaarne uw e-mail id',
'LBL_CHECK_MAILID' => 'Gaarne uw e-mail id voor de klanten portal bekijken',
'LBL_LOGIN_REVOKED' => 'Uw login is ingetrokken. Contacteer uw administrator.',
'LBL_MAIL_SENT' => 'Een e-mail is naar uw e-mail id verstuurd met daarin klant portal login gegevens',
'LBL_ALTBODY' => 'Dit is een e-mail pagina met platte tekst voor niet HTML mail clients',

//Added fields after 4_0_1
'LBL_TICKET_RESOLUTION'=>'Oplossing',
'Solution'=>'Oplossing',
'Add Comment'=>'Commentaar toevoegen',
'LBL_ADD_COMMENT'=>'Commentaar toevoegen',//give the same value given to the above string 'Add Comment'

//Added for 4.2 Release -- CustomView
'Ticket ID'=>'Ticket ID',
'Subject'=>'Onderwerp',

//Added after 4.2 alpha
'Severity'=>'Ernstigheid',
'Product Name'=>'Productnaam',
'Related To'=>'Gerelateerd aan',
'LBL_MORE'=>'Meer',

'LBL_TICKETS'=>'Tickets',

//Added on 09-12-2005
'LBL_CUMULATIVE_STATISTICS'=>'Cumulatieve Statistieken',

//Added on 12-12-2005
'LBL_CONVERT_AS_FAQ_BUTTON_TITLE'=>'Converteer naar FAQ',
'LBL_CONVERT_AS_FAQ_BUTTON_KEY'=>'C',
'LBL_CONVERT_AS_FAQ_BUTTON_LABEL'=>'Converteer naar FAQ',
'Attachment'=>'Bijlage',
'LBL_COMMENT_INFORMATION'=>'Commentaar',

//Added for existing picklist entries

'Big Problem'=>'Groot probleem',
'Small Problem'=>'Klein probleem',
'Other Problem'=>'Probleem',

'Low'=>'Laag',
'Normal'=>'Normaal',
'High'=>'Hoog',
'Urgent'=>'Urgent',

'Minor'=>'Klein',
'Major'=>'Groot',
'Feature'=>'Mogelijkheden',
'Critical'=>'Kritiek',

'Open'=>'Open',
'In Progress'=>'In behandeling',
'Wait For Response'=>'Wacht op reactie',
'Closed'=>'Gesloten',

//added to support i18n in ticket mails
'Hi' => 'Hallo',
'Dear'=> 'Beste',
'LBL_PORTAL_BODY_MAILINFO'=> 'Ticket is',
'LBL_DETAIL' => 'de omschrijving is:',
'LBL_REGARDS'=> 'Groeten',
'LBL_TEAM'=> 'HelpDesk team',
'LBL_TICKET_DETAILS' => 'Ticket omschrijving',
'LBL_SUBJECT' => 'Onderwerp: ',
'created' => 'Gemaakt',
'replied' => 'Beantwoord',
'reply'=>'Er is een antwoord naar',
'customer_portal' => 'in "Klanten Portal" van '.$HELPDESK_SUPPORT_NAME.'.',
'link' => 'Men kan de volgende link bekijken om de gemaakte antwoorden te zien:',
'Thanks' => 'Bedankt',
//'Support_team' => 'vTiger Support Team',
'Support_team' => $HELPDESK_SUPPORT_NAME,


// Added/Updated for vtiger CRM 5.0.4

//this label for customerportal.
'LBL_STATUS_CLOSED' =>'Closed',//Do not convert this label. This is used to check the status. If the status 'Closed' is changed in vtigerCRM server side then you have to change in customerportal language file also.
'LBL_STATUS_UPDATE' => 'Ticket status is gewijzigd in',
'LBL_COULDNOT_CLOSED' => 'Ticket kan niet worden',
'LBL_CUSTOMER_COMMENTS' => 'De relatie heeft de volgende informatie geantwoord:',
'LBL_RESPOND'=> 'Gelieve zo snel mogelijk te reageren op bovengenoemd Ticket.',
'LBL_REGARDS' =>'Met vriendelijke groet',
'LBL_SUPPORT_ADMIN' => 'Ondersteuningsbeheerder',
'LBL_RESPONDTO_TICKETID' =>'Beantwoord Ticket ID',
'LBL_CUSTOMER_PORTAL' => 'in Klant Portaal - Dringend',
'LBL_LOGIN_DETAILS' => 'Hieronder volgen uw inloggegevens voor de klantportaal :',
'LBL_MAIL_COULDNOT_SENT' =>'Mail kon niet worden verzonden',
'LBL_USERNAME' => 'Gebruikersnaam :',
'LBL_PASSWORD' => 'Wachtwoord :',
'LBL_SUBJECT_PORTAL_LOGIN_DETAILS' => 'Betreft uw login gegevens voor de klantportaal',
'LBL_GIVE_MAILID' => 'Uw e-mailadres svp',
'LBL_CHECK_MAILID' => 'Controleer uw e-mailadres voor de Klantportaal',
'LBL_LOGIN_REVOKED' => 'Uw inloggegevens zijn vervallen. Neem contact op met de helpdesk.',
'LBL_MAIL_SENT' => 'Een e-mail met de inloggegevsn is verstuurd naar het ons bekende emailadres',
'LBL_ALTBODY' => '',

// Added after 5.0.4 GA

// Module Sequence Numbering
'Ticket No' => 'Ticket Nr',
// END

'Hours' => 'Uren',
'Days' => 'Dagen',

'From Portal' => 'Van Portaal',
);

?>
