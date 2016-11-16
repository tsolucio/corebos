/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Hungarian (hu-HU) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Janos Meszaros (Janez) 
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

kendo.ui.Locale = "Hungarian (hu-HU)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
  sortAscending: "Növekvő sorrend",
  sortDescending: "Csökkenő sorrend",
  filter: "Szűrő",
  columns: "Oszlopok"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
  empty: "Ragadja meg az oszlop címsorát és dobja ide a csoportosításhoz"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
  info: "Elemek ezzel az értékkel:", // sets the text on top of the filter menu
  isTrue: "igaz",                   // sets the text for "isTrue" radio button
  isFalse: "hamis",                 // sets the text for "isFalse" radio button
  filter: "Szűrés",                    // sets the text for the "Filter" button
  clear: "Alaphelyzet",                      // sets the text for the "Clear" button
  and: "És",
  or: "Vagy",
  selectValue: "-Válasszon-"
 /***************************************************************************/   
});
         
kendo.ui.FilterMenu.prototype.options.operators =           
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
  string: {
      eq: "megegyezik",
      neq: "ettől eltérő",
      startswith: "kezdve",
      contains: "tartalmazza",
      doesnotcontain: "nem tartalmazza",
      endswith: "vége"
  },
  number: {
      eq: "egyenlő",
      neq: "nem egyenlő",
      gte: "nagyobb vagy egyenlő",
      gt: "nagyobb",
      lte: "kisebb vagy egyenlő",
      lt: "kisebb"
  },
  date: {
      eq: "megegyezik",
      neq: "eltér",
      gte: "ekkor vagy utána",
      gt: "utána",
      lte: "elötte vagy ekkor",
      lt: "elötte"
  },
  enums: {
      eq: "megegyezik",
      neq: "elétrő"
  }
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
  $.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
  display: "{0} - {1} a {2} elemből",
  empty: "Nincs megjeleníthető elem.",
  page: "Oldal",
  of: "a {0}",
  itemsPerPage: "elem per oldal",
  first: "Ugrás az első oldalra",
  previous: "Ugrás az előző oldalra",
  next: "Ugrás a következő oldalra",
  last: "Ugrás az utolsó oldalra",
  refresh: "Frissítés"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
  $.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
  required: "A(z) {0} kitöltése kötelező",
  pattern: "A(z) {0} érvénytelen",
  min: "A(z) {0} nagyobbnak vagy egyenlőnek kell lennie, mint a {1}",
  max: "A(z) {0} kisebbnek vagy egyenlőnek kell lennie, mint a {1}",
  step: "A(z) {0} érvénytelen",
  email: "A(z) {0} nem valós email cím",
  url: "A(z) {0} nem valós URL cím",
  date: "A(z) {0} nem valós dátum"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
  uploadFile: "Feltöltés",
  orderBy: "Rendezés",
  orderByName: "Név",
  orderBySize: "Méret",
  directoryNotFound: "Könyvtár ezzel a névvel nem található.",
  emptyFolder: "Üres mappa",
  deleteFile: 'Biztosan törölni szeretné a következő állományt "{0}"?',
  invalidFileType: "A kiválaszott fájl \"{0}\" érvénytelen. Támogatott fájltípusok: {1}.",
  overwriteFile: "A fájl a megadott névvel \"{0}\" már létezik ebben a könyvtárban. Kívánja felülírni?",
  dropFilesHere: "Húzza ide az állományokat a feltöltéshez"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
  $.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
  bold: "Félkövér",
  italic: "Dőlt",
  underline: "Aláhúzott",
  strikethrough: "Áthúzott",
  superscript: "Felső index",
  subscript: "Alsó index",
  justifyCenter: "Szöveg igazítása középre",
  justifyLeft: "Szöveg igazítása balra",
  justifyRight: "Szöveg igazítása jobbra",
  justifyFull: "sorkizárt",
  insertUnorderedList: "Számozatlan lista beszúrása",
  insertOrderedList: "Számozott lista beszúrása",
  indent: "Behúzás",
  outdent: "Kihúzás",
  createLink: "Hivatkozás beszúrása",
  unlink: "Hivatkozás eltávolítása",
  insertImage: "Kép beszúrása",
  insertHtml: "HTML beszúrása",
  fontName: "betűtípus kiválasztása",
  fontNameInherit: "(Örökölt betűtípus)",
  fontSize: "Betűméret kiválasztása",
  fontSizeInherit: "(Örökölt méret)",
  formatBlock: "Formátum",
  foreColor: "Szín",
  backColor: "Héttérszín",
  style: "Stílusok",
  emptyFolder: "Üres mappa",
  uploadFile: "Feltöltés",
  orderBy: "Rendezés:",
  orderBySize: "Méret",
  orderByName: "Név",
  invalidFileType: "A kiválasztott fájl \"{0}\" nem megjelelő típus. Támogatott típusok: {1}.",
  deleteFile: 'Biztosan törölni szeretné a következő állományt "{0}"?',
  overwriteFile: 'A fájl a megadott névvel "{0}" már létezik ebben a könyvtárban. Kívánja felülírni?',
  directoryNotFound: "A megadott könyvtár nem létezik.",
  imageWebAddress: "Web cím",
  imageAltText: "Alternatív szöveg",
  dialogInsert: "Beszúrás",
  dialogButtonSeparator: "Vagy",
  dialogCancel: "Mégse"
 /***************************************************************************/   
});
