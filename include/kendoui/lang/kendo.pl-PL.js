/*
* Kendo UI Localization Project for v2012.3.1114 
* Copyright 2012 Telerik AD. All rights reserved.
* 
* Polski Poland (pl-PL) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Miroslaw Szajner  
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

kendo.ui.Locale = "Polski Poland (pl-PL)";
kendo.ui.ColumnMenu.prototype.options.messages = 
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

/* COLUMN MENU MESSAGES 
 ****************************************************************************/   
  sortAscending: "Rosnąco",
  sortDescending: "Malejąco",
  filter: "Filtruj",
  columns: "Kolumny"
 /***************************************************************************/   
});

kendo.ui.Groupable.prototype.options.messages = 
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

/* GRID GROUP PANEL MESSAGES 
 ****************************************************************************/   
  empty: "Przenieś nagłówek kolumny i upuść go tutaj w celu grupowania po tej kolumnie"
 /***************************************************************************/   
});

kendo.ui.FilterMenu.prototype.options.messages = 
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {
  
/* FILTER MENU MESSAGES 
 ***************************************************************************/   
  info: "Wybierz rekordy z wartością:",        // sets the text on top of the filter menu
	filter: "Filtruj",      // sets the text for the "Filter" button
	clear: "Wyczyść",        // sets the text for the "Clear" button
	// when filtering boolean numbers
	isTrue: "jest prawdziwe", // sets the text for "isTrue" radio button
	isFalse: "jest fałszywe",     // sets the text for "isFalse" radio button
	//changes the text of the "And" and "Or" of the filter menu
	and: "i",
	or: "lub",
    selectValue: "-- wybierz wartość --"
 /***************************************************************************/   
});
         
kendo.ui.FilterMenu.prototype.options.operators =           
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

/* FILTER MENU OPERATORS (for each supported data type) 
 ****************************************************************************/   
  string: {
      eq: "równą",
      neq: "różną od",
      startswith: "rozpoczynającą się od",
      contains: "zawierającą",
      doesnotcontain: "nie zawierającą",
      endswith: "kończącą się na"
  },
  number: {
	  eq: "równą",
      neq: "różną od",
      gte: "większą lub równą",
      gt: "większą od",
      lte: "mniejszą lub równą",
      lt: "mniejszą od"
  },
  date: {
	  eq: "równą",
      neq: "różną od",
      gte: "większą lub równą",
      gt: "większą od",
      lte: "mniejszą lub równą",
      lt: "mniejszą od"
  },
  enums: {
	  eq: "równą",
      neq: "różną od",
  }
 /***************************************************************************/   
});

kendo.ui.Pager.prototype.options.messages = 
  $.extend(kendo.ui.Pager.prototype.options.messages, {
  
/* PAGER MESSAGES 
 ****************************************************************************/   
  display: "{0} - {1} z {2} rekordów",
  empty: "Brak rekordów",
  page: "Strona",
  of: "z {0}",
  itemsPerPage: "rekordów na stronie",
  first: "Idź do pierwszej strony",
  previous: "Idź do poprzedniej strony",
  next: "Idź do następnej strony",
  last: "Idź do ostatniej strony",
  refresh: "Odśwież"
 /***************************************************************************/   
});

kendo.ui.Validator.prototype.options.messages = 
  $.extend(kendo.ui.Validator.prototype.options.messages, {

/* VALIDATOR MESSAGES 
 ****************************************************************************/   
  required: "{0} jest wymagane",
  pattern: "{0} jest nieprawidłowe",
  min: "{0} musi być większe lub równe {1}",
  max: "{0} musi być mniejsze lub równe {1}",
  step: "{0} jest nieważny",
  email: "{0} jest nieprawidłowym adresem e-mail",
  url: "{0} jest nieprawidłowym adresem URL",
  date: "{0} jest nieprawidłową datą"
 /***************************************************************************/   
});

kendo.ui.ImageBrowser.prototype.options.messages = 
	  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

/* IMAGE BROWSER MESSAGES 
 ****************************************************************************/   
  uploadFile: "Wyślij",
  orderBy: "Sortuj wg",
  orderByName: "Nazwy",
  orderBySize: "Rozmiaru",
  directoryNotFound: "Folder o podanej nazwie nie został odnaleziony.",
  emptyFolder: "Pusty folder",
  invalidFileType: "Wybrany plik \"{0}\" jest nieprawidłowy. Obsługiwane pliki {1}.",
  deleteFile: 'Czy napewno chcesz usunąć plik "{0}"?',
  overwriteFile: 'Plik o nazwie "{0}" już istnieje w bieżącym folderze. Czy zastąpić?',
  dropFilesHere: "umieść pliki tutaj, aby jest wysłać"
 /***************************************************************************/   
});

kendo.ui.Editor.prototype.options.messages = 
  $.extend(kendo.ui.Editor.prototype.options.messages, {

/* EDITOR MESSAGES 
 ****************************************************************************/   
  bold: "Pogrubienie",
  italic: "Kursywa",
  underline: "Podkreślenie",
  strikethrough: "Przekreślenie",
  superscript: "Indeks górny",
  subscript: "Indeks dolny",
  justifyCenter: "Wycentruj",
  justifyLeft: "Do lewej",
  justifyRight: "Do prawej",
  justifyFull: "Wyjustuj",
  insertUnorderedList: "Wstaw listę nienumerowaną",
  insertOrderedList: "Wstaw listę numerowaną",
  indent: "Zwiększ wcięcie",
  outdent: "Zmniejsz wcięcie",
  createLink: "Wstaw łącze",
  unlink: "Usuń łącze",
  insertImage: "Wstaw obraz",
  insertHtml: "Wstaw HTML",
  fontName: "Wybierz czcionkę",
  fontNameInherit: "(dziedzicz czcionkę)",
  fontSize: "Wybierz rozmiar",
  fontSizeInherit: "(dziedzicz rozmiar)",
  formatBlock: "Format",
  foreColor: "Kolor czcionki",
  backColor: "Kolor tła",
  style: "Styl",
  emptyFolder: "Pusty folder",
  uploadFile: "Prześlij",
  orderBy: "Sortuj wg:",
  orderBySize: "Rozmiaru",
  orderByName: "Nazwy",
  invalidFileType: "Wybrany plik \"{0}\" jest nieprawidłowy. Obsługiwane pliki {1}.",
  deleteFile: 'Czy napewno chcesz usunąć plik "{0}"?',
  overwriteFile: 'Plik o nazwie "{0}" już istnieje w bieżącym folderze. Czy zastąpić?',
  directoryNotFound: "Folder o podanej nazwie nie został odnaleziony.",
  imageWebAddress: "Adresy internetowe",
  imageAltText: "Tekst alternatywny",
  dialogInsert: "Wstaw",
  dialogButtonSeparator: "lub",
  dialogCancel: "Anuluj"
 /***************************************************************************/   
});
