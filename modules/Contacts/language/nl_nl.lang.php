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
 * @version 	$Revision: 1.6 $ $Date: 2012/02/28 23:40:22 $
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/Contacts/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/

$mod_strings = Array(
// Mike Crowe Mod --------------------------------------------------------Added for general search
'LBL_MODULE_NAME'=>'Contacten',
'LBL_INVITEE'=>'Medewerker',
'LBL_MODULE_TITLE'=>'Contacten: Home',
'LBL_SEARCH_FORM_TITLE'=>'Contact zoeken',
'LBL_LIST_FORM_TITLE'=>'Contactlijst',
'LBL_NEW_FORM_TITLE'=>'Nieuw contact',
'LBL_CONTACT_OPP_FORM_TITLE'=>'Contact-gelegenheid:',
'LBL_CONTACT'=>'Contact:',

'LBL_LIST_NAME'=>'Naam',
'LBL_LIST_LAST_NAME'=>'Achternaam',
'LBL_LIST_FIRST_NAME'=>'Voornaam',
'LBL_LIST_CONTACT_NAME'=>'Contactnaam',
'LBL_LIST_TITLE'=>'Titel',
'LBL_LIST_ACCOUNT_NAME'=>'Accountnaam',
'LBL_LIST_EMAIL_ADDRESS'=>'E-mail',
'LBL_LIST_PHONE'=>'Telefoon',
'LBL_LIST_CONTACT_ROLE'=>'Contact bron',

//DON'T CONVERT THESE THEY ARE MAPPINGS
'db_last_name' => 'LBL_LIST_LAST_NAME',
'db_first_name' => 'LBL_LIST_FIRST_NAME',
'db_title' => 'LBL_LIST_TITLE',
'db_email1' => 'LBL_LIST_EMAIL_ADDRESS',
'db_email2' => 'LBL_LIST_EMAIL_ADDRESS',
//END DON'T CONVERT
	
'LBL_EXISTING_CONTACT' => 'Gebruikte en bestaande contacten',
'LBL_CREATED_CONTACT' => 'Nieuwe account aangemaakt',
'LBL_EXISTING_ACCOUNT' => 'Bestaande klant gebruikt',
'LBL_CREATED_ACCOUNT' => 'Nieuwe account aangemaakt',
'LBL_CREATED_CALL' => 'Nieuw telefoonafspraak aangemaakt',
'LBL_CREATED_MEETING' => 'Nieuwe vergadering aangemaakt',
'LBL_ADDMORE_BUSINESSCARD' =>'Visitekaart toevoegen',

'LBL_BUSINESSCARD' => 'Visitekaart',

'LBL_NAME'=>'Naam:',
'LBL_CONTACT_NAME'=>'Contactnaam:',
'LBL_CONTACT_INFORMATION'=>'Contact informatie',
'LBL_CUSTOM_INFORMATION'=>'Algemene informatie',
'LBL_FIRST_NAME'=>'Voornaam:',
'LBL_OFFICE_PHONE'=>'Telefoon Kantoor:',
'LBL_ACCOUNT_NAME'=>'Accountnaam:',
'LBL_ANY_PHONE'=>'Telefoon prive:',
'LBL_PHONE'=>'Telefoon:',
'LBL_LAST_NAME'=>'Achternaam:',
'LBL_MOBILE_PHONE'=>'Telefoon Mobiel:',
'LBL_HOME_PHONE'=>'Thuis:',
'LBL_LEAD_SOURCE'=>'Lead bron:',
'LBL_OTHER_PHONE'=>'Telefoon:',
'LBL_FAX_PHONE'=>'Fax:',
'LBL_TITLE'=>'Titel:',
'LBL_DEPARTMENT'=>'Afdeling:',
'LBL_BIRTHDATE'=>'Verjaardag:',
'LBL_EMAIL_ADDRESS'=>'E-mail:',
'LBL_OTHER_EMAIL_ADDRESS'=>'Prive e-mail:',
'LBL_ANY_EMAIL'=>'Bedrijfs e-mail:',
'LBL_REPORTS_TO'=>'Rapporteert aan:',
'LBL_ASSISTANT'=>'Assistent:',
'LBL_YAHOO_ID'=>'Secundair e-mailadres:',
'LBL_ASSISTANT_PHONE'=>'Telefoon assistent:',
'LBL_DO_NOT_CALL'=>'Niet bellen:',
'LBL_EMAIL_OPT_OUT'=>'E-mail optie uit:',
'LBL_PRIMARY_ADDRESS'=>'Hoofdadres:',
'LBL_ALTERNATE_ADDRESS'=>'Postadres:',
'LBL_ANY_ADDRESS'=>'Privé adres:',
'LBL_CITY'=>'Plaats:',
'LBL_STATE'=>'Provincie:',
'LBL_POSTAL_CODE'=>'Postcode:',
'LBL_COUNTRY'=>'Land:',
'LBL_DESCRIPTION_INFORMATION'=>'Omschrijving',
'LBL_IMAGE_INFORMATION'=>'Afbeelding:',
'LBL_ADDRESS_INFORMATION'=>'Adresinformatie',
'LBL_DESCRIPTION'=>'Omschrijving:',
'LBL_CONTACT_ROLE'=>'Rol:',
'LBL_OPP_NAME'=>'Verkoopkans naam:',
'LBL_DUPLICATE'=>'Mogelijke gedupliceerde contacten',
'MSG_DUPLICATE' => 'Bij het aanmaken van deze contactgegevens creert u waarschijnlijk een duplicaat van de gegevens. U kunt een contact selecteren van de lijst of u klikt op Nieuw contact om verder te gaan met de ingevoerde gegevens.',

'LNK_NEW_APPOINTMENT' => 'Nieuwe afspraak',
'LBL_ADD_BUSINESSCARD' => 'Business kaart toevoegen',
'NTC_DELETE_CONFIRMATION'=>'Weet u zeker dat u dit veld wilt verwijderen?',
'NTC_REMOVE_CONFIRMATION'=>'Weet u zeker dat u dit contact wilt verwijderen?',
'NTC_REMOVE_DIRECT_REPORT_CONFIRMATION'=>'Weet u zeker dat u deze medewerker wilt verwijderen?',
'ERR_DELETE_RECORD'=>"en_us Een veld moet gespecificeerd zijn om de contactpersoon te verwijderen.",
'NTC_COPY_PRIMARY_ADDRESS'=>'Kopieer Bezoekadres naar Postadres',
'NTC_COPY_ALTERNATE_ADDRESS'=>' Kopieer Postadres naar Bezoekadres',

'LBL_SELECT_CONTACT'=>'Selecteer Contact',
//Added for search heading
'LBL_GENERAL_INFORMATION'=>'Algemene informatie',



//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nieuwe Verkoopkans',
'LBL_POTENTIAL_TITLE'=>'Verkoopkansen',

'LBL_NEW_TASK'=>'Nieuwe taak',
'LBL_TASK_TITLE'=>'Taken',
'LBL_NEW_CALL'=>'Nieuw telefoongesprek',
'LBL_CALL_TITLE'=>'Telefoongesprekken',
'LBL_NEW_MEETING'=>'Nieuwe vergadering',
'LBL_MEETING_TITLE'=>'Vergaderingen',
'LBL_NEW_EMAIL'=>'Nieuwe e-mail',
'LBL_EMAIL_TITLE'=>'E-mail',
'LBL_NEW_NOTE'=>'Nieuwe notitie',
'LBL_NOTE_TITLE'=>'Notities',

// Added for 4GA
'LBL_TOOL_FORM_TITLE'=>'Contact gereedschap',

'Salutation'=>'Aanhef',
'First Name'=>'Voornaam',
'Office Phone'=>'Telefoon Kantoor',
'Last Name'=>'Achternaam',
'Mobile'=>'Telefoon Mobiel',
'Account Name'=>'Accountnaam',
'Home Phone'=>'Telefoon Thuis',
'Lead Source'=>'Lead bron',
'Phone'=>'Telefoonnummer',
'Title'=>'Titel',
'Fax'=>'Fax',
'Department'=>'Afdeling',
'Birthdate'=>'Verjaardag',
'Email'=>'E-mail',
'Reports To'=>'Rapporteert aan',
'Assistant'=>'Assistent',
'Yahoo Id'=>'Tweede e-mailadres',
'Assistant Phone'=>'Telefoon assistent',
'Do Not Call'=>'Niet bellen',
'Email Opt Out'=>'E-mail optie uit',
'Assigned To'=>'Toegewezen aan',
'Campaign Source'=>'Campagne bron',
'Reference' =>'Referentie',
'Created Time'=>'Gemaakt',
'Modified Time'=>'Gewijzigd',
'Mailing Street'=>'Postadres straat',
'Other Street'=>'Bezoekadres straat',
'Mailing City'=>'Postadres Plaats',
'Mailing State'=>'Postadres Provincie',
'Mailing Zip'=>'Postadres Postcode',
'Mailing Country'=>'Postadres Land',
'Mailing Po Box'=>'Postbus',
'Other Po Box'=>'Bezoekadres postbus ',
'Other City'=>'Bezoekadres Plaats',
'Other State'=>'Bezoekadres Provincie',
'Other Zip'=>'Bezoekadres Postcode',
'Other Country'=>'Bezoekadres Land',
'Contact Image'=>'Contact foto',
'Description'=>'Omschrijving',

// Added fields for Add Business Card
'LBL_NEW_CONTACT'=>'Nieuw contact',
'LBL_NEW_ACCOUNT'=>'Nieuw account',
'LBL_NOTE_SUBJECT'=>'Notitie onderwerp:',
'LBL_NOTE'=>'Notitie:',
'LBL_WEBSITE'=>'Website:',
'LBL_NEW_APPOINTMENT'=>'Nieuwe afspraak',
'LBL_SUBJECT'=>'Onderwerp:',
'LBL_START_DATE'=>'Startdatum:',
'LBL_START_TIME'=>'Starttijd:',

//Added field after 4_0_1
'Portal User'=>'Toegang Portaal',
'LBL_CUSTOMER_PORTAL_INFORMATION'=>'Klantportaal informatie',
'Support Start Date'=>'Startdatum toegang',
'Support End Date'=>'Einddatum toegang',
//Added for 4.2 Release -- CustomView
'Name'=>'Naam',
'LBL_ALL'=>'Alles',
'LBL_MAXIMUM_LIMIT_ERROR'=>'FOUT, uw bestand is te groot. Probeer opnieuw a.u.b. Probeer een bestand kleiner dan 800000 bytes',
'LBL_UPLOAD_ERROR'=>'Problemen met het versturen van uw bestand. Probeer opnieuw a.u.b!',
'LBL_IMAGE_ERROR'=>'Dit bestand is geen foto(.gif/.jpg/.png)',
'LBL_INVALID_IMAGE'=>'Dit bestand bevat geen data',

//Added after 5Alpha5
'Notify Owner'=>'Bericht eigenaar',

//Added for Picklist Values
'--None--'=>'--Geen--',

'Mr.'=>'heer',
'Ms.'=>'mevrouw',
'Mrs.'=>'mejuffrouw',
'Dr.'=>'dr.',
'Prof.'=>'prof.',

'Cold Call'=>'Koud bellen',
'Existing Customer'=>'Bestaande klant',
'Self Generated'=>'Eigen inspanning',
'Employee'=>'Medewerker',
'Partner'=>'Partner',
'Public Relations'=>'Public Relations',
'Direct Mail'=>'Direct mail',
'Conference'=>'Conferentie',
'Trade Show'=>'Beurs',
'Web Site'=>'Website',
'Word of mouth'=>'Mond tot mond',
'Other'=>'Anders',

'User List'=>'Gebruikerslijst',

//Added for 5.0.3
'Customer Portal Login Details'=>'Inlog gegevens van klant Portaal',
'Dear'=>'Beste',
'Your Customer Portal Login details are given below:'=>'Uw klant Portaal inlog gegevens zijn hieronder weergegeven:',
'User Id :'=>'Gebruikers Id :',
'Password :'=>'Wachtwoord :',
'Please Login Here'=>'aub Hier inloggen',
'Note :'=>'Notitie :',
'We suggest you to change your password after logging in first time'=>'Wij stellen u voor om het wachtwoord na de eerste keer inloggen te wijzigen.',
'Support Team'=>'Ondersteuningsteam',

'TITLE_AJAX_CSS_POPUP_CHAT'=>'Chatkanaal',

// Added after 5.0.4 GA

// Module Sequence Numbering
'Contact Id' => 'Contact Nr',
'Secondary Email'=>'Tweede E-mail',
// END
);

?>
