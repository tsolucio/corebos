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
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/modules/PurchaseOrder/language/nl_nl.lang.php,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/

$mod_strings = Array(
'LBL_MODULE_NAME'=>'Inkooporder',
'LBL_RELATED_PRODUCTS'=>'Productdetails',
'LBL_MODULE_TITLE'=>'Inkooporder: Home',
'LBL_SEARCH_FORM_TITLE'=>'Zoek inkooporder',
'LBL_LIST_FORM_TITLE'=>'Inkooporder lijst',
'LBL_NEW_FORM_TITLE'=>'Nieuwe inkooporder',
'LBL_MEMBER_ORG_FORM_TITLE'=>'Leden organisatie',

'LBL_LIST_ACCOUNT_NAME'=>'Accountnaam',
'LBL_LIST_CITY'=>'Plaats',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_STATE'=>'Provincie',
'LBL_LIST_PHONE'=>'Telefoon',
'LBL_LIST_EMAIL_ADDRESS'=>'e-mailadres',
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
'LBL_TICKER_SYMBOL'=>'Ticker Symbool:',
'LBL_OTHER_PHONE'=>'Telefoon Mobiel:',
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
'LBL_Quote_INFORMATION'=>'Accountinformatie',
'LBL_CUSTOM_INFORMATION'=>'Klantinformatie',
'LBL_BILLING_ADDRESS'=>'Postadres:',
'LBL_SHIPPING_ADDRESS'=>'Afleveradres:',
'LBL_ANY_ADDRESS'=>'Postadres:',
'LBL_CITY'=>'Plaats:',
'LBL_STATE'=>'Provincie:',
'LBL_POSTAL_CODE'=>'Postcode:',
'LBL_COUNTRY'=>'Land:',
'LBL_DESCRIPTION_INFORMATION'=>'Omschrijving',
'LBL_TERMS_INFORMATION'=>'Algemene Voorwaarden',
'LBL_DESCRIPTION'=>'Omschrijving:',
'NTC_COPY_BILLING_ADDRESS'=>'Kopieer Postadres naar Afleveradres',
'NTC_COPY_SHIPPING_ADDRESS'=>'Kopieer Afleveradres naar Postadres',
'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'Weet u zeker dat u dit veld wilt verwijderen als Onderdeel van de organisatie?',
'LBL_DUPLICATE'=>'Mogelijk dubbel Account',
'MSG_DUPLICATE' => 'Bij het aanmaken van dit account creert u waarschijnlijk een duplicaat van de accountgegevens. U kunt een account selecteren van de lijst of u klikt op nieuw account om verder te gaan met de ingevoerde gegevens.',

'LBL_INVITEE'=>'Contacten',
'ERR_DELETE_RECORD'=>"Een veld moet gespecificeerd zijn om de accountgegevens te verwijderen.",

'LBL_SELECT_ACCOUNT'=>'Selecteer account',
'LBL_GENERAL_INFORMATION'=>'Algemene informatie',

//for v4 release added
'LBL_NEW_POTENTIAL'=>'Nieuw potentieel',
'LBL_POTENTIAL_TITLE'=>'Potentielen',

'LBL_NEW_TASK'=>'Nieuwe taak',
'LBL_TASK_TITLE'=>'Taken',
'LBL_NEW_CALL'=>'Nieuw telefoongesprek',
'LBL_CALL_TITLE'=>'Telefoongesprekken',
'LBL_NEW_MEETING'=>'Nieuwe vergaderingen',
'LBL_MEETING_TITLE'=>'Vergaderingen',
'LBL_NEW_EMAIL'=>'Nieuwe e-mail',
'LBL_EMAIL_TITLE'=>'E-mail',
'LBL_NEW_CONTACT'=>'Nieuw contact',
'LBL_CONTACT_TITLE'=>'Contacten',

//Added fields after RC1 - Release
'LBL_ALL'=>'Alles',
'LBL_PROSPECT'=>'Prospect',
'LBL_INVESTOR'=>'Investeerder',
'LBL_RESELLER'=>'Wederverkoper',
'LBL_PARTNER'=>'Partner',

// Added for 4GA
'LBL_TOOL_FORM_TITLE'=>'Account gereedschap',
//Added for 4GA
'Subject'=>'Onderwerp',
'Quote Name'=>'Offerte naam',
'Vendor Name'=>'Leveranciersnaam',
'Requisition No'=>'Vorderingsnummer',
'Tracking Number'=>'Volgnummer',
'Contact Name'=>'Contactnaam',
'Due Date'=>'Vervaldatum',
'Carrier'=>'Vervoerder',
'Type'=>'Type',
'Sales Tax'=>'Verkoopbelasting',
'Sales Commission'=>'Verkoopcommissie',
'Excise Duty'=>'Accijnzen', // inland taxes
'Total'=>'Totaal',
'Product Name'=>'Productnaam',
'Assigned To'=>'Toegewezen aan',
'Billing Address'=>'P Postadres',
'Shipping Address'=>'Afleveradres',
'Billing City'=>'P Plaats',
'Billing State'=>'P Provincie',
'Billing Code'=>'P Postcode',
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
'Potential Name'=>'Naam potentieel',
'Customer No'=>'Klantnummer',
'Purchase Order'=>'Inkooporder',
'Vendor Terms'=>'Leveranciersvoorwaarden',
'Pending'=>'Wacht op',
'Account Name'=>'Accountnaam',
'Terms & Conditions'=>'Algemene Voorwaarden',
//Quote Info
'LBL_PO_INFORMATION'=>'Inkooporder informatie',
'LBL_PO'=>'Inkooporder:',

 //Added for 4.2 GA
'LBL_SO_FORM_TITLE'=>'Verkoop',
'LBL_PO_FORM_TITLE'=>'Inkoop',
'LBL_SUBJECT_TITLE'=>'Onderwerp',
'LBL_VENDOR_NAME_TITLE'=>'Leveranciersnaam',
'LBL_TRACKING_NO_TITLE'=>'Volgnummer:',
'LBL_PO_SEARCH_TITLE'=>'Zoek inkooporder',
'LBL_SO_SEARCH_TITLE'=>'Zoek verkooporder',
'LBL_QUOTE_NAME_TITLE'=>'Offertenaam',
'Order Id'=>'Ordernummer',
'Status'=>'Status',
'PurchaseOrder'=>'Inkooporder',
'LBL_MY_TOP_PO'=>'Mijn openstaande inkooporders',

//Added for existing Picklist Entries

'FedEx'=>'FedEx',
'UPS'=>'UPS',
'USPS'=>'TNT express',
'DHL'=>'DHL',
'BlueDart'=>'TNT post',

'Created'=>'Gemaakt',
'Approved'=>'Akkoord',
'Delivered'=>'Geleverd',
'Cancelled'=>'Geannuleerd',
'Received Shipment'=>'Goederen ontvangen',

//Added for Reports (5.0.4)
'Tax Type'=>'Belastingsoort',
'Discount Percent'=>'Kortingspercentage',
'Discount Amount'=>'Kortingsbedrag',
'Terms & Conditions'=>'Voorwaarden',
'Adjustment'=>'Bijstelling',
'Sub Total'=>'Subtotaal',
'S&H Amount'=>'Handling en Verzendtoeslag',

//Added after 5.0.4 GA
'PurchaseOrder No'=>'Inkoopordernummer',
'SINGLE_PurchaseOrder'=>'Inkooporder',
);

?>
