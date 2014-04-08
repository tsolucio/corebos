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
 * @version 	$Revision: 1.5 $ $Date: 2011/11/14 17:07:26 $
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/Accounts/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/
 
$mod_strings = Array(
'LBL_MODULE_NAME'=>'Accounts',
'LBL_MODULE_TITLE'=>'Accounts: Home',
'LBL_SEARCH_FORM_TITLE'=>'Account zoeken',
'LBL_LIST_FORM_TITLE'=>'Accountlijst',
'LBL_NEW_FORM_TITLE'=>'Nieuw account',
'LBL_MEMBER_ORG_FORM_TITLE'=>'Leden organisatie',
// Label for Top Accounts in Home Page, added for 4.2 GA
'LBL_TOP_ACCOUNTS'=>'Mijn accounts', 
'LBL_TOP_AMOUNT'=>'Hoeveelheid',
'LBL_LIST_ACCOUNT_NAME'=>'Accountnaam',
'LBL_LIST_CITY'=>'Plaats',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_STATE'=>'Provincie',
'LBL_LIST_PHONE'=>'Telefoon',
'LBL_LIST_EMAIL_ADDRESS'=>'E-mailadres',
'LBL_LIST_CONTACT_NAME'=>'Contactnaam',
'LBL_LIST_AMOUNT' => 'Totale verkoopkansen',

//DON'T CONVERT THESE THEY ARE MAPPINGS
'db_name' => 'LBL_LIST_ACCOUNT_NAME',
'db_website' => 'LBL_LIST_WEBSITE',
'db_billing_address_city' => 'LBL_LIST_CITY',

//END DON'T CONVERT

'LBL_ACCOUNT'=>'Account:',
'LBL_ACCOUNT_NAME'=>'Accountnaam:',
'LBL_PHONE'=>'Telefoon:',
'LBL_WEBSITE'=>'Website:',
'LBL_FAX'=>'Fax:',
'LBL_TICKER_SYMBOL'=>'Ticker symbool:',
'LBL_OTHER_PHONE'=>'Telefoon Mobiel:',
'LBL_ANY_PHONE'=>'Telefoon Extra:',
'LBL_MEMBER_OF'=>'Onderdeel van:',
'LBL_EMAIL'=>'E-mail:',
'LBL_EMPLOYEES'=>'Werknemers:',
'LBL_OTHER_EMAIL_ADDRESS'=>'Bedrijfs e-mailadres:',
'LBL_ANY_EMAIL'=>'Extra e-mailadres:',
'LBL_OWNERSHIP'=>'Eigendom:',
'LBL_RATING'=>'Beoordeling:',
'LBL_INDUSTRY'=>'Industrie',
'LBL_SIC_CODE'=>'SIC nummer:',
'LBL_TYPE'=>'Type:',
'LBL_ANNUAL_REVENUE'=>'Jaarlijkse omzet:',
'LBL_ADDRESS_INFORMATION'=>'Adresinformatie',
'LBL_ACCOUNT_INFORMATION'=>'Accountinformatie',
'LBL_CUSTOM_INFORMATION'=>'Extra informatie',
'LBL_BILLING_ADDRESS'=>'Postadres:',
'LBL_SHIPPING_ADDRESS'=>'Afleveradres:',
'LBL_ANY_ADDRESS'=>'Bezoekadres:',
'LBL_CITY'=>'Plaats:',
'LBL_STATE'=>'Provincie:',
'LBL_POSTAL_CODE'=>'Postcode:',
'LBL_COUNTRY'=>'Land:',
'LBL_DESCRIPTION_INFORMATION'=>'Omschrijving',
'LBL_DESCRIPTION'=>'Omschrijving:',
'NTC_COPY_BILLING_ADDRESS'=>'Kopieer Postadres naar Bezoekadres',
'NTC_COPY_SHIPPING_ADDRESS'=>'Kopieer Bezoekadres naar Postadres',
'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'Weet u zeker dat u dit veld wilt verwijderen als Onderdeel van deze organisatie?',
'LBL_DUPLICATE'=>'Mogelijke dubbele Accounts',
'MSG_DUPLICATE' => 'Wanneer u deze Account aanmaakt kan dit leiden tot een duplicaat. U kunt een keuze maken uit de geselecteerde accounts hieronder of u kunt klikken op Nieuw Account om door te gaan met uw nieuwe Account en de door u ingegeven informatie.',

'LBL_INVITEE'=>'Contacten',
'ERR_DELETE_RECORD'=>"Een veld moet gespecificeerd zijn om de account te verwijderen.",

'LBL_SELECT_ACCOUNT'=>'Selecteer account',
'LBL_GENERAL_INFORMATION'=>'Algemene informatie',

//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nieuwe Verkoopkans',
'LBL_POTENTIAL_TITLE'=>'Verkoopkansen',

'LBL_NEW_TASK'=>'Nieuwe taak',
'LBL_TASK_TITLE'=>'Taken',
'LBL_NEW_CALL'=>'Telefoongesprek',
'LBL_CALL_TITLE'=>'Telefoongesprekken',
'LBL_NEW_MEETING'=>'Vergadering',
'LBL_MEETING_TITLE'=>'Vergadering',
'LBL_NEW_EMAIL'=>'Nieuwe e-mail',
'LBL_EMAIL_TITLE'=>'E-mail',
'LBL_NEW_CONTACT'=>'Nieuwe contacten',
'LBL_CONTACT_TITLE'=>'Contacten',

//Added fields after RC1 - Release
'LBL_ALL'=>'Alle',
'LBL_PROSPECT'=>'Prospect',
'LBL_INVESTOR'=>'Investeerder',
'LBL_RESELLER'=>'Wederverkoper',
'LBL_PARTNER'=>'Partner',

// Added for 4GA
'LBL_TOOL_FORM_TITLE'=>'Account gereedschap',
//Added for 4GA
'Account Name'=>'Accountnaam',
'Phone'=>'Telefoon',
'Website'=>'Website',
'Fax'=>'Fax',
'Ticker Symbol'=>'Ticker symbool',
'Other Phone'=>'Telefoon Mobiel',
'Member Of'=>'Onderdeel van',
'Email'=>'E-mail',
'Employees'=>'Werknemers',
'Other Email'=>'Extra e-mailadres',
'Ownership'=>'Eigendom',
'Rating'=>'Beoordeling',
'industry'=>'Industrie',
'SIC Code'=>'SBI code',
'Type'=>'Type',
'Annual Revenue'=>'Jaarlijkse omzet',
'Assigned To'=>'Toegewezen aan',
'Billing Address'=>'Postadres',
'Shipping Address'=>'Bezoekadres',
'Billing City'=>'P Plaats',
'Shipping City'=>'B Plaats',
'Billing State'=>'P Provincie',
'Shipping State'=>'B Provincie',
'Billing Code'=>'P Postcode',
'Shipping Code'=>'B Postcode',
'Billing Country'=>'P Land',
'Shipping Country'=>'B Land',
'Created Time'=>'Gemaakt',
'Modified Time'=>'Gewijzigd',
'Description'=>'Omschrijving',
'Billing Po Box'=>'P Postbus',
'Shipping Po Box'=>'B Postbus',

//Added after 4.2 patch 2
'Email Opt Out'=>'E-mail optie uit',
'LBL_EMAIL_OPT_OUT'=>'E-mail optie uit:',

//Added after 5Alpha5
'Notify Owner'=>'Notificatie aan eigenaar',

//Added for existing picklist entries

'--None--'=>'--Geen--',

'Acquired'=>'Overname',
'Active'=>'Actief',
'Market Failed'=>'Slechte markt',
'Project Cancelled'=>'Project geannuleerd',
'Shutdown'=>'Afsluiten',

'Apparel'=>'Kleding',
'Banking'=>'Banken',
'Biotechnology'=>'Biotechnologie',
'Chemicals'=>'Chemicalieen',
'Communications'=>'Communicatie',
'Construction'=>'Constructie',
'Consulting'=>'Consulting',
'Education'=>'Opleidingen',
'Electronics'=>'Electronica',
'Energy'=>'Energie',
'Engineering'=>'Installatie',
'Entertainment'=>'Entertainment',
'Environmental'=>'Milieu',
'Finance'=>'Financieel',
'Food & Beverage'=>'Voedingsmiddelen & Dranken',
'Government'=>'Overheid',
'Healthcare'=>'Gezondheidszorg',
'Hospitality'=>'Hotels en Conferentie gelegenheden',
'Insurance'=>'Verzekering',
'Machinery'=>'Machinerie',
'Manufacturing'=>'Productie',
'Media'=>'Media',
'Not For Profit'=>'Non Profit',
'Recreation'=>'Recreatie',
'Retail'=>'Detailhandel',
'Shipping'=>'Distributie',
'Technology'=>'Technologie',
'Telecommunications'=>'Telecommunicatie',
'Transportation'=>'Transport',
'Utilities'=>'Nutsbedrijven',
'Other'=>'Anders',

'Analyst'=>'Analist',
'Competitor'=>'Concurrent',
'Customer'=>'Klant',
'Integrator'=>'Integrator',
'Investor'=>'Investeerder',
'Partner'=>'Partner',
'Press'=>'Pers',
'Prospect'=>'Prospect',
'Reseller'=>'Wederverkoper',
'LBL_START_DATE' => 'Startdatum',
'LBL_END_DATE' => 'Einddatum',
// Added/Updated for vtiger CRM 5.0.4

//added to fix the issue #4081
'LBL_ACCOUNT_EXIST' => 'Accountnaam bestaat al!',

// mailer export
'LBL_MAILER_EXPORT' => 'Mailer export',
'LBL_MAILER_EXPORT_CONTACTS_TYPE'=>'Selecteer contacten:',
'LBL_MAILER_EXPORT_CONTACTS_DESCR'=>'Contacten kunt u selecteren met "standaard velden" en andere velden.',
'LBL_MAILER_EXPORT_RESULTS_TYPE'=>'Selecteer export type:',
'LBL_MAILER_EXPORT_RESULTS_DESCR'=>'De gegevens zullen worden verzameld van accounts en haar contacten, die zijn retourneerd van een vorige zoekopdracht.',
'LBL_EXPORT_RESULTS_EMAIL' => 'Export e-mail data',
'LBL_EXPORT_RESULTS_EMAIL_CORP'=>'Export e-mail data, de "bedrijfs e-mail account" zal worden gebruikt als het contact e-mail veld niet ingevuld is.',
'LBL_EXPORT_RESULTS_FULL'=>'Export data met contacten, e-mail, Accountnaam, adres, telefoon, etc.',
'LBL_EXPORT_RESULTS_GO'=>'Export',
'LBL_MAILER_EXPORT_IGNORE' => '--negeer--',
'LBL_MAILER_EXPORT_CHECKED' =>'Gecontroleerd',
'LBL_MAILER_EXPORT_NOTCHECKED' => 'Niet gecontroleerd',

// Added after 5.0.4 GA

//Module Sequence Numbering
'Account No'=>'Account Nr',
// END

// Account Hierarchy
'LBL_SHOW_ACCOUNT_HIERARCHY' => 'Toon Account Hierarchie', 


);

?>