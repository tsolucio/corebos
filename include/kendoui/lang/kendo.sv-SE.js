/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Standard Swedish (sv-SE) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Johan Karlsson (https://github.com/DonKarlssonSan)
*                 
*
* This project is released to the public domain, although one must abide to the 
* licensing terms set forth by Telerik to use Kendo UI, as shown bellow.
*
* Telerik's original licensing terms:
* -----------------------------------
* Kendo UI Web commercial licenses may be obtained at
* https://www.kendoui.com/purchase/license-agreement/kendo-ui-web-commercial.aspx
* If you do not own a commercial license, this file shall be governed by the
* GNU General Public License (GPL) version 3.
* For GPL requirements, please review: http://www.gnu.org/copyleft/gpl.html
*/

kendo.ui.Locale = "Sverige (sv-SE)";
kendo.ui.ColumnMenu.prototype.options.messages =
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

      /* COLUMN MENU MESSAGES 
      ****************************************************************************/
      sortAscending: "Stigande",
      sortDescending: "Fallande",
      filter: "Filter",
      columns: "Kolumner"
      /***************************************************************************/
  });

kendo.ui.Groupable.prototype.options.messages =
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

      /* GRID GROUP PANEL MESSAGES 
      ****************************************************************************/
      empty: "Dra en kolumnrubrik och släpp den här för att gruppera på den kolumnen"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.messages =
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {

      /* FILTER MENU MESSAGES 
      ***************************************************************************/
      info: "Visa poster med",        // sets the text on top of the filter menu
      filter: "Filtrera",      // sets the text for the "Filter" button
      clear: "Ta bort",        // sets the text for the "Clear" button
      // when filtering boolean numbers
      isTrue: "Är sann", // sets the text for "isTrue" radio button
      isFalse: "Är falsk",     // sets the text for "isFalse" radio button
      //changes the text of the "And" and "Or" of the filter menu
      and: "Och",
      or: "Eller",
      selectValue: "-Välj ett värde-"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.operators =
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

      /* FILTER MENU OPERATORS (for each supported data type) 
      ****************************************************************************/
      string: {
          eq: "Lika med",
          neq: "Inte lika med",
          startswith: "Börjar med",
          contains: "Innehåller",
          døsnotcontain: "Innehåller inte",
          endswith: "Slutar med"
      },
      number: {
          eq: "Lika med",
          neq: "Inte lika med",
          gte: "Större än eller lika med",
          gt: "Större än",
          lte: "Mindre än eller lika med",
          lt: "Mindre än"
      },
      date: {
          eq: "Lika med",
          neq: "Inte lika med",
          gte: "Större än eller lika med",
          gt: "Större än",
          lte: "Mindre än eller lika med",
          lt: "Mindre än"

      },
      enums: {
          eq: "Lika med",
          neq: "Inte lika med"
      }
      /***************************************************************************/
  });

kendo.ui.Pager.prototype.options.messages =
  $.extend(kendo.ui.Pager.prototype.options.messages, {

      /* PAGER MESSAGES 
      ****************************************************************************/
      display: "{0} - {1} av {2} poster",
      empty: "Inga poster",
      page: "Sida",
      of: "av {0}",
      itemsPerPage: "Poster per sida",
      first: "Första sidan",
      previous: "Föregående sida",
      next: "Nästa sida",
      last: "Sista sidan",
      refresh: "Uppdatera"	
      /***************************************************************************/
  });

kendo.ui.Validator.prototype.options.messages =
  $.extend(kendo.ui.Validator.prototype.options.messages, {

      /* VALIDATOR MESSAGES 
      ****************************************************************************/
      required: "{0} är obligatorisk",
      pattern: "{0} är ogiltig",
      min: "{0} ska vara större än eller lika med {1}",
      max: "{0} ska vara mindre än eller lika med {1}",
      step: "{0} är ogiltig",
      email: "{0} är inte en giltig e-mail-adress",
      url: "{0} är inte en giltig URL",
      date: "{0} er inte ett giltigt datum"
      /***************************************************************************/
  });

kendo.ui.ImageBrowser.prototype.options.messages =
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

      /* IMAGE BROWSER MESSAGES 
      ****************************************************************************/
      uploadFile: "Skickar",
      orderBy: "Sortera efter",
      orderByName: "Namn",
      orderBySize: "Storlek",
      directoryNotFound: "Biblioteket kunde inte hittas.",
      emptyFolder: "Tom mapp",
      deleteFile: "Är du säker på du vill radera filen: \"{0}\"?",
      invalidFileType: "Det valda filformatet: \"{0}\" är ogiltigt. Giltiga filformat är {1}.",
      overwriteFile: "En fil med samma namn \"{0}\" existerar redan, vill du skriva över?",
      dropFilesHere: "Släpp filer här"
      /***************************************************************************/
  });

kendo.ui.Editor.prototype.options.messages =
  $.extend(kendo.ui.Editor.prototype.options.messages, {

      /* EDITOR MESSAGES 
      ****************************************************************************/
      bold: "Fet",
      italic: "Kursiv",
      underline: "Understruket",
      strikethrough: "Genomstruket",
      superscript: "Framhävd",
      subscript: "Sänkt",
      justifyCenter: "Centrerat",
      justifyLeft: "Vänstrejustera",
      justifyRight: "Höjrejustera",
      justifyFull: "Lika marginaler",
      insertUnorderedList: "Klistra in osorterad lista",
      insertOrderedList: "Klistra in sorterad lista",
      indent: "Öka indrag",
      outdent: "Minska indrag",
      createLink: "Skapa länk",
      unlink: "Radera länk",
      insertImage: "Klistra in bild",
      insertHtml: "Klistra in HTML",
      fontName: "Font",
      fontNameInherit: "(Ärvd font)",
      fontSize: "Fontstorlek",
      fontSizeInherit: "(Ärvd fontstorlek)",
      formatBlock: "Format",
      foreColor: "Färg",
      backColor: "Bakgrundsfärg",
      style: "Stil",
      emptyFolder: "Tom mapp",
      uploadFile: "Skickar",
      orderBy: "Sortera efter:",
      orderBySize: "Storlek",
      orderByName: "Namn",
      invalidFileType: "Det valda filformatet: \"{0}\" är ogiltigt. Giltiga filformat är {1}.",
      deleteFile: "Är du säker på du vill radera filen: \"{0}\"?",
      overwriteFile: "En fil med samma namn \"{0}\" existerar redan, vill du skriva över?",
      directoryNotFound: "Biblioteket kunde inte hittas.",
      imageWebAddress: "Internet adress",
      imageAltText: "Alternativ Text",
      dialogInsert: "Ersätt",
      dialogButtonSeparator: "eller",
      dialogCancel: "Avbryt"
      /***************************************************************************/
  });
