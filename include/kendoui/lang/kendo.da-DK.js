/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Standard Danish (da-DK) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Klaus Nielsen
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

kendo.ui.Locale = "Danmark (da-DK)";
kendo.ui.ColumnMenu.prototype.options.messages =
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

      /* COLUMN MENU MESSAGES 
      ****************************************************************************/
      sortAscending: "Stigende",
      sortDescending: "Faldende",
      filter: "Filter",
      columns: "Kolonner"
      /***************************************************************************/
  });

kendo.ui.Groupable.prototype.options.messages =
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

      /* GRID GROUP PANEL MESSAGES 
      ****************************************************************************/
      empty: "Træk en kolonneoverskrift og slip den her for at gruppere efter denne kolonne"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.messages =
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {

      /* FILTER MENU MESSAGES 
      ***************************************************************************/
      info: "Vis poster med",        // sets the text on top of the filter menu
      filter: "Filtrer",      // sets the text for the "Filter" button
      clear: "Fjern",        // sets the text for the "Clear" button
      // when filtering boolean numbers
      isTrue: "Er sand", // sets the text for "isTrue" radio button
      isFalse: "Er falsk",     // sets the text for "isFalse" radio button
      //changes the text of the "And" and "Or" of the filter menu
      and: "Og",
      or: "Eller",
      selectValue: "-Vælg en værdi-"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.operators =
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

      /* FILTER MENU OPERATORS (for each supported data type) 
      ****************************************************************************/
      string: {
          eq: "Lig med",
          neq: "Ikke lig med",
          startswith: "Starter med",
          contains: "Indeholder",
          døsnotcontain: "Indeholder ikke",
          endswith: "Ender med"
      },
      number: {
          eq: "Lig med",
          neq: "Ikke lig med",
          gte: "Større end eller lig med",
          gt: "Større end",
          lte: "Mindre end eller lig med",
          lt: "Mindre end"
      },
      date: {
          eq: "Lig med",
          neq: "Ikke lig med",
          gte: "Større end eller lig med",
          gt: "Større end",
          lte: "Mindre end eller lig med",
          lt: "Er mindre"
      },
      enums: {
          eq: "Lig med",
          neq: "Ikke lig med"
      }
      /***************************************************************************/
  });

kendo.ui.Pager.prototype.options.messages =
  $.extend(kendo.ui.Pager.prototype.options.messages, {

      /* PAGER MESSAGES 
      ****************************************************************************/
      display: "{0} - {1} af {2} poster",
      empty: "Ingen poster",
      page: "Side",
      of: "af {0}",
      itemsPerPage: "Poster pr. side",
      first: "Første side",
      previous: "Forrige side",
      next: "Næste side",
      last: "Sidste side",
      refresh: "Opdater"
      /***************************************************************************/
  });

kendo.ui.Validator.prototype.options.messages =
  $.extend(kendo.ui.Validator.prototype.options.messages, {

      /* VALIDATOR MESSAGES 
      ****************************************************************************/
      required: "{0} er obligatorisk",
      pattern: "{0} er ugyldig",
      min: "{0} skal være større end eller lig med {1}",
      max: "{0} skal være mindre end eller lig med {1}",
      step: "{0} er ugyldig",
      email: "{0} er ikke en gyldig e-mail adresse",
      url: "{0} er ikke en gyldig URL",
      date: "{0} er ikke en gyldig dato"
      /***************************************************************************/
  });

kendo.ui.ImageBrowser.prototype.options.messages =
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

      /* IMAGE BROWSER MESSAGES 
      ****************************************************************************/
      uploadFile: "Sender",
      orderBy: "Sorter efter",
      orderByName: "Navn",
      orderBySize: "Størrelse",
      directoryNotFound: "Biblioteket blev ikke fundet.",
      emptyFolder: "Tom mappe",
      deleteFile: "Er du sikker på du vil slette filen: \"{0}\"?",
      invalidFileType: "Det valgte filformat: \"{0}\" er ugyldigt. Understøttede filformater er {1}.",
      overwriteFile: "En fil med samme navn \"{0}\" eksisterer i forvejen, vil du overskrive?",
      dropFilesHere: "Slip filer her"
      /***************************************************************************/
  });

kendo.ui.Editor.prototype.options.messages =
  $.extend(kendo.ui.Editor.prototype.options.messages, {

      /* EDITOR MESSAGES 
      ****************************************************************************/
      bold: "Fed",
      italic: "Kursiv",
      underline: "Understreget",
      strikethrough: "Gennemstreget",
      superscript: "Fremhævet",
      subscript: "Sænket",
      justifyCenter: "Centret",
      justifyLeft: "Venstrejuster",
      justifyRight: "Højrejuster",
      justifyFull: "Lige margener",
      insertUnorderedList: "Indsæt uordnet liste",
      insertOrderedList: "Indsæt ordnet liste",
      indent: "Forøg indrykning",
      outdent: "Formindsk indrykning",
      createLink: "Opret link",
      unlink: "Fjern link",
      insertImage: "Indsæt billede",
      insertHtml: "Indsæt HTML",
      fontName: "Skrifttype",
      fontNameInherit: "(Nedarv skrifttype)",
      fontSize: "Skriftstørrelse",
      fontSizeInherit: "(Nedarv skriftstørrelse)",
      formatBlock: "Format",
      foreColor: "Farve",
      backColor: "Baggrundsfarve",
      style: "Stil",
      emptyFolder: "Tom mappe",
      uploadFile: "Sender",
      orderBy: "Sorter efter:",
      orderBySize: "Størrelse",
      orderByName: "Navn",
      invalidFileType: "Det valgte filformat: \"{0}\" er ugyldigt. Understøttede filformater er {1}.",
      deleteFile: "Er du sikker på du vil slette filen: \"{0}\"?",
      overwriteFile: "En fil med samme navn \"{0}\" eksisterer i forvejen, vil du overskrive?",
      directoryNotFound: "Biblioteket blev ikke fundet.",
      imageWebAddress: "Internet adresse",
      imageAltText: "Alternativ tekst",
      dialogInsert: "Indsæt",
      dialogButtonSeparator: "eller",
      dialogCancel: "Annuller"
      /***************************************************************************/
  });
