/*******************************************************************************
 * Vicus eBusiness Solutions Version Control
 * @package 	NL-Dutch
 * Description	Dutch language pack for vtiger CRM version 5.3.x
 * @author	$Author: luuk $
 * @version 	$Revision: 1.2 $ $Date: 2011/11/13 08:02:24 $
 * @source	$Source: /var/lib/cvs/vtiger530/Dutch/jscalendar/lang/calendar-nl.js,v $
 * @copyright	Copyright (c)2005-2011 Vicus eBusiness Solutions bv <info@vicus.nl>
 * @license	vtiger CRM Public License Version 1.0 (by definition)
 ********************************************************************************/

// full day names
Calendar._DN = new Array
("Zondag",
 "Maandag",
 "Dinsdag",
 "Woensdag",
 "Donderdag",
 "Vrijdag",
 "Zaterdag",
 "Zondag");
 
// short day names
Calendar._SDN = new Array
("Zo",
 "Ma",
 "Di",
 "Wo",
 "Do",
 "Fr",
 "Za",
 "Zo"); 

// short day names only use 2 letters instead of 3
Calendar._SDN_len = 2;

// full month names
Calendar._MN = new Array
("Januari",
 "Februari",
 "Maart",
 "April",
 "Mei",
 "Juni",
 "Juli",
 "Augustus",
 "September",
 "Oktober",
 "November",
 "December");

// short month names
Calendar._SMN = new Array
("Jan",
 "Feb",
 "Mrt",
 "Apr",
 "Mei",
 "Jun",
 "Jul",
 "Aug",
 "Sep",
 "Okt",
 "Nov",
 "Dec");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "Over de Kalender";

Calendar._TT["ABOUT"] =
"DHTML Datum/Tijd Selector\n" +
"(c) dynarch.com 2002-2003\n" + // don't translate this this ;-)
"For latest version visit: http://dynarch.com/mishoo/calendar.epl\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Datum selectie:\n" +
"- Gebruik de \xab, \xbb knoppen om het jaar te selecteren\n" +
"- Gebruik de " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " knoppen om de maand te selecteren\n" +
"- Houd muis knop ingedrukt voor snelle selectie.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Tijd selectie:\n" +
"- Klik op elk van de tijdsgedeelten om te verhogen\n" +
"- of Shift-klik om te verlagen\n" +
"- of klik en sleep voor snelle selectie.";

Calendar._TT["TOGGLE"] = "Selecteer de eerste week-dag";
Calendar._TT["PREV_YEAR"] = "Vorig jaar (ingedrukt voor menu)";
Calendar._TT["PREV_MONTH"] = "Vorige maand (ingedrukt voor menu)";
Calendar._TT["GO_TODAY"] = "Ga naar Vandaag";
Calendar._TT["NEXT_MONTH"] = "Volgende maand (ingedrukt voor menu)";
Calendar._TT["NEXT_YEAR"] = "Volgend jaar (ingedrukt voor menu)";
Calendar._TT["SEL_DATE"] = "Selecteer datum";
Calendar._TT["DRAG_TO_MOVE"] = "Klik en sleep om te verplaatsen";
Calendar._TT["PART_TODAY"] = " (vandaag)";
Calendar._TT["MON_FIRST"] = "Toon Maandag eerst";
Calendar._TT["SUN_FIRST"] = "Toon Zondag eerst";
Calendar._TT["CLOSE"] = "Sluiten";
Calendar._TT["TODAY"] = "Vandaag";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "%s eerste dag van de week";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["TIME_PART"] = "(Shift-)vasthouden en dan klikken om te veranderen";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "wk";
Calendar._TT["TIME"] = "Tijd:";
