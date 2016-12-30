<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
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
 * @version 	$Revision: 1.6 $ $Date: 2012/02/28 23:40:22 $
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/Leads/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/

if ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true')) {
	$toggle_historicos = 'See Non Converted Leads';
	$toggle_name = 'Converted Leads';
} else {
	$toggle_historicos = 'See Converted Leads';
	$toggle_name = 'Leads';
}

$mod_strings = Array(
'LBL_TGL_HISTORICOS' => $toggle_historicos,
'LBL_MODULE_NAME'=>$toggle_name,
'Leads' => $toggle_name,
'LBL_DIRECT_REPORTS_FORM_NAME'=>'Medewerkers',
'LBL_MODULE_TITLE'=>'Leads: Home',
'LBL_SEARCH_FORM_TITLE'=>'Zoek Lead',
'LBL_LIST_FORM_TITLE'=>'Leadlijst',
'LBL_NEW_FORM_TITLE'=>'Nieuwe Lead',
'LBL_LEAD_OPP_FORM_TITLE'=>'Contact:',
'LBL_LEAD'=>'Leads:',
'LBL_ADDRESS_INFORMATION'=>'Adresinformatie',
'LBL_CUSTOM_INFORMATION'=>'Klantinformatie',

'LBL_LIST_NAME'=>'Naam',
'LBL_LIST_LAST_NAME'=>'Achternaam',
'LBL_LIST_COMPANY'=>'Bedrijf',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_LEAD_NAME'=>'Lead naam',
'LBL_LIST_EMAIL'=>'E-mail',
'LBL_LIST_PHONE'=>'Telefoon',
'LBL_LIST_LEAD_ROLE'=>'Rol',

'LBL_NAME'=>'Naam:',
'LBL_LEAD_NAME'=>'Lead naam:',
'LBL_LEAD_INFORMATION'=>'Lead informatie',
'LBL_FIRST_NAME'=>'Voornaam:',
'LBL_PHONE'=>'Telefoon:',
'LBL_COMPANY'=>'Bedrijf:',
'LBL_DESIGNATION'=>'Functie:',
'LBL_PHONE'=>'Telefoon:',
'LBL_LAST_NAME'=>'Achternaam:',
'LBL_MOBILE'=>'Telefoon Mobiel:',
'LBL_EMAIL'=>'E-mail:',
'LBL_LEAD_SOURCE'=>'Lead bron:',
'LBL_LEAD_STATUS'=>'Lead status:',
'LBL_WEBSITE'=>'Website:',
'LBL_FAX'=>'Fax:',
'LBL_INDUSTRY'=>'Industrie:',
'LBL_ANNUAL_REVENUE'=>'Jaaromzet:',
'LBL_RATING'=>'Beoordeling:',
'LBL_LICENSE_KEY'=>'Licentie code:',
'LBL_NO_OF_EMPLOYEES'=>'Aantal medewerkers:',
'LBL_YAHOO_ID'=>'Tweede e-mailadres:',

'LBL_ADDRESS_STREET'=>'Straat:',
'LBL_ADDRESS_POSTAL_CODE'=>'Postcode:',
'LBL_ADDRESS_CITY'=>'Plaats:',
'LBL_ADDRESS_COUNTRY'=>'Land:',
'LBL_ADDRESS_STATE'=>'Provincie:',
'LBL_ADDRESS'=>'Adres:',
'LBL_DESCRIPTION_INFORMATION'=>'Omschrijving',
'LBL_DESCRIPTION'=>'Omschrijving:',

'LBL_CONVERT_LEAD'=>'Converteer Lead:',
'LBL_CONVERT_LEAD_INFORMATION'=>'Converteer Lead informatie',
'LBL_ACCOUNT_NAME'=>'Accountnaam',
'LBL_POTENTIAL_NAME'=>'Naam verkoopkans',
'LBL_POTENTIAL_CLOSE_DATE'=>'Vervaldatum verkoopkans',
'LBL_POTENTIAL_AMOUNT'=>'Bedrag verkoopkans',
'LBL_POTENTIAL_SALES_STAGE'=>'Stadium verkoopkans',

'NTC_DELETE_CONFIRMATION'=>'Wilt u dit verwijderen?',
'NTC_REMOVE_CONFIRMATION'=>'Wilt u deze contactpersoon verwijderen?',
'NTC_REMOVE_DIRECT_REPORT_CONFIRMATION'=>'Wilt u dit verwijderen?',
'NTC_REMOVE_OPP_CONFIRMATION'=>'Wilt u dit verwijderen?',
'ERR_DELETE_RECORD'=>'Een veld moet gespecificeerd zijn om een contact te verwijderen.',

'LBL_COLON'=>' : ', 
'LBL_IMPORT_LEADS'=>'Importeer Leads',
'LBL_LEADS_FILE_LIST'=>'Bestandslijst Lead',
'LBL_INSTRUCTIONS'=>'Instructies',
'LBL_KINDLY_PROVIDE_AN_XLS_FILE'=>'Graag een .xls bestand A.U.B.',
'LBL_PROVIDE_ATLEAST_ONE_FILE'=>'U moet in iedergeval 1 bestand leveren',

'LBL_NONE'=>'Geen',
'LBL_ASSIGNED_TO'=>'Toegewezen aan:',
'LBL_SELECT_LEAD'=>'Selecteer lead',
'LBL_GENERAL_INFORMATION'=>'Algemene informatie',
'LBL_DO_NOT_CREATE_NEW_POTENTIAL'=>'Hier geen nieuwe verkoopkans maken a.u.b.',

'LBL_NEW_POTENTIAL'=>'Nieuwe Verkoopkans',
'LBL_POTENTIAL_TITLE'=>'Verkoopkans',

'LBL_NEW_TASK'=>'Nieuwe taak',
'LBL_TASK_TITLE'=>'Taken',
'LBL_NEW_CALL'=>'Nieuw telefoongesprek',
'LBL_CALL_TITLE'=>'Telefoongesprekken',
'LBL_NEW_MEETING'=>'Nieuwe vergadering',
'LBL_MEETING_TITLE'=>'Vergaderingen',
'LBL_NEW_EMAIL'=>'Nieuwe e-mail',
'LBL_EMAIL_TITLE'=>'E-mails',
'LBL_NEW_NOTE'=>'Nieuwe notitie',
'LBL_NOTE_TITLE'=>'Notities',
'LBL_NEW_ATTACHMENT'=>'Nieuwe bijlage',
'LBL_ATTACHMENT_TITLE'=>'Bijlagen',

'LBL_ALL'=>'Alle',
'LBL_CONTACTED'=>'Contact',
'LBL_LOST'=>'Verloren',
'LBL_HOT'=>'Heet',
'LBL_COLD'=>'Koud',

'LBL_TOOL_FORM_TITLE'=>'Leads gereedschap',

'LBL_SELECT_TEMPLATE_TO_MAIL_MERGE'=>'Selecteer sjabloon voor e-mail merge:',

'Salutation'=>'Begroeting',
'First Name'=>'Voornaam',
'Phone'=>'Telefoon',
'Last Name'=>'Achternaam',
'Mobile'=>'Telefoon Mobiel',
'Company'=>'Bedrijf',
'Fax'=>'Fax',
'Designation'=>'Functie',
'Email'=>'E-mail',
'Lead Source'=>'Bron lead',
'Website'=>'Website',
'Annual Revenue'=>'Jaaromzet',
'Lead Status'=>'Status lead',
'Industry'=>'Industrie',
'Rating'=>'Beoordeling',
'No Of Employees'=>'Aantal medewerkers',
'Assigned To'=>'Toegewezen aan',
'Yahoo Id'=>'Tweede e-mailadres',
'Created Time'=>'Gemaakt',
'Modified Time'=>'Gewijzigd',
'Street'=>'Straat',
'Postal Code'=>'Postcode',
'City'=>'Plaats',
'Country'=>'Land',
'State'=>'Provincie',
'Description'=>'Omschrijving',
'Po Box'=>'Postbus',
'Campaign Source'=>'Bron campagne',
'Name'=>'Naam',
'LBL_NEW_LEADS'=>'Mijn nieuwe Leads',

//Added for Existing Picklist Entries
'--None--'=>'--Geen--',
'Mr.'=>'heer',
'Ms.'=>'mevrouw',
'Mrs.'=>'mejuffrouw',
'Dr.'=>'dr.',
'Prof.'=>'prof.',

'Acquired'=>'Overname',
'Active'=>'Actief',
'Market Failed'=>'Slechte markt',
'Project Cancelled'=>'Project geannuleerd',
'Shutdown'=>'Afsluiten',

'Apparel'=>'Kleding',
'Banking'=>'Banken',
'Biotechnology'=>'Biotechnologie',
'Chemicals'=>'Chemisch',
'Communications'=>'Communicatie',
'Construction'=>'Constructie',
'Consulting'=>'Consulting',
'Education'=>'Educatie',
'Electronics'=>'Electronica',
'Energy'=>'Energie',
'Engineering'=>'Installatie',
'Entertainment'=>'Entertainment',
'Environmental'=>'Milieu',
'Finance'=>'Financieel',
'Food & Beverage'=>'Voedingsmiddelen & Dranken',
'Government'=>'Overheid',
'Healthcare'=>'Gezondheidszorg',
'Hospitality'=>'Hotels en conferentie gelegenheden',
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

'Cold Call'=>'Koud bellen',
'Existing Customer'=>'Bestaande klant',
'Self Generated'=>'Eigen inspanning',
'Employee'=>'Werknemer',
'Partner'=>'Partner',
'Public Relations'=>'Public Relations',
'Direct Mail'=>'Direct mail',
'Conference'=>'Conferentie',
'Trade Show'=>'Beurs',
'Web Site'=>'Website',
'Word of mouth'=>'Mond tot mond',

'Attempted to Contact'=>'Contact gezocht',
'Cold'=>'Koud',
'Contact in Future'=>'Contakt in toekomst',
'Contacted'=>'Gecontacteerd',
'Hot'=>'Warm',
'Junk Lead'=>'Junk Lead',
'Lost Lead'=>'Verloren Lead',
'Not Contacted'=>'Niet gecontacteerd',
'Pre Qualified'=>'Voorgekwalificeerd',
'Qualified'=>'Gekwalificeerd',
'Warm'=>'Warm',

'Designation'=>'Functie',

//Module Sequence Numbering
'Lead No'=>'Lead Nr',

'LBL_TRANSFER_RELATED_RECORDS_TO' => 'Draag geselecteerde records over aan',

'LBL_FOLLOWING_ARE_POSSIBLE_REASONS' => 'Hetvolgende is een van de mogelijke redenen',
'LBL_LEADS_FIELD_MAPPING_INCOMPLETE' => 'Niet alle verplichte velden zijn gekoppeld',
'LBL_MANDATORY_FIELDS_ARE_EMPTY' => 'Enkele van de verplichte velden zijn leeg',
'LBL_LEADS_FIELD_MAPPING' => 'Leads Maatwerk Veldmapping',
'LeadAlreadyConverted' => 'Lead cannot be converted. Either it has already been converted or you lack permission on one or more of the destination modules.',
'Is Converted From Lead' => 'Wordt geconverteerd van Lead',
'Converted From Lead' => 'Wordt geconverteerd Lead',

);

?>
