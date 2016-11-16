/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Nederlands BE (nl-BE) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Felipe Machado (Loudenvier) 
*                 http://feliperochamachado.com.br/index_en.html
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

kendo.ui.Locale = "Nederlands NL (nl-BE)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
  sortAscending: "Sorteer Oplopend",
  sortDescending: "Sorteer Aflopend",
  filter: "Filteren",
  columns: "Kolommen"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
  empty: "Sleep hier een kolomnaam om te groeperen op deze kolom"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
  info: "Laat items zien met een waarde die:", // sets the text on top of the filter menu
  isTrue: "juist is",                   // sets the text for "isTrue" radio button
  isFalse: "fout is",                 // sets the text for "isFalse" radio button
  filter: "Filteren",                    // sets the text for the "Filter" button
  clear: "Leegmaken",                      // sets the text for the "Clear" button
  and: "En",
  or: "Of",
  selectValue: "-Selecteer een waarde-"
 /***************************************************************************/   
});
         
kendo.ui.FilterMenu.prototype.options.operators =           
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
  string: {
      eq: "Gelijk is aan",
      neq: "Niet gelijk is aan",
      startswith: "Start met",
      contains: "Bevat",
      doesnotcontain: "Niet bevat",
      endswith: "Eindigd op"
  },
  number: {
      eq: "Gelijk is aan",
      neq: "Niet gelijk is aan",
      gte: "Groter dan of gelijk is aan",
      gt: "Groter is dan",
      lte: "Kleiner dan of gelijk is aan",
      lt: "Kleiner is dan"
  },
  date: {
      eq: "Gelijk is aan",
      neq: "Niet gelijk is aan",
      gte: "Later of gelijk is aan",
      gt: "Later is dan",
      lte: "Vroeger of gelijk is aan",
      lt: "Vroeger is dan"
  },
  enums: {
      eq: "Gelijk is aan",
      neq: "Niet gelijk is aan"
  }
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
  $.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
  display: "{0} - {1} van {2} items",
  empty: "Geen items om weer te geven",
  page: "Pagina",
  of: "van {0}",
  itemsPerPage: "items per pagina",
  first: "Ga naar de eerste pagina",
  previous: "Ga naar de vorige pagina",
  next: "Ga naar de volgende pagina",
  last: "Ga naar de laatste pagina",
  refresh: "Vernieuwen"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
  $.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
  required: "{0} is verplicht",
  pattern: "{0} is ongeldig",
  min: "{0} moet groter zijn of gelijk aan {1}",
  max: "{0} moet kleiner zijn of gelijk aan {1}",
  step: "{0} is ongeldig",
  email: "{0} is een ongeldig email adres",
  url: "{0} is een ongeldige URL",
  date: "{0} is een foutieve datum"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
  uploadFile: "Uploaden",
  orderBy: "Sorteren op",
  orderByName: "Naam",
  orderBySize: "Grootte",
  directoryNotFound: "Kon geen folder vinden met deze naam.",
  emptyFolder: "Lege folder",
  deleteFile: 'Bent u zeker dat u "{0}" wenst te deleten?',
  invalidFileType: "Het geselecteerde bestand \"{0}\" is ongeldig. Geldige bestandstypes zijn {1}.",
  overwriteFile: "Een bestand met naam \"{0}\" bestaat reeds in de huidge folder. Wenst u dit te overschrijven?",
  dropFilesHere: "Sleep hier uw files om te uploaden"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
  $.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
  bold: "Vet",
  italic: "Cursief",
  underline: "Onderlijnen",
  strikethrough: "Doorstrepen",
  superscript: "Superscript",
  subscript: "Subscript",
  justifyCenter: "Centreren",
  justifyLeft: "Links uitlijnen",
  justifyRight: "Rechts uitlijnen",
  justifyFull: "Uitlijnen",
  insertUnorderedList: "Ongeordende lijst toevoegen",
  insertOrderedList: "Geordende lijst toevoegen",
  indent: "Inspringen",
  outdent: "Uitspringen",
  createLink: "Hyperlink toevoegen",
  unlink: "Hyperlink verwijderen",
  insertImage: "Afbeelding toevoegen",
  insertHtml: "HTML toevoegen",
  fontName: "Selecteer een lettertype",
  fontNameInherit: "(basis lettertype)",
  fontSize: "Selecteer grootte van lettertype",
  fontSizeInherit: "(basis grootte)",
  formatBlock: "Structuur",
  foreColor: "Kleur",
  backColor: "Achtergrondkleur",
  style: "Stijlen",
  emptyFolder: "Lege Folder",
  uploadFile: "Uploaden",
  orderBy: "Sorteren op:",
  orderBySize: "Grootte",
  orderByName: "Naam",
  invalidFileType: "Het geselecteerde bestand \"{0}\" is ongeldig. Geldige bestandtypes zijn {1}.",
  deleteFile: 'Bent u zeker dat u "{0}" wenst te deleten?',
  overwriteFile: 'Een bestand met naam \"{0}\" bestaat reeds in de huidge folder. Wenst u dit te overschrijven?',
  directoryNotFound: "Kon geen folder vinden met deze naam.",
  imageWebAddress: "Web adres",
  imageAltText: "Alternatieve tekst",
  dialogInsert: "Toevoegen",
  dialogButtonSeparator: "of",
  dialogCancel: "Annuleren"
 /***************************************************************************/   
});
