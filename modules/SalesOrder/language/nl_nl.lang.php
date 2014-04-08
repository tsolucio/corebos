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
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/SalesOrder/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/
 
$mod_strings = Array(
'LBL_MODULE_NAME'=>'Verkooporders',
'LBL_SO_MODULE_NAME'=>'Verkooporder',
'LBL_RELATED_PRODUCTS'=>'Product details',
'LBL_MODULE_TITLE'=>'Verkooporder: Home',
'LBL_SEARCH_FORM_TITLE'=>'Orders zoeken',
'LBL_LIST_SO_FORM_TITLE'=>'Verkooporder lijst',
'LBL_NEW_FORM_SO_TITLE'=>'Nieuwe verkooporder',
'LBL_MEMBER_ORG_FORM_TITLE'=>'Leden organisaties',

'LBL_LIST_ACCOUNT_NAME'=>'Accountnaam',
'LBL_LIST_CITY'=>'Plaats',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_STATE'=>'Provincie',
'LBL_LIST_PHONE'=>'Telefoon',
'LBL_LIST_EMAIL_ADDRESS'=>'E-mailadres',
'LBL_LIST_CONTACT_NAME'=>'Contactnaam',

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
'LBL_TICKER_SYMBOL'=>'Ticker Symbol:',
'LBL_OTHER_PHONE'=>'Telefoon Bedrijf:',
'LBL_ANY_PHONE'=>'Telefoon Prive :',
'LBL_MEMBER_OF'=>'Onderdeel van:',
'LBL_EMAIL'=>'E-mail:',
'LBL_EMPLOYEES'=>'Medewerkers:',
'LBL_OTHER_EMAIL_ADDRESS'=>'Bedrijfs e-mail:',
'LBL_ANY_EMAIL'=>'Prive e-mail:',
'LBL_OWNERSHIP'=>'Eigenaar:',
'LBL_RATING'=>'Beoordeling:',
'LBL_INDUSTRY'=>'Industrie:',
'LBL_SIC_CODE'=>'SBI code:',
'LBL_TYPE'=>'Type:',
'LBL_ANNUAL_REVENUE'=>'Jaaromzet:',
'LBL_ADDRESS_INFORMATION'=>'Adresinformatie',
'LBL_Quote_INFORMATION'=>'Accountinformatie',
'LBL_CUSTOM_INFORMATION'=>'Informatie',
'LBL_BILLING_ADDRESS'=>'Postadres:',
'LBL_SHIPPING_ADDRESS'=>'Afleveradres:',
'LBL_ANY_ADDRESS'=>'Privé adres:',
'LBL_CITY'=>'Plaats:',
'LBL_STATE'=>'Provincie:',
'LBL_POSTAL_CODE'=>'Postcode:',
'LBL_COUNTRY'=>'Land:',
'LBL_DESCRIPTION_INFORMATION'=>'Informatie',
'LBL_TERMS_INFORMATION'=>'Algemene Voorwaarden',
'LBL_DESCRIPTION'=>'Omschrijving:',
'NTC_COPY_BILLING_ADDRESS'=>'Kopieer Postadres naar Afleveradres',
'NTC_COPY_SHIPPING_ADDRESS'=>'Kopieer Afleveradres naar Postadres',
'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'Weet u zeker dat u dit wilt verwijderen?',
'LBL_DUPLICATE'=>'Mogelijk gedupliceerde accounts',
'MSG_DUPLICATE' => 'Deze optie kan mogelijk een gedupliceerde account aanmaken. Selecteer een bestaande account of maak een nieuwe !',

'LBL_INVITEE'=>'Contacten',
'ERR_DELETE_RECORD'=>"U moet een account selecteren om hem te verwijderen.",

'LBL_SELECT_ACCOUNT'=>'Selecteer account',
'LBL_GENERAL_INFORMATION'=>'Algemene informatie',

//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nieuwe verkoopkans',
'LBL_POTENTIAL_TITLE'=>'Verkoopkans',

'LBL_NEW_TASK'=>'Nieuwe taak',
'LBL_TASK_TITLE'=>'Taken',
'LBL_NEW_CALL'=>'Nieuw telefoongesprek',
'LBL_CALL_TITLE'=>'Telefoongesprekken',
'LBL_NEW_MEETING'=>'Nieuwe vergadering',
'LBL_MEETING_TITLE'=>'Vergaderingen',
'LBL_NEW_EMAIL'=>'Nieuwe e-mail',
'LBL_EMAIL_TITLE'=>'E-mail',
'LBL_NEW_CONTACT'=>'Nieuw contact',
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
'Subject'=>'Onderwerp',
'Quote Name'=>'Offertenaam',
'Vendor Name'=>'Naam Leverancier',
'Requisition No'=>'Incassonummer',
'Tracking Number'=>'Volgnummer',
'Contact Name'=>'Contactnaam',
'Due Date'=>'Leverdatum',
'Carrier'=>'Vervoerder',
'Type'=>'Type',
'Sales Tax'=>'BTW',
'Sales Commission'=>'Verkoopcommissie',
'Excise Duty'=>'Accijnzen', // inland taxes
'Total'=>'Totaal',
'Product Name'=>'Productnaam',
'Assigned To'=>'Toegewezen aan',
'Billing Address'=>'Postadres',
'Shipping Address'=>'Afleveradres',
'Billing City'=>'P Plaats',
'Billing State'=>'P Provincie',
'Billing Code'=>'P postcode',
'Billing Country'=>'P Land',
'Billing Po Box'=>'P Postbus',
'Shipping Po Box'=>'A Postbus',
'Shipping City'=>'A Plaats',
'Shipping State'=>'A Provincie',
'Shipping Code'=>'A Postcode',
'Shipping Country'=>'A Land',
'City'=>'Plaats',
'State'=>'Provincie',
'Code'=>'Postcode',
'Country'=>'Land',
'Created Time'=>'Gemaakt',
'Modified Time'=>'Gewijzigd',
'Description'=>'Omschrijving',
'Potential Name'=>'Verkoopkans naam',
'Customer No'=>'Klantnummer',
'Purchase Order'=>'Verkooporder',
'Vendor Terms'=>'Voorwaarden leverancier',
'Pending'=>'Wacht op',
'Account Name'=>'Accountnaam',
'Terms & Conditions'=>'Algemene Voorwaarden',
//Quote Info
'LBL_SO_INFORMATION'=>'Verkooporder information',
'LBL_SO'=>'Verkooporder:',

 //Added for 5.0 GA
'LBL_SO_FORM_TITLE'=>'Verkoop',
'LBL_SUBJECT_TITLE'=>'Onderwerp',
'LBL_VENDOR_NAME_TITLE'=>'Leveranciersnaam',
'LBL_TRACKING_NO_TITLE'=>'Trackingnummer:',
'LBL_SO_SEARCH_TITLE'=>'Verkooporder zoeken',
'LBL_QUOTE_NAME_TITLE'=>'Offertenaam',
'Order Id'=>'Ordernummer',
'LBL_MY_TOP_SO'=>'Mijn openstaande verkooporders',
'Status'=>'Status',
'SalesOrder'=>'Verkooporder',

//Added for existing Picklist Entries

'FedEx'=>'FedEx',
'UPS'=>'UPS',
'USPS'=>'TNT express',
'DHL'=>'DHL',
'BlueDart'=>'TNT post',

'Created'=>'Gemaakt',
'Approved'=>'Goedgekeurd',
'Delivered'=>'Geleverd',
'Cancelled'=>'Geannuleerd',
'Adjustment'=>'Bijstelling',
'Sub Total'=>'Subtotaal',
// added by Vicus
'AutoCreated'=>'AutoCreated',
'Sent'=>'Verzonden',
'Credit Invoice'=>'Creditnota',
'Paid'=>'Betaald',

//Added for Reports (5.0.4)
'Tax Type'=>'Belastingsoort',
'Discount Percent'=>'Kortingspercentage',
'Discount Amount'=>'Kortingsbedrag',
'Terms & Conditions'=>'Voorwaarden',
'S&H Amount'=>'Handling en Verzendtoeslag',

//Added after 5.0.4 GA
'SalesOrder No'=>'Verkooporder Nr',

'Recurring Invoice Information' => 'Periodiek factureren',
'Enable Recurring' => 'Activeer periodiek factureren',
'Frequency' => 'Frequentie',
'Start Period' => 'Start Periode',
'End Period' => 'Einde Periode',
'Payment Duration' => 'Betalingstermijn',
'Invoice Status' => 'Factuur Status',

'SINGLE_SalesOrder'=>'Verkooporder',

'Net 30 days' => 'Netto 30 dagen',
'Net 45 days' => 'Netto 45 dagen',
'Net 60 days' => 'Netto 60 dagen',


);

?>
