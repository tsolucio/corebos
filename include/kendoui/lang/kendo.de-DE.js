/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Standard German (de-DE) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Claudio Mertz
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

kendo.ui.Locale = "Deutschland (de-DE)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
  sortAscending: "Aufsteigend",
  sortDescending: "Absteigend",
  filter: "Filter",
  columns: "Spalten"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
  empty: "Keine Einträge vorhanden"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
  info: "Zeige Einträge mit",        // sets the text on top of the filter menu
	filter: "Filtern",      // sets the text for the "Filter" button
	clear: "Löschen",        // sets the text for the "Clear" button
	// when filtering boolean numbers
	isTrue: "Ist wahr", // sets the text for "isTrue" radio button
	isFalse: "Ist falsch",     // sets the text for "isFalse" radio button
	//changes the text of the "And" and "Or" of the filter menu
	and: "UND",
	or: "ODER",
  selectValue: "-Wählen Sie einen Wert-"
 /***************************************************************************/   
});
         
kendo.ui.FilterMenu.prototype.options.operators =           
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
  string: {
      eq: "Ist gleich",
      neq: "Ist ungleich",
      startswith: "Beginnt mit",
      contains: "Enthält",
      doesnotcontain: "Enthält nicht",
      endswith: "Endet mit"
  },
  number: {
      eq: "Ist gleich",
      neq: "Ist ungleich",
      gte: "Ist größer oder gleich",
      gt: "Ist größer",
      lte: "Ist kleiner oder gleich",
      lt: "Ist kleiner"
  },
  date: {
      eq: "Ist gleich",
      neq: "Ist ungleich",
      gte: "Ist größer oder gleich",
      gt: "Ist größer",
      lte: "Ist kleiner oder gleich",
      lt: "Ist kleiner"
  },
  enums: {
      eq: "Ist gleich",
      neq: "Ist ungleich"
  }
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
  $.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
  display: "{0} - {1} von {2} Einträgen",
  empty: "Keine Einträge",
  page: "Seite",
  of: "von {0}",
  itemsPerPage: "Einträge pro Seite",
  first: "Erste Seite",
  previous: "Vorherige Seite",
  next: "Nächste Seite",
  last: "Letzte Seite",
  refresh: "Aktualisieren"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
  $.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
  required: "{0} ist notwendig",
  pattern: "{0} ist ungültig",
  min: "{0} muss größer oder gleich sein als {1}",
  max: "{0} muss kleiner oder gleich sein als {1}",
  step: "{0} ist ungültig",
  email: "{0} ist keine gültige E-Mail",
  url: "{0} ist keine gültige URL",
  date: "{0} ist kein gültiges Datum"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
  uploadFile: "Senden",
  orderBy: "Sortieren nach",
  orderByName: "Name",
  orderBySize: "Größe",
  directoryNotFound: "Das Verzeichnis wurde nicht gefunden.",
  emptyFolder: "Leeres Verzeichnis",
  deleteFile: 'Sind Sie sicher, dass Sie "{0}" wirklich löschen wollen?',
  invalidFileType: "Die ausgewählte Datei \"{0}\" ist ungültig. Unterstützte Dateitypen sind {1}.",
  overwriteFile: "Eine Datei namens \"{0}\" existiert bereits im aktuellen Ordner. Überschreiben?",
  dropFilesHere: "Dateien hier verschieben"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
  $.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
  bold: "Fett",
  italic: "Kursiv",
  underline: "Unterstrichen",
  strikethrough: "Durchgestrichen",
  superscript: "Hochgestellt",
  subscript: "Tiefgestellt",
  justifyCenter: "Zentrieren",
  justifyLeft: "Linksbündig",
  justifyRight: "Rechtsbündig",
  justifyFull: "Blocksatz",
  insertUnorderedList: "Unsortierte Liste einfügen",
  insertOrderedList: "Sortierte Liste einfügen",
  indent: "Einzug vergrößern",
  outdent: "Einzug verkleinern",
  createLink: "Link erstellen",
  unlink: "Link entfernen",
  insertImage: "Bild einfügen",
  insertHtml: "HTML einfügen",
  fontName: "Schriftart",
  fontNameInherit: "(Schriftart vererben)",
  fontSize: "Wählen Si die Schrifgröße",
  fontSizeInherit: "(Schriftgröße vererben)",
  formatBlock: "Format",
  foreColor: "Farbe",
  backColor: "Hintergrundfarbe",
  style: "Stil",
  emptyFolder: "Leeres Verzeichnis",
  uploadFile: "Senden",
  orderBy: "Sortieren nach:",
  orderBySize: "Größe",
  orderByName: "Name",
  invalidFileType: "Die ausgewählte Datei \"{0}\" ist ungültig. Unterstützte Dateitypen sind {1}.",
  deleteFile: 'Sind Sie sicher, dass Sie "{0}" wirklich löschen wollen?',
  overwriteFile: "Eine Datei namens \"{0}\" existiert bereits im aktuellen Ordner. Überschreiben?",
  directoryNotFound: "Das Verzeichnis wurde nicht gefunden.",
  imageWebAddress: "Internet-Adresse",
  imageAltText: "Alternativer Text",
  dialogInsert: "Einfügen",
  dialogButtonSeparator: "oder",
  dialogCancel: "Abbrechen"
 /***************************************************************************/   
});
