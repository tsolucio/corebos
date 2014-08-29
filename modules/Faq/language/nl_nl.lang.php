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
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/Faq/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/
 
$mod_strings = Array(
'LBL_MODULE_NAME'=>'FAQ',
'LBL_MODULE_TITLE'=>'FAQ: Home',
'LBL_SEARCH_FORM_TITLE'=>'Zoek FAQ',
'LBL_LIST_FORM_TITLE'=>'FAQ lijst',
'LBL_NEW_FORM_TITLE'=>'Nieuwe FAQ',
'LBL_MEMBER_ORG_FORM_TITLE'=>'Leden organisatie',

'LBL_LIST_ACCOUNT_NAME'=>'Organisatienaam',
'LBL_LIST_CITY'=>'Plaats',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_STATE'=>'Provincie',
'LBL_LIST_PHONE'=>'Telefoon',
'LBL_LIST_EMAIL_ADDRESS'=>'e-mailadres',
'LBL_LIST_CONTACT_NAME'=>'Contactpersoon',
'LBL_FAQ_INFORMATION'=>'FAQ informatie',

//DON'T CONVERT THESE THEY ARE MAPPINGS
'db_name' => 'LBL_LIST_ACCOUNT_NAME',
'db_website' => 'LBL_LIST_WEBSITE',
'db_billing_address_city' => 'LBL_LIST_CITY',

//END DON'T CONVERT

'LBL_ACCOUNT'=>'FAQ:',
'LBL_ACCOUNT_NAME'=>'Organisatie:',
'LBL_PHONE'=>'Telefoon:',
'LBL_WEBSITE'=>'Website:',
'LBL_FAX'=>'Fax:',
'LBL_TICKER_SYMBOL'=>'Ticker Symbool:',
'LBL_OTHER_PHONE'=>'Telefoon:',
'LBL_ANY_PHONE'=>'Telefoon Prive:',
'LBL_MEMBER_OF'=>'Onderdeel van:',
'LBL_EMAIL'=>'E-mail:',
'LBL_EMPLOYEES'=>'Werknemers:',
'LBL_OTHER_EMAIL_ADDRESS'=>'Prive e-mail:',
'LBL_ANY_EMAIL'=>'Bedrijfs e-mail:',
'LBL_OWNERSHIP'=>'Eigenaar:',
'LBL_RATING'=>'Beoordeling:',
'LBL_INDUSTRY'=>'Industrie:',
'LBL_SIC_CODE'=>'SBI code:',
'LBL_TYPE'=>'Type:',
'LBL_ANNUAL_REVENUE'=>'Jaarlijkse omzet:',
'LBL_ADDRESS_INFORMATION'=>'Adresinformatie',
'LBL_ACCOUNT_INFORMATION'=>'FAQ informatie',
'LBL_BILLING_ADDRESS'=>'Postadres:',
'LBL_SHIPPING_ADDRESS'=>'Bezoekadres:',
'LBL_ANY_ADDRESS'=>'Privéadres:',
'LBL_CITY'=>'Plaats:',
'LBL_STATE'=>'Provincie:',
'LBL_POSTAL_CODE'=>'Postcode:',
'LBL_COUNTRY'=>'Land:',
'LBL_DESCRIPTION_INFORMATION'=>'Omschrijving',
'LBL_DESCRIPTION'=>'Omschrijving:',
'NTC_COPY_BILLING_ADDRESS'=>'Kopieer Postadres naar Bezoekadres',
'NTC_COPY_SHIPPING_ADDRESS'=>'Kopieer Bezoekadres naar Postadres',
'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'Weet u zeker dat u dit veld wilt verwijderen als Onderdeel van de organisatie?',
'LBL_DUPLICATE'=>'Mogelijke dubbele FAQ',
'MSG_DUPLICATE' => 'Bij het aanmaken van deze contactgegevens creert u waarschijnlijk een duplicatie van de gegegevens. U kunt een contact selecteren van de lijst of u klikt op FAQ om verder te gaan met de ingevoerde gegevens.',

'LBL_INVITEE'=>'Contacten',
'ERR_DELETE_RECORD'=>"Een veld moet gespecificeerd zijn om de account te verwijderen.",

'LBL_SELECT_ACCOUNT'=>'Selecteer FAQ',
'LBL_GENERAL_INFORMATION'=>'Algemene informatie',

//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nieuwe verkoopkans',
'LBL_POTENTIAL_TITLE'=>'Verkoopkansen',

'LBL_NEW_TASK'=>'Nieuwe taak',
'LBL_TASK_TITLE'=>'Taken',
'LBL_NEW_CALL'=>'Nieuw telefoongesprek',
'LBL_CALL_TITLE'=>'Telefoongesprekken',
'LBL_NEW_MEETING'=>'Nieuwe vergadering',
'LBL_MEETING_TITLE'=>'vergaderingen',
'LBL_NEW_EMAIL'=>'Nieuwe e-mail',
'LBL_EMAIL_TITLE'=>'E-mails',
'LBL_NEW_CONTACT'=>'Nieuw contact',
'LBL_CONTACT_TITLE'=>'Contacten',

//Added for 4GA Release
'Category'=>'Categorie',
'Related To'=>'Gerelateerd aan',
'Question'=>'Vraag',
'Answer'=>'Antwoord',
'Comments'=>'Opmerkingen',
'LBL_COMMENTS'=>'Opmerkingen',//give the same value given to the above string 'Comments'
'Created Time'=>'Aangemaakt',
'Modified Time'=>'Gewijzigd',

//Added fields after 4.2 alpha
'LBL_TICKETS'=>'Tickets',
'LBL_FAQ'=>'FAQ',
'Product Name'=>'Productnaam',
'FAQ Id'=>'FAQ Id',
'Add Comment'=>'Opmerking toevoegen',
'LBL_ADD_COMMENT'=>'Opmerking toevoegen',//give the same value given to the above string 'Add Comment'
'LBL_COMMENT_INFORMATION'=>'Opmerking informatie',
'Status'=>'Status',

//Added on 10-12-2005
'LBL_QUESTION'=>'Vragen',
'LBL_CATEGORY'=>'Categorie',
'LBL_MY_FAQ'=>'Mijn FAQ',

//Added for existing Picklist Entries

'General'=>'Algemeen',

'Draft'=>'Tijdelijk',
'Reviewed'=>'Gecontroleerd',
'Published'=>'Gepubliceerd',
'Obsolete'=>'Verouderd',

// Module Sequence Numbering
'Faq No' => 'FAQ Nr',
// END			
);

?>
