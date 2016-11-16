/*
* Kendo UI Localization Project for v2012.3.1114
* Copyright 2012 Telerik AD. All rights reserved.
*
* Slovak SK (sk-SK) Language Pack
*
* Project home : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author : Katarina Polakova
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

kendo.ui.Locale = "Slovak SK (sk-SK)";
kendo.ui.ColumnMenu.prototype.options.messages =
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

      /* COLUMN MENU MESSAGES
      ****************************************************************************/
      sortAscending: "Zoradiť vzostupne",
      sortDescending: "Zoradiť zostupne",
      filter: "Filter",
      columns: "Stĺpce"
      /***************************************************************************/
  });

kendo.ui.Groupable.prototype.options.messages =
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

      /* GRID GROUP PANEL MESSAGES
      ****************************************************************************/
      empty: "Chyťte hlavičku stĺpca a presuňte ho sem pre zoskupenie podľa tohoto stĺpca"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.messages =
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {

      /* FILTER MENU MESSAGES
      ***************************************************************************/
      info: "Zobraziť položky s hodnotou, ktoré:", // sets the text on top of the filter menu
      isTrue: "je pravda", // sets the text for "isTrue" radio button
      isFalse: "nie je pravda", // sets the text for "isFalse" radio button
      filter: "Filtruj", // sets the text for the "Filter" button
      clear: "Vymazať", // sets the text for the "Clear" button
      and: "A",
      or: "Alebo",
      selectValue: "-Vyber hodnotu-"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.operators =
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

      /* FILTER MENU OPERATORS (for each supported data type)
      ****************************************************************************/
      string: {
          eq: "Je rovné",
          neq: "Nie je rovné",
          startswith: "Začína s",
          contains: "Obsahuje",
          doesnotcontain: "Neobsahuje",
          endswith: "Končí s"
      },
      number: {
          eq: "Je rovné",
          neq: "Nie je rovné",
          gte: "Je väčšie alebo rovné",
          gt: "Je väčšie",
          lte: "Je menšie alebo rovné",
          lt: "Je menšie"
      },
      date: {
          eq: "Je rovný",
          neq: "Nie je rovný",
          gte: "Je väčší alebo rovný",
          gt: "Je väčší",
          lte: "Je menší alebo rovný",
          lt: "Je menší"
      },
      enums: {
          eq: "Je rovný",
          neq: "Nie je rovný"
      }
      /***************************************************************************/
  });

kendo.ui.Pager.prototype.options.messages =
  $.extend(kendo.ui.Pager.prototype.options.messages, {

      /* PAGER MESSAGES
      ****************************************************************************/
      display: "{0} - {1} z {2} položiek",
      empty: "Žiadne položky na zobrazenie",
      page: "Strana",
      of: "z {0}",
      itemsPerPage: "položiek na stránku",
      first: "Choď na prvú stránku",
      previous: "Choď na predchádzajúcu stránku",
      next: "Choď na ďalšiu stránku",
      last: "Choď na poslednú stránku",
      refresh: "Obnoviť"
      /***************************************************************************/
  });

kendo.ui.Validator.prototype.options.messages =
  $.extend(kendo.ui.Validator.prototype.options.messages, {

      /* VALIDATOR MESSAGES
      ****************************************************************************/
      required: "{0} je povinný",
      pattern: "{0} nie je platný",
      min: "{0} má byť väčší alebo rovný {1}",
      max: "{0} má byť menší alebo rovný {1}",
      step: "{0} nie je platný",
      email: "{0} nie je platný email",
      url: "{0} nie je platná URL adresa",
      date: "{0} nie je platný dátum"
      /***************************************************************************/
  });

kendo.ui.ImageBrowser.prototype.options.messages =
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

      /* IMAGE BROWSER MESSAGES
      ****************************************************************************/
      uploadFile: "Vlož súbor",
      orderBy: "Usporiadaj podľa",
      orderByName: "Meno",
      orderBySize: "Veľkosť",
      directoryNotFound: "Adresár s týmto menom nebol nájdený.",
      emptyFolder: "Prázdny adresár",
      deleteFile: 'Ste si istý, že chcete vymazať "{0}"?',
      invalidFileType: "Vybraný súbor \"{0}\" nie je platný. Podporované typy súborov sú {1}.",
      overwriteFile: "Súbor s menom \"{0}\" už v adresári existuje. Chcete ho prepísať?",
      dropFilesHere: "pustite súbory sem pre vloženie"
      /***************************************************************************/
  });

kendo.ui.Editor.prototype.options.messages =
  $.extend(kendo.ui.Editor.prototype.options.messages, {

      /* EDITOR MESSAGES
      ****************************************************************************/
      bold: "Tučný",
      italic: "Kurzíva",
      underline: "Podčiarknutý",
      strikethrough: "Prečiarknutý",
      superscript: "Horný index",
      subscript: "Dolný index",
      justifyCenter: "Zarovnať na stred",
      justifyLeft: "Zarovnať doľava",
      justifyRight: "Zarovnať doprava",
      justifyFull: "Zarovnať do bloku",
      insertUnorderedList: "Vložiť neusporiadaný text",
      insertOrderedList: "Vložiť usporiadaný text",
      indent: "Indent",
      outdent: "Outdent",
      createLink: "Vložiť hyperlink",
      unlink: "Odstrániť hyperlink",
      insertImage: "Vložiť obrázok",
      insertHtml: "Vložiť HTML",
      fontName: "Vybrať font family",
      fontNameInherit: "(zdedený font)",
      fontSize: "Vyberte veľkosť fontu",
      fontSizeInherit: "(zdedená veľkosť)",
      formatBlock: "Formát",
      foreColor: "Farba",
      backColor: "Farba pozadia",
      style: "Štýly",
      emptyFolder: "Prázdny adresár",
      uploadFile: "Vložiť",
      orderBy: "Usporiadať podľa:",
      orderBySize: "Veľkosť",
      orderByName: "Meno",
      invalidFileType: "Vybraný súbor \"{0}\" nie je platný. Podporované typy súborov sú {1}.",
      deleteFile: 'Ste si istý, že chcete vymazať "{0}"?',
      overwriteFile: 'Súbor s menom \"{0}\" už v adresári existuje. Chcete ho prepísať?',
      directoryNotFound: "Adresár s týmto menom nebol nájdený.",
      imageWebAddress: "Webová adresa",
      imageAltText: "Alternatívny text",
      dialogInsert: "Vložiť",
      dialogButtonSeparator: "alebo",
      dialogCancel: "Zrušiť"
      /***************************************************************************/
  });

kendo.ui.NumericTextBox.prototype.options =
    $.extend(kendo.ui.NumericTextBox.prototype.options, {

        /* NUMERIC TEXT BOX OR INTEGER TEXT BOX MESSAGES
        ****************************************************************************/
        upArrowText: "Inkrementuj hodnotu",
        downArrowText: "Dekrementuj hodnotu"
        /***************************************************************************/
    });

kendo.ui.Upload.prototype.options.localization =
    $.extend(kendo.ui.Upload.prototype.options.localization, {

        /* BUTTON IN UPLOAD MESSAGES
        ****************************************************************************/
        select: "Výber...",
        remove: "Odstrániť",
        removeAll: "Odstrániť všetko"
        /***************************************************************************/
    });
