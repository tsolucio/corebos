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
 * $Header$
 * Description:  Defines the English language pack for Sales Order
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$mod_strings = Array(
    'LBL_MODULE_NAME'=>'Bestellungen',
    'LBL_SO_MODULE_NAME'=>'Verkäufe',
    'LBL_RELATED_PRODUCTS'=>'Artikel',
    'LBL_MODULE_TITLE'=>'Bestellungen: Home',
    'LBL_SEARCH_FORM_TITLE'=>'Bestellungen suchen',
    'LBL_LIST_SO_FORM_TITLE'=>'Verkäufe',
    'LBL_NEW_FORM_SO_TITLE'=>'Neuer Verkauf',
    'LBL_MEMBER_ORG_FORM_TITLE'=>'Mitglied von',

    'LBL_LIST_ACCOUNT_NAME'=>'Organisation',
    'LBL_LIST_CITY'=>'Ort',
    'LBL_LIST_WEBSITE'=>'Webseite',
    'LBL_LIST_STATE'=>'Bundesland',
    'LBL_LIST_PHONE'=>'Telefon',
    'LBL_LIST_EMAIL_ADDRESS'=>'E-Mail',
    'LBL_LIST_CONTACT_NAME'=>'Person',

    //DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_name' => 'LBL_LIST_ACCOUNT_NAME',
    'db_website' => 'LBL_LIST_WEBSITE',
    'db_billing_address_city' => 'LBL_LIST_CITY',

    //END DON'T CONVERT

    'LBL_ACCOUNT'=>'Organisation:',
    'LBL_ACCOUNT_NAME'=>'Organisation:',
    'LBL_PHONE'=>'Telefon:',
    'LBL_WEBSITE'=>'Webseite:',
    'LBL_FAX'=>'Fax:',
    'LBL_TICKER_SYMBOL'=>'Org. Namenszusatz:',
    'LBL_OTHER_PHONE'=>'anderes Telefon:',
    'LBL_ANY_PHONE'=>'weiteres Telefon:',
    'LBL_MEMBER_OF'=>'Mitglied von:',
    'LBL_EMAIL'=>'E-Mail:',
    'LBL_EMPLOYEES'=>'Angestellte:',
    'LBL_OTHER_EMAIL_ADDRESS'=>'andere E-Mail:',
    'LBL_ANY_EMAIL'=>'weitere E-Mail:',
    'LBL_OWNERSHIP'=>'Besitzer:',
    'LBL_RATING'=>'Bewertung:',
    'LBL_INDUSTRY'=>'Branche:',
    'LBL_SIC_CODE'=>'SIC Code:',
    'LBL_TYPE'=>'Typ:',
    'LBL_ANNUAL_REVENUE'=>'Jahresumsatz:',
    'LBL_ADDRESS_INFORMATION'=>'Addresse',
    'LBL_Quote_INFORMATION'=>'Organisation',
    'LBL_CUSTOM_INFORMATION'=>'zusätzliche Information',
    'LBL_BILLING_ADDRESS'=>'Rechnungsadressel:',
    'LBL_SHIPPING_ADDRESS'=>'Lieferadresse:',
    'LBL_ANY_ADDRESS'=>'Weitere Addresse:',
    'LBL_CITY'=>'Ort:',
    'LBL_STATE'=>'Bundesland:',
    'LBL_POSTAL_CODE'=>'PLZ:',
    'LBL_COUNTRY'=>'Land:',
    'LBL_DESCRIPTION_INFORMATION'=>'Zusatzinformationen',
    'LBL_TERMS_INFORMATION'=>'Liefer- und Zahlungsbedingungen',
    'LBL_DESCRIPTION'=>'Beschreibung:',
    'NTC_COPY_BILLING_ADDRESS'=>'Kopiere Rechnungsadressea auf Lieferadresse',
    'NTC_COPY_SHIPPING_ADDRESS'=>'Kopiere Lieferadresse auf Rechnungsadresse',
    'NTC_REMOVE_MEMBER_ORG_CONFIRMATION'=>'Möchten Sie diesen Eintrag löschen?',
    'LBL_DUPLICATE'=>'Eventuell doppelte Organisation angelegt',
    'MSG_DUPLICATE' => 'Das Anlegen dieser Organisation führt möglicherweise zu einer doppelten Eintragung. Sie können entweder mit der Auswahl einer Organisation aus der untenstehenden Liste fortfahren oder einen neue Organisation anlegen.',

    'LBL_INVITEE'=>'Personen',
    'ERR_DELETE_RECORD'=>"Zum Löschen muss mindestens ein Eintrag markiert sein.",

    'LBL_SELECT_ACCOUNT'=>'Organisation wählen',
    'LBL_GENERAL_INFORMATION'=>'Allgemein',

    //for v4 release added
    'LBL_NEW_POTENTIAL'=>'Neues Verkaufspotential',
    'LBL_POTENTIAL_TITLE'=>'Verkaufspotentiale',

    'LBL_NEW_TASK'=>'Neue Aufgabe',
    'LBL_TASK_TITLE'=>'Aufgaben',
    'LBL_NEW_CALL'=>'Neuer Anruf',
    'LBL_CALL_TITLE'=>'Anrufe',
    'LBL_NEW_MEETING'=>'Neues Meeting',
    'LBL_MEETING_TITLE'=>'Meetings',
    'LBL_NEW_EMAIL'=>'Neue E-Mail',
    'LBL_EMAIL_TITLE'=>'E-Mails',
    'LBL_NEW_CONTACT'=>'Neue Person',
    'LBL_CONTACT_TITLE'=>'Personen',

    //Added fields after RC1 - Release
    'LBL_ALL'=>'All',
    'LBL_PROSPECT'=>'Potentieller Kunde',
    'LBL_INVESTOR'=>'Investor',
    'LBL_RESELLER'=>'Wiederverkäufer',
    'LBL_PARTNER'=>'Partner',

    // Added for 4GA
    'LBL_TOOL_FORM_TITLE'=>'Account Tools',
    //Added for 4GA
    'Subject'=>'Titel',
    'Quote Name'=>'Angebot',
    'Vendor Name'=>'Lieferant',
    'Requisition No'=>'Bestellnummer',
    'Tracking Number'=>'Bedarfsnummer',
    'Contact Name'=>'Person',
    'Due Date'=>'Lieferdatum',
    'Carrier'=>'Transporteur',
    'Type'=>'Typ',
    'Sales Tax'=>'Verkaufssteuer',
    'Sales Commission'=>'Provision',
    'Excise Duty'=>'Abgaben',
    'Total'=>'Total',
    'Product Name'=>'Produkt',
    'Assigned To'=>'zuständig',
    'Billing Address'=>'Rechnungsadresse Strasse',
    'Shipping Address'=>'Lieferadresse Strasse',
    'Billing City'=>'Rechnungsadresse Ort',
    'Billing State'=>'Rechnungsadresse Bundesland',
    'Billing Code'=>'Rechnungsadresse PLZ',
    'Billing Country'=>'Rechnungsadresse Land',
    'Billing Po Box'=>'Rechnungsadresse Postfachnr.',
    'Shipping Po Box'=>'Lieferadresse Postfachnr.',
    'Shipping City'=>'Lieferadresse Ort',
    'Shipping State'=>'Lieferadresse Bundesland',
    'Shipping Code'=>'Lieferadresse PLZ',
    'Shipping Country'=>'Lieferadresse Land',
    'City'=>'Ort',
    'State'=>'Bundesland',
    'Code'=>'PLZ',
    'Country'=>'Land',
    'Created Time'=>'erstellt',
    'Modified Time'=>'geändert',
    'Description'=>'Beschreibung',
    'Potential Name'=>'Verkaufspotential',
    'Customer No'=>'Kundenzeichen',
    'Purchase Order'=>'Bestellnummer',
    'Vendor Terms'=>'Lieferbedingungen',
    'Pending'=>'hängig',
    'Account Name'=>'Organisation',
    'Terms & Conditions'=>'Zahlungs- und Lieferbedingungen',
    //Quote Info
    'LBL_SO_INFORMATION'=>'Verkauf',
    'LBL_SO'=>'Verkauf:',

    //Added for 4.2 GA
    'LBL_SO_FORM_TITLE'=>'Verkauf',
    'LBL_SUBJECT_TITLE'=>'Titel',
    'LBL_VENDOR_NAME_TITLE'=>'Lieferant',
    'LBL_TRACKING_NO_TITLE'=>'Bestellnummer:',
    'LBL_SO_SEARCH_TITLE'=>'Verkäufe suchen',
    'LBL_QUOTE_NAME_TITLE'=>'Angebotsname',
    'Order No'=>'Bestellnr.',
    'LBL_MY_TOP_SO'=>'Meine wichtigsten Verkäufe',
    'Status'=>'Status',
    'SalesOrder'=>'Verkaufsbestellung',

    //Added for existing Picklist Entries

    'FedEx'=>'FedEx',
    'UPS'=>'UPS',
    'USPS'=>'USPS',
    'DHL'=>'DHL',
    'BlueDart'=>'Post',

    'Created'=>'erstellt',
    'Approved'=>'bestätigt',
    'Delivered'=>'geliefert',
    'Cancelled'=>'abgebrochen',
    'Adjustment'=>'Anpassung',
    'Sub Total'=>'Zwischensumme',
    'AutoCreated'=>'automatisch',
    'Sent'=>'gesendet',
    'Credit Invoice'=>'Rechnung erstellen',
    'Paid'=>'bezahlt',


    //Added for Reports (5.0.4)
    'Tax Type'=>'Steuertyp',
    'Discount Percent'=>'Rabatt (%)',
    'Discount Amount'=>'Rabatt',
    'S&H Amount'=>'Versandkosten',

    //Added after 5.0.4 GA
    'SalesOrder No'=>'Verkaufsbestellung Nr.',

    'Recurring Invoice Information' => 'Informationen für wiederkehrende Rechnungen',
    'Enable Recurring' => 'Wiederholung zulassen',
    'Frequency' => 'Frequenz',
    'Start Period' => 'Start',
    'End Period' => 'Ende',
    'Payment Duration' => 'Zahlungsbedingung',
    'Invoice Status' => 'Rechnungsstatus',

    'SINGLE_SalesOrder'=>'Sales Order',
    'Net 30 days' => 'Netto 30 Tage',
    'Net 45 days' => 'Netto 45 Tage',
    'Net 60 days' => 'Netto 60 Tage',
    'SalesOrder ID' => 'Verkaufsbestellungs-ID',

    'Terms & Conditions'=>'Geschäftsbedingungen',

    '--None--'=>'--ohne--',
    'Daily'=>'täglich',
    'Weekly'=>'wöchentlich',
    'Monthly'=>'monatlich',
    'Quarterly'=>'quartalsweise',
    'Yearly'=>'jährlich',
);

?>